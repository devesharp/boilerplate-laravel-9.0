<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;

class LoginUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "login" => new Rule("string|required", "Login ou email do usuário"),
            "password" => new Rule("string|required", "Senha do usuário"),
        ];
    }
}
