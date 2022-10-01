<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;

class TokenVerifyUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "access_token" => new Rule("string", "Acess token")
        ];
    }
}
