<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class ForgetPasswordUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "login" => ["string", "Login ou email do usuário"],
        ];
    }
}
