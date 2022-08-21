<?php

namespace App\Modules\Users\Transformers;

use Devesharp\Patterns\Transformer\Transformer;
use \App\Modules\Users\Models\Users;

class UsersTransformer extends Transformer
{
    public string $model = Users::class;

    protected array $loads = [];

    /**
     * @param $model
     * @param string $context
     * @param null $requester
     * @return mixed
     * @throws \Exception
     */
    public function transformDefault(
        $model,
        $requester = null
    ) {
        if (! $model instanceof $this->model) {
            throw new \Exception('invalid model transform');
        }

        $transform = [];

        $transform['id'] = (string) $model->id;
        $transform['name'] = (string) $model->name;
        $transform['role'] = (string) $model->role;
        $transform['login'] = (string) $model->login;
        $transform['email'] = (string) $model->email;
        $transform['document'] = (string) $model->document;
        $transform['CPF'] = (string) $model->CPF;
        $transform['image'] = (string) $model->image;
        $transform['updated_at'] = (string) $model->updated_at;
        $transform['created_at'] = (string) $model->created_at;

        return $transform;
    }
}
