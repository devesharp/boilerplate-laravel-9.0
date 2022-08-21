<?php

namespace App\Modules\Users\Services;

use App\Core\Users\Models\UsersTokens;
use App\Models\User;
use App\Modules\Users\Models\Users;
use Carbon\Carbon;
use App\Exceptions\Exception;
use Devesharp\Patterns\Repository\RepositoryMysql;
use Devesharp\Patterns\Transformer\Transformer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UsersAuthService
{
    public function __construct(
        protected \App\Modules\Users\Transformers\UsersTransformer $transformer,
        protected \App\Modules\Users\Repositories\UsersRepository $repository,
        protected \App\Modules\Users\Repositories\UsersAccessTokensRepository $usersTokensRepository,
        protected \App\Modules\Users\Validators\UsersValidator $validator
    ) {}

    /**
     * @param $data
     * @return mixed
     * @throws \Devesharp\Exceptions\Exception
     */
    public function login($data)
    {
        $credentials = $this->validator->login($data);

        $dataEmail = [
            'email' => $credentials['login'],
            'password' => $credentials['password'],
        ];

        $dataLogin = [
            'login' => $credentials['login'],
            'password' => $credentials['password'],
        ];

        $token = auth()->setTTL(60 * 60 * 24 * 365)->attempt($dataLogin);

        if (empty($token)) {
            $token = auth()->setTTL(60 * 60 * 24 * 365)->attempt($dataEmail);
        }

        if (empty($token)) {
            Exception::Exception(Exception::LOGIN_INCORRECT);
        }

        /** @var Users $user */
        $user = auth()->user();
        $user->access_token = $token;

        if ($user->blocked) {
            Exception::Exception(\App\Exceptions\Exception::USER_BLOCKED);
        }

        if (! $user->enabled) {
            \App\Exceptions\Exception::Exception(\App\Exceptions\Exception::LOGIN_INCORRECT);
        }

        return Transformer::item($user, $this->transformer);
    }

    /**
     * @return mixed
     */
    public function me()
    {
        $user = auth()->user();

        return Transformer::item($user, $this->transformer);
    }

    public function checkValidToken($token): bool
    {
        try {
            $apy = JWTAuth::setToken($token)
                ->getPayload()
                ->toArray();
        } catch (\Exception $e) {
            return false;
        }

        $token = $apy['t'] ?? '';

        $tokenValid = $this->usersTokensRepository
            ->whereSameString('token', $token)
            ->count();

        if (1 !== $tokenValid) {
            return false;
        }

        return true;
    }

    /**
     * @param string $login
     * @param null $token
     * @return array
     * @throws Exception
     */
    public function forgetPassword(string $login, $token = null)
    {
        $token = $token ?? base64_encode(uniqid(rand(), true) . "-" . date("YmdHis"));

        if (empty($login)) {
            Exception::Exception(Exception::RECOVERY_PASSWORD_LOGIN_INVALID);
        }

        // Resgatar usuário
        $user = $this->repository
            ->andWhere(function (RepositoryMysql $query) use ($login) {
                $query->orWhereLike("login", $login)->orWhereLike("email", $login);
            })
            ->findOne();

        if (empty($user)) {
            Exception::Exception(Exception::RECOVERY_PASSWORD_LOGIN_INVALID);
        }

        // Adicionar token
        $this->repository->updateById($user->id, [
            "remember_token" => $token,
            "remember_token_at" => Carbon::now(),
        ]);

        // Enviar email

        return [
            "email" => $user->email,
        ];
    }

    /**
     * @param array $data
     * @return array
     * @throws \Devesharp\Exceptions\Exception
     */
    public function checkTokenRecoveryPasswordValid(array $data)
    {
        $token = $data['token'];

        if (empty($token)) {
            Exception::Exception(Exception::RECOVERY_PASSWORD_TOKEN_INVALID);
        }

        // Resgatar usuário
        $user = $this->repository->whereSameString('remember_token', $token)->findMany();

        // Não deve existir dois tokens iguais
        if (count($user) > 1) {
            $this->repository->whereSameString('remember_token', $token)->update([
                'remember_token' => '',
                'remember_token_at' => null,
            ]);

            Exception::Exception(Exception::RECOVERY_PASSWORD_TOKEN_INVALID);
        }

        if (empty($user)) {
            Exception::Exception(Exception::RECOVERY_PASSWORD_TOKEN_INVALID);
        }

        $tokenGenerateAt = Carbon::make($user[0]->remember_token_at);

        if (Carbon::now()->greaterThan($tokenGenerateAt->addDays(1))) {
            Exception::Exception(Exception::RECOVERY_PASSWORD_TOKEN_EXPIRED);
        }

        return [
            'token' => $token,
        ];
    }

    /**
     * Mudar Senha da conta pelo token de esqueci a Senha.
     *
     * @param array $data
     * @return bool[]
     * @throws Exception
     */
    public function changePasswordByToken(array $data)
    {
        $data = $this->validator->changePasswordByToken($data);

        $user = $this->repository
            ->clearQuery()
            ->whereSameString("remember_token", $data["remember_token"])
            ->findOne();

        // Token não existe
        if (empty($user)) {
            Exception::NotFound();
        }

        $user->remember_token = null;
        $user->password = Hash::make($data["password"]);
        $user->update();

        return [
            'changed' => true
        ];
    }

    /**
     * @return bool[]
     */
    public function logout()
    {
        $token = auth()->payload()['t'];

        $this->usersTokensRepository->deleteByToken($token);

        auth()->logout();

        return [
            "logout" => true,
        ];
    }

    /**
     * @return array
     */
    public function refresh()
    {
        return [
            "access_token" => auth()->refresh(),
        ];
    }

    /**
     * @param Users $user
     */
    function createTokenForUser(Users $user): string {
        $token = hash('sha256', Str::random(40)) . '|' . Carbon::now()->getTimestamp();

        $exist = $this->usersTokensRepository
            ->clearQuery()
            ->whereInt('user_id', $user->id)
            ->whereSameString('token', $token)
            ->count();

        if ($exist) {
            return $this->createTokenForUser($user);
        }

        $this->usersTokensRepository->create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        return $token;
    }
}
