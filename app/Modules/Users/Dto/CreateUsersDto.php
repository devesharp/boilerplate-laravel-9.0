<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;

class CreateUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "name" => new Rule("string|max:200|required", "Nome do usuário"),
            "login" => new Rule("string|max:200|required", "Login do usuário"),
            "email" => new Rule("string|max:200|required", "Email do usuário"),
            "password" => new Rule("string|max:100|required", "Senha do usuário"),
            "document" => new Rule("string|max:100", "RG do usuário"),
            "CPF" => new Rule("string|max:14", "CPF do usuário"),
        ];
    }
}
