<?php

namespace App\Modules\Users\Repositories;

use Devesharp\Patterns\Repository\RepositoryMysql;

/**
 * Class UsersRepository
 *
 * @method public                                      Builder getModel()
 * @method \App\Modules\Users\Models\Users findById($id, $enabled = true)
 * @method \App\Modules\Users\Models\Users findIdOrFail($id, $enabled = true)
 */
class UsersRepository extends RepositoryMysql
{
    /**
     * @var string
     */
    protected $model = \App\Modules\Users\Models\Users::class;
}
