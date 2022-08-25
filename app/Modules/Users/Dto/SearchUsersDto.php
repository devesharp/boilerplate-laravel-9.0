<?php

namespace App\Modules\Users\Dto;

use Devesharp\Patterns\Dto\AbstractDto;
use Devesharp\Patterns\Dto\Templates\SearchTemplateDto;

class SearchUsersDto extends AbstractDto
{
    protected function configureValidatorRules(): array
    {
        $this->extendRules(SearchTemplateDto::class);

        return [
            'filters.id' => ['numeric', 'Id do usuário'],
            'filters.name' => ['string', 'Nome do usuário'],
            'filters.no_get_me' => ['boolean', 'Não retornar meu usuário'],
        ];
    }
}
