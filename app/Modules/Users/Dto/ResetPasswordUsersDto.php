<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class ResetPasswordUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            'remember_token' => ['required|string', 'Token para recuperação de senha'],
            'new_password' => ['required|string|max:100', 'Nova senha'],
        ];
    }
}
