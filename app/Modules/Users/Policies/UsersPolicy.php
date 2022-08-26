<?php

namespace App\Modules\Users\Policies;

use App\Modules\Users\Interfaces\UsersPermissions;

class UsersPolicy
{
    function create($requester) {
        if (!$requester->can(UsersPermissions::USERS_CREATE)) {
            \Devesharp\Exceptions\Exception::Unauthorized();
        }
    }

    function update($requester, $model) {
        if (!$requester->can(UsersPermissions::USERS_UPDATE) && $requester->id !== $model->id) {
            \Devesharp\Exceptions\Exception::Unauthorized();
        }
    }

    function get($requester, $model) {
        if (!$requester->can(UsersPermissions::USERS_VIEW) && $requester->id !== $model->id) {
            \Devesharp\Exceptions\Exception::Unauthorized();
        }
    }

    function search($requester) {
        if (!$requester->can(UsersPermissions::USERS_SEARCH)) {
            \Devesharp\Exceptions\Exception::Unauthorized();
        }
    }

    function delete($requester, $model) {
        if (!$requester->can(UsersPermissions::USERS_DELETE)) {
            \Devesharp\Exceptions\Exception::Unauthorized();
        }
    }
}
