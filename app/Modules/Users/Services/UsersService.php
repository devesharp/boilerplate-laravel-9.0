<?php

namespace App\Modules\Users\Services;

use Devesharp\Patterns\Service\Service;
use Devesharp\Patterns\Service\ServiceFilterEnum;
use Devesharp\Patterns\Transformer\Transformer;
use Devesharp\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        protected \App\Modules\Users\Validators\UsersValidator $validator,
        protected \App\Modules\Users\Transformers\UsersTransformer $transformer,
        protected \App\Modules\Users\Repositories\UsersRepository $repository,
        protected \App\Modules\Users\Policies\UsersPolicy $policy
    ) {
    }

    /**
     * Create resource
     *
     * @param array $originalData
     * @param null $requester
     * @return mixed
     * @throws \Exception
     */
    public function create(array $originalData, $requester = null, $context = 'model')
    {
        try {

            // Authorization
            $this->policy->create($requester);

            // Data validation
            $data = $this->validator->create($originalData, $requester);

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
     * @param int $id
     * @param array $originalData
     * @param null $requester
     * @return mixed
     * @throws \Exception
     */
    public function update(
        int $id,
        array $originalData,
        $requester = null,
        $context = 'model'
    ) {
        try {
            $model = $this->repository->findIdOrFail($id);

            // Authorization
            $this->policy->update($requester, $model);

            // Iniciar transação
            DB::beginTransaction();

            // Data validation
            $data = $this->validator->update($originalData, $requester);

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
     * @param Collection $requestData
     * @param $currentModel
     * @param string $method
     * @return Collection
     */
    public function treatment(
        $requester,
        Collection $requestData,
        $currentModel,
        string $method
    ) {
        if ($method == 'update') {
            return $requestData;
        } else if ($method == 'create') {
            return $requestData;
        }

        return $requestData;
    }

    /**
     * @param int $id
     * @param $receiver
     * @param string $context
     * @return mixed
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

        if ($context != 'model')
            $this->policy->get($receiver, $model);

        return Transformer::item(
            $model,
            $this->transformer,
            $context,
            $receiver,
        );
    }

    /**
     * @param array $originalData
     * @param null $requester
     * @return array
     */
    public function search(array $originalData = [], $requester = null)
    {
        // Authorization
        $this->policy->search($requester);

        // Validate data
        $data = $this->validator->search($originalData, $requester);

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
     * @param null $requester
     * @return \Devesharp\Pattners\Repository\RepositoryInterface|\App\Modules\Users\Repositories\UsersRepository
     */
    protected function makeSearch(&$data, $requester = null)
    {
        /** @var \App\Modules\Users\Repositories\UsersRepository $query */
        $query = parent::makeSearch($data, $requester);

//        // Example Query
//        $query->whereInt('id', 1);

        return $query;
    }

    /**
     * @param $id
     * @param $requester
     * @return bool
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
}
