<?php

namespace App\Modules\Users\Docs;

use Devesharp\SwaggerGenerator\RoutesDocAbstract;
use Devesharp\SwaggerGenerator\RoutesDocInfo;

class UsersAuthRouteDoc extends RoutesDocAbstract
{
    public function getRouteInfo(string $name): RoutesDocInfo {
        return match ($name) {
            'Login' => new RoutesDocInfo("Login", ""),
            'AuthCheck' => new RoutesDocInfo("Verificação de AccessToken", ""),
            'PasswordReset' => new RoutesDocInfo("Recuperar senha", ""),
            'VerifyRememberToken' => new RoutesDocInfo("Verificação de token de recuperação de senha", ""),
            'Logout' => new RoutesDocInfo("Logout", ""),
            default => new RoutesDocInfo("", ""),
        };
    }
}
