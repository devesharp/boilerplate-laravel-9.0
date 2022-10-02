<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class UpdateUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        $this->extendRules(CreateUsersDto::class);
        $this->disableRequiredValues();

        return [
            'password' => null,
        ];
    }
}
