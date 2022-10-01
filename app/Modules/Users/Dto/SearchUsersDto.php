<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Rule;
use Devesharp\Patterns\Dto\Templates\SearchTemplateDto;

class SearchUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        $this->extendRules(SearchTemplateDto::class);

        return [
            'filters.id' => new Rule('numeric', 'Id do usuário'),
            'filters.name' => new Rule('string', 'Nome do usuário'),
            'filters.no_get_me' => new Rule('boolean', 'Não retornar meu usuário'),
        ];
    }
}
