<?php

namespace App\Modules\Users\Validators;

use Devesharp\Patterns\Validator\Validator;

class UsersValidator extends Validator
{
    use \Devesharp\Patterns\Validator\ValidatorAPIGenerator;

    protected array $rules = [
        'create' => [
            "name" => ["string|max:100|required", "Nome do usuário"],
            "login" => ["string|max:100|required", "Login do usuário"],
            "email" => ["string|max:100|required", "Email do usuário"],
            "password" => ["string|max:100|required", "Senha do usuário"],
            "document" => ["string|max:100", "RG do usuário"],
            "CPF" => ["string|max:100", "CPF do usuário"],
//            "permissions" => "array",
        ],
        'update' => [
            '_extends' => 'create',
        ],
        // Busca
        'search' => [
            'filters.id' => ['numeric', 'Id do usuário'],
            'filters.name' => ['string', 'Nome do usuário'],
            'filters.no_get_me' => ['boolean', 'Não retornar meu usuário'],
        ],
    ];

    public function create(array $data, $requester = null)
    {
        $context = 'create';

        return $this->validate($data, $this->getValidate($context));
    }

    public function update(array $data, $requester = null)
    {
        $context = 'update';

        return $this->validate($data, $this->removeRequiredRules($this->getValidate($context)));
    }

    public function search(array $data, $requester = null)
    {
        return $this->validate($data, $this->getValidateWithSearch('search'));
    }
}
