<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;

class ResetPasswordUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            'remember_token' => new Rule('required|string', 'Token para recuperação de senha'),
            'new_password' => new Rule('required|string|max:100', 'Nova senha'),
        ];
    }
}
