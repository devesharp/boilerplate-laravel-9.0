<?php

namespace App\Modules\Users\Docs;

use Devesharp\APIDocs\RoutesDocAbstract;
use Devesharp\APIDocs\RoutesDocInfo;

class UsersRouteDoc extends RoutesDocAbstract
{
    public function getRouteInfo(string $name): RoutesDocInfo {
        return match ($name) {
            "CreateUsers" => new RoutesDocInfo("Criar Usuários", ""),
            "UpdateUsers" => new RoutesDocInfo("Atualizar Usuários", ""),
            "GetUsers" => new RoutesDocInfo("Resgatar Usuários", ""),
            "SearchUsers" => new RoutesDocInfo("Buscar Usuários", ""),
            "DeleteUsers" => new RoutesDocInfo("Deletar Usuários", ""),
            'UpdateUsersMe' => new RoutesDocInfo("Atualizar usuário atual", ""),
            'GetUsersMe' => new RoutesDocInfo("Resgatar usuário atual", ""),
            'ChangePasswordUsers' => new RoutesDocInfo("Mudar senha", ""),
            default => new RoutesDocInfo("", ""),
        };
    }
}
