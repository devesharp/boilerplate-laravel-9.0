<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;

class UploadAvatarDtoUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            'file' => new Rule('file', 'Arquivo'),
        ];
    }
}
