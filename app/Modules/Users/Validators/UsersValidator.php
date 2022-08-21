<?php

namespace App\Modules\Users\Validators;

use Devesharp\Patterns\Validator\Validator;

class UsersValidator extends Validator
{
    use \Devesharp\Patterns\Validator\ValidatorAPIGenerator;

    protected array $rules = [
        'create' => [
            "name" => ["string|max:200|required", "Nome do usuário"],
            "login" => ["string|max:200|required", "Login do usuário"],
            "email" => ["string|max:200|required", "Email do usuário"],
            "password" => ["string|max:100|required", "Senha do usuário"],
            "document" => ["string|max:100", "RG do usuário"],
            "CPF" => ["string|max:14", "CPF do usuário"],
//            "permissions" => "array",
        ],
        'login' => [
            "login" => ["string|required", "Login do usuário"],
            "password" => ["string|required", "Senha do usuário"]
        ],
        'update' => [
            '_extends' => 'create',
            'password' => null,
        ],
        // Busca
        'search' => [
            'filters.id' => ['numeric', 'Id do usuário'],
            'filters.name' => ['string', 'Nome do usuário'],
            'filters.no_get_me' => ['boolean', 'Não retornar meu usuário'],
        ],
        /*
         * Mudar senha
         */
        'change_password' => [
            'old_password' => 'required|string|max:100',
            'new_password' => 'required|string|max:100',
        ],
        /*
       * Mudar senha por token
       */
        'change_password_token' => [
            'remember_token' => 'required|string',
            'password' => 'required|string|max:100',
        ],
    ];

    public function login(array $data, $requester = null)
    {
        $context = 'login';

        return $this->validate($data, $this->getValidate($context));
    }

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

    /**
     * @param  array                     $data
     * @throws \Devesharp\CRUD\Exception
     * @return mixed
     */
    public function changePasswordByToken(array $data)
    {
        return $this->validate($data, $this->getValidate('change_password_token'));
    }
}
