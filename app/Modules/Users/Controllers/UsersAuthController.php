<?php

namespace App\Modules\Users\Controllers;

use App\Modules\Users\Dto\ForgetPasswordUsersDto;
use App\Modules\Users\Dto\LoginUsersDto;
use App\Modules\Users\Dto\LogoutUsersDto;
use App\Modules\Users\Dto\ResetPasswordUsersDto;
use App\Modules\Users\Dto\VerifyRememberPasswordUsersDto;
use App\Modules\Users\Services\UsersAuthService;
use App\Modules\Users\Services\UsersService;
use Devesharp\Patterns\Controller\ControllerBase;

class UsersAuthController extends ControllerBase
{
    public function __construct(
        protected UsersAuthService $usersAuthService,
        protected UsersService $usersService
    ) {
        parent::__construct();
    }

//    public function register()
//    {
//        return $this->usersService->register(\request()->all());
//    }

    public function login()
    {
        $user = $this->usersAuthService->login(LoginUsersDto::make(\request()->all()));

        return [
            'user' => $user,
            'info' => [],
        ];
    }

    public function check()
    {
        $user = $this->usersService->get($this->auth->id, $this->auth);

        return [
            'user' => $user,
            'info' => [],
        ];
    }

    public function passwordRecover()
    {
        return $this->usersAuthService->forgetPassword(ForgetPasswordUsersDto::make(\request()->all()));
    }

    public function changePasswordByToken()
    {
        return $this->usersAuthService->changePasswordByToken(ResetPasswordUsersDto::make(request()->all()));
    }

    public function verifyRememberPassword()
    {
        return $this->usersAuthService->verifyRememberPassword(VerifyRememberPasswordUsersDto::make(request()->all()));
    }

    public function logout()
    {
        return $this->usersAuthService->logout(LogoutUsersDto::make(request()->all()), $this->auth);
    }

    public function me()
    {
        return $this->usersAuthService->me();
    }

    public function refresh()
    {
        return $this->usersAuthService->refresh();
    }
}
