<?php

namespace App\Modules\Users\Controllers;

use App\Modules\Users\Dto\ChangePasswordDtoUsersDto;
use App\Modules\Users\Dto\CreateUsersDto;
use App\Modules\Users\Dto\SearchUsersDto;
use App\Modules\Users\Dto\UpdateUsersDto;
use App\Modules\Users\Dto\UploadAvatarDtoUsersDto;
use Devesharp\Patterns\Controller\ControllerBase;

class UsersController extends ControllerBase
{
    public function __construct(
        protected \App\Modules\Users\Services\UsersService $service
    ) {
        parent::__construct();
    }

    public function search()
    {
        return $this->service->search(SearchUsersDto::make(request()->all()), $this->auth, 'default');
    }

    public function get($id)
    {
        return $this->service->get($id, $this->auth);
    }

    public function update($id)
    {
        return $this->service->update($id, UpdateUsersDto::make(request()->all()), $this->auth, 'default');
    }

    public function getSelf()
    {
        return $this->service->get($this->auth->id, $this->auth);
    }

    public function updateSelf()
    {
        return $this->service->update($this->auth->id, UpdateUsersDto::make(request()->all()), $this->auth, 'default');
    }

    public function create()
    {
        return $this->service->create(CreateUsersDto::make(request()->all()), $this->auth, 'default');
    }

    public function changePassword()
    {
        return $this->service->changePassword(ChangePasswordDtoUsersDto::make(request()->all()), $this->auth, 'default');
    }

    public function uploadAvatar($id)
    {
        return $this->service->uploadAvatar($id, UploadAvatarDtoUsersDto::make(request()->all()), $this->auth, 'default');
    }

    public function uploadAvatarMe()
    {
        return $this->service->uploadAvatar2($this->auth->id, UploadAvatarDtoUsersDto::make(request()->all()), $this->auth, 'default');
    }

    public function delete($id)
    {
        return $this->service->delete($id, $this->auth);
    }
}
