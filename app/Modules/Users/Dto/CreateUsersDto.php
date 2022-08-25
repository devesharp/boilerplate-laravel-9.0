<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class CreateUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "name" => ["string|max:200|required", "Nome do usuário"],
            "login" => ["string|max:200|required", "Login do usuário"],
            "email" => ["string|max:200|required", "Email do usuário"],
            "password" => ["string|max:100|required", "Senha do usuário"],
            "document" => ["string|max:100", "RG do usuário"],
            "CPF" => ["string|max:14", "CPF do usuário"],
//            "permissions" => "array",
        ];
    }
}
