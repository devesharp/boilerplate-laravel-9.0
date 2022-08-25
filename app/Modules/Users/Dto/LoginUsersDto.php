<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class LoginUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "login" => ["string|required", "Login ou email do usuário"],
            "password" => ["string|required", "Senha do usuário"]
        ];
    }
}
