<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;

class ChangePasswordDtoUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        return [
            "old_password" => ["string|required", "Senha antiga"],
            "new_password" => ["string|required", "Nova senha"],
        ];
    }
}
