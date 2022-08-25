<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class VerifyRememberPasswordUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            'remember_token' => ['string', 'Token para recuperação de senha'],
        ];
    }
}
