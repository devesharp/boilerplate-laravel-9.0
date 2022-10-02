<?php

namespace App\Modules\Users\Transformers;

use App\Modules\Users\Models\Users;
use App\Supports\Formatters\CPFFormatter;
use App\Supports\Formatters\DateTimeBrFormatter;
use App\Supports\Formatters\RGFormatter;
use Devesharp\Patterns\Transformer\Transformer;

class UsersTransformer extends Transformer
{
    public string $model = Users::class;

    protected array $loads = [];

    /**
     * @param $model
     * @param  string  $context
     * @param  null  $requester
     * @return mixed
     *
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
        $transform['document'] = format(RGFormatter::class, (string) $model->document);
        $transform['CPF'] = format(CPFFormatter::class, $model->CPF);
        $transform['image'] = (string) $model->image;
        $transform['updated_at'] = format(DateTimeBrFormatter::class, $model->updated_at);
        $transform['created_at'] = format(DateTimeBrFormatter::class, $model->created_at);

        if (! empty($model->access_token)) {
            $transform['access_token'] = $model->access_token;
        }

        return $transform;
    }
}
