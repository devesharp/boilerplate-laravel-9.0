<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class TokenVerifyUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "access_token" => ["string", "Acess token"]
        ];
    }
}
