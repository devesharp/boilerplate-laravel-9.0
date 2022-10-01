<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;
use Devesharp\Patterns\Dto\Templates\ActionManyTemplateDto;

class DeleteUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        $this->extendRules(ActionManyTemplateDto::class);

        return [
            'filters.name' => new Rule('string|max:100|required', 'Nome'),
            'filters.age' => new Rule('numeric|required', 'Idade'),
            'filters.active' => new Rule('boolean', 'Ativo'),
        ];
    }
}
