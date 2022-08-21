<?php

namespace App\Modules\Users\Presenters;

use Devesharp\Patterns\Presenter\Presenter;

class UsersPresenter extends Presenter {

    public function fullName()
    {
        return $this->name;
    }
}
