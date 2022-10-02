<?php

namespace App\Modules\Users\Services;

use App\Exceptions\Exception;
use App\Modules\Uploads\Services\UploadsAWSService;
use App\Modules\Users\Dto\ChangePasswordDtoUsersDto;
use App\Modules\Users\Dto\CreateUsersDto;
use App\Modules\Users\Dto\SearchUsersDto;
use App\Modules\Users\Dto\UpdateUsersDto;
use App\Modules\Users\Dto\UploadAvatarDtoUsersDto;
use Devesharp\Patterns\Service\Service;
use Devesharp\Patterns\Service\ServiceFilterEnum;
use Devesharp\Patterns\Transformer\Transformer;
use Devesharp\Support\Collection;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersService extends Service
{
    /**
     * Sorts permitidas.
     */
    public array $sort = [
        'id' => [
            'column' => 'id',
        ],
    ];

    /**
     * @var string Sort padrão
     */
    public string $sort_default = '-id';

    /**
     * @var int limit de resultados
     */
    public int $limitMax = 20;

    /**
     * @var int limit padrão
     */
    public int $limitDefault = 20;

    /**
     * @var array Filtros rápidos
     */
    public array $filters = [
        // Filter default
        'id' => [
            'column' => 'id',
            'filter' => ServiceFilterEnum::whereInt,
        ],
        'name' => [
            'column' => 'name',
            'filter' => ServiceFilterEnum::whereContainsLike,
        ],
        // Filter column raw
        'full_name' => [
            'column' => "raw:(name || ' ' || age)",
            'filter' => ServiceFilterEnum::whereContainsExplodeString,
        ],
    ];

    public function __construct(
        protected \App\Modules\Users\Transformers\UsersTransformer $transformer,
        protected \App\Modules\Users\Repositories\UsersRepository $repository,
        protected \App\Modules\Uploads\Repositories\S3FilesRepository $s3FilesRepository,
        protected \App\Modules\Users\Policies\UsersPolicy $policy
    ) {
    }

    /**
     * Create resource
     *
     * @param  CreateUsersDto  $data
     * @param  null  $requester
     * @return mixed
     *
     * @throws \Exception
     */
    public function create(CreateUsersDto $data, $requester = null, $context = 'model')
    {
        try {
            // Authorization
            $this->policy->create($requester);

            // Iniciar transação
            DB::beginTransaction();

            // Treatment data
            $resourceData = $this->treatment($requester, $data, null, 'create');

            // Create Model
            $model = $this->repository->create($resourceData->toArray());

            DB::commit();

            return $this->get($model->id, $requester, $context);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  int  $id
     * @param  UpdateUsersDto  $originalData
     * @param  null  $requester
     * @return mixed
     *
     * @throws \Exception
     */
    public function update(
        int $id,
        UpdateUsersDto $data,
        $requester = null,
        $context = 'model'
    ) {
        try {
            $model = $this->repository->findIdOrFail($id);

            // Authorization
            $this->policy->update($requester, $model);

            // Iniciar transação
            DB::beginTransaction();

            // Treatment data
            $resourceData = $this->treatment($requester, $data, $model, 'update');

            // Update Model
            $this->repository->updateById($id, $resourceData->toArray());

            DB::commit();

            return $this->get($id, $requester, $context);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $requester
     * @param  Collection  $requestData
     * @param $currentModel
     * @param  string  $method
     * @return Collection
     */
    public function treatment($requester, Collection $requestData, $currentModel, string $method)
    {
        if ($method == 'update') {
            return $requestData;
        } elseif ($method == 'create') {
            return $requestData;
        }

        return $requestData;
    }

    /**
     * @param  int  $id
     * @param $receiver
     * @param  string  $context
     * @return mixed
     *
     * @throws \Devesharp\Exceptions\Exception
     */
    public function get(int $id, $receiver, string $context = 'default')
    {
        // Get model
        $model = $this->makeSearch($data, $receiver)
            ->whereInt('id', $id)
            ->findOne();

        if (empty($model)) {
            \Devesharp\Exceptions\Exception::NotFound(\App\Modules\Users\Models\Users::class);
        }

        if ($context != 'model') {
            $this->policy->get($receiver, $model);
        }

        return Transformer::item(
            $model,
            $this->transformer,
            $context,
            $receiver,
        );
    }

    /**
     * @param  SearchUsersDto  $originalData
     * @param  null  $requester
     * @return array
     */
    public function search(SearchUsersDto $data, $requester = null)
    {
        // Authorization
        $this->policy->search($requester);

        // Make query
        $query = $this->makeSearch($data, $requester);

        return $this->transformerSearch(
            $query,
            $this->transformer,
            'default',
            $requester,
        );
    }

    /**
     * @param $data
     * @param  null  $requester
     * @return \Devesharp\Pattners\Repository\RepositoryInterface|\App\Modules\Users\Repositories\UsersRepository
     */
    protected function makeSearch(&$data, $requester = null)
    {
        /** @var \App\Modules\Users\Repositories\UsersRepository $query */
        $query = parent::makeSearch($data, $requester);

        return $query;
    }

    /**
     * @param $id
     * @param $requester
     * @return array
     *
     * @throws \Devesharp\Exceptions\Exception
     */
    public function delete($id, $requester = null)
    {
        try {
            $model = $this->repository->findIdOrFail($id);

            // Authorization
            $this->policy->delete($requester, $model);

            // Iniciar transação
            DB::beginTransaction();

            $this->repository->updateById($id, ['enabled' => false]);

            DB::commit();

            return [
                'id' => $id,
                'deleted' => true,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Trocar senha
     *
     * @param  ChangePasswordDtoUsersDto  $originalData
     * @param $user
     * @return bool[]
     */
    public function changePassword(ChangePasswordDtoUsersDto $data, $user)
    {
        if (! Hash::check($data['old_password'], $user->password)) {
            Exception::Exception(\App\Exceptions\Exception::PASSWORD_INCORRECT);
        }

        $this->repository->clearQuery()->updateById($user->id, [
            'password' => Hash::make($data['new_password']),
        ]);

        return [
            'changed' => true,
        ];
    }

    /**
     * Atualizar avatar do usuário
     *
     * @param  UploadAvatarDtoUsersDto  $data
     * @param $user
     * @return bool[]
     *
     * @throws \Devesharp\Exceptions\Exception
     */
    public function uploadAvatar($id, UploadAvatarDtoUsersDto $data, $requester)
    {
        $model = $this->repository->findIdOrFail($id);

        // Authorization
        $this->policy->update($requester, $model);

        /** @var File $file */
        $file = $data->file;
        $name = base64_encode($model->id.'-'.sha1($model->id));
        $filePath = 'avatar/'.$name.'.png';

        $response = app(UploadsAWSService::class)->uploadPublicFile($filePath, $file, $file->getMimeType());

        $this->repository->clearQuery()->updateById($model->id, [
            'image' => $response['key'],
        ]);

        $this->s3FilesRepository->clearQuery()->create([
            'user_id' => $requester->id,
            'path' => $response['key'],
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return $response;
    }
}
