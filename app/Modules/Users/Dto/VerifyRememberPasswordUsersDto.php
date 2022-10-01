<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;

class VerifyRememberPasswordUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            'remember_token' => new Rule('string', 'Token para recuperação de senha'),
        ];
    }
}
