<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;

class ForgetPasswordUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            'login' => new Rule('string', 'Login ou email do usuário'),
        ];
    }
}
