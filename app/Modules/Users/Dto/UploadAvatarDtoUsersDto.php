<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class UploadAvatarDtoUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "file" => ["file", "Arquivo"],
        ];
    }
}
