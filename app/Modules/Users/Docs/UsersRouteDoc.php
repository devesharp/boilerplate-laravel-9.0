<?php

namespace App\Modules\Users\Docs;

use Devesharp\SwaggerGenerator\RoutesDocAbstract;
use Devesharp\SwaggerGenerator\RoutesDocInfo;

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
            'UploadAvatar' => new RoutesDocInfo("Upload de avatar", ""),
            'UploadAvatarMe' => new RoutesDocInfo("Upload de avatar usuário atual", ""),
            default => new RoutesDocInfo("", ""),
        };
    }
}
