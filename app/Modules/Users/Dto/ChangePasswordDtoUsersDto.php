<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;

class ChangePasswordDtoUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            'old_password' => new Rule('string|required', 'Senha antiga'),
            'new_password' => new Rule('string|required', 'Nova senha'),
        ];
    }
}
