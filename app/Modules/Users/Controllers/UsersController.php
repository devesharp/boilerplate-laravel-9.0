<?php

namespace App\Modules\Users\Controllers;

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
        return $this->service->search(request()->all(), $this->auth, 'default');
    }

    public function get($id)
    {
        return $this->service->get($id, $this->auth);
    }

    public function update($id)
    {
        return $this->service->update($id, request()->all(), $this->auth, 'default');
    }

    public function create()
    {
        return $this->service->create(request()->all(), $this->auth, 'default');
    }

    public function delete($id)
    {
        return $this->service->delete($id, $this->auth);
    }
}
