<?php

namespace App\Modules\Users\Repositories;

use Devesharp\Patterns\Repository\RepositoryMysql;
use Illuminate\Support\Carbon;

/**
 * Class UsersAccessTokensRepository
 *
 * @method public                                      Builder getModel()
 * @method \App\Modules\Users\Models\UsersAccessTokens findById($id, $enabled = true)
 * @method \App\Modules\UsersAccessTokens\Models\Users findIdOrFail($id, $enabled = true)
 */
class UsersAccessTokensRepository extends RepositoryMysql
{
    /**
     * @var string
     */
    protected $model = \App\Modules\Users\Models\UsersAccessTokens::class;

    function deleteByToken($token)
    {
        return $this->clearQuery()->whereSameString('token', $token)->update(['enabled' => false, 'logout_at' => Carbon::now()]);
    }
}
