<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Templates\SearchTemplateDto;

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
