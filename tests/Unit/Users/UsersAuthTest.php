<?php

namespace Tests\Unit\Users;

use App\Modules\Users\Dto\ResetPasswordUsersDto;
use App\Modules\Users\Dto\ForgetPasswordUsersDto;
use App\Modules\Users\Dto\LoginUsersDto;
use App\Modules\Users\Dto\LogoutUsersDto;
use App\Modules\Users\Dto\TokenVerifyUsersDto;
use App\Modules\Users\Dto\VerifyRememberPasswordUsersDto;
use App\Modules\Users\Models\Users;
use App\Modules\Users\Models\UsersAccessTokens;
use App\Modules\Users\Services\UsersAuthService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UsersAuthTest extends TestCase
{
  public UsersAuthService $service;

  /**
   * Setup.
   */
  protected function setUp(): void
  {
    parent::setUp();
    $this->service = app(UsersAuthService::class);
  }

  /**
   * @testdox login - Realizar autentificação com login e senha
   */
  public function testLoginWithLogin()
  {
    $user = Users::factory([
         'password' => Hash::make('123456aa'),
      ])->create();

    $userLogged = $this->service->login(LoginUsersDto::make([
         'login' => $user->login,
         'password' => '123456aa',
      ]));

    $this->assertEquals(true, isset($userLogged['access_token']));
    $this->assertEquals(true, is_string($userLogged['access_token']));

    $this->assertDatabaseHas(UsersAccessTokens::getTableName(), [
         'user_id' => $user->id
      ]);
  }

  /**
   * @testdox login - Realizar autentificação com email e senha
   */
  public function testLoginWithEmail()
  {
      $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $userLogged = $this->service->login(LoginUsersDto::make([
         'login' => $user->email,
         'password' => '123456aa',
      ]));

    $this->assertEquals(true, isset($userLogged['access_token']));
    $this->assertEquals(true, is_string($userLogged['access_token']));
  }

  /**
   * @testdox login - Não deve conseguir realizar login com usuário bloqueado
   */
  public function testLoginNotCanBlocked()
  {
    $this->expectException(\App\Exceptions\Exception::class);
    $this->expectExceptionCode(\App\Exceptions\Exception::USER_BLOCKED);

      $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();
    $user->blocked = 1;
    $user->update();

    $this->service->login(LoginUsersDto::make([
         'login' => $user->email,
         'password' => '123456aa',
      ]));
  }

  /**
   * @testdox login - Não deve conseguir realizar login com usuário deletado
   */
  public function testLoginNotCanDeleted()
  {
    $this->expectException(\App\Exceptions\Exception::class);
    $this->expectExceptionCode(\App\Exceptions\Exception::LOGIN_INCORRECT);

      $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();
    $user->enabled = 0;
    $user->update();

    $this->service->login(LoginUsersDto::make([
         'login' => $user->email,
         'password' => '123456aa',
      ]));
  }

  /**
   * @testdox logout - Remover token
   */
  public function testLogout()
  {
      $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

      $userLogged = $this->service->login(LoginUsersDto::make([
          'login' => $user->login,
          'password' => '123456aa',
      ]));

      $this->assertEquals(true, isset($userLogged['access_token']));
      $this->assertEquals(true, is_string($userLogged['access_token']));

    /*
     * Logout
     */
      auth()->setUser($user);
      $this->service->logout(LogoutUsersDto::make([]), $user);

    $this->assertEquals(0, UsersAccessTokens::query()->where('enabled', true)->count());
  }

  /**
   * @testdox isValidToken - Verifica se token é válido
   */
  public function testIsValidToken()
  {
      $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $userLogged = $this->service->login(LoginUsersDto::make([
         'login' => $user->email,
         'password' => '123456aa',
      ]));

    $user = $this->service->checkValidToken(TokenVerifyUsersDto::make([
        'access_token' => $userLogged['access_token']
    ]));
    $this->assertEquals($user, true);
  }

  /**
   * @testdox isValidToken - Token inválido
   */
  public function testIsValidTokenInvalid()
  {
    $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $userLogged = $this->service->login(LoginUsersDto::make([
         'login' => $user->email,
         'password' => '123456aa',
      ]));

    $user = $this->service->checkValidToken(TokenVerifyUsersDto::make([
        'access_token' => $userLogged['access_token'] . 'o'
    ]));
    $this->assertEquals($user, false);
  }

  /**
   * @testdox isValidToken - Token válido, porém expirado
   */
  public function testIsValidTokenExpired()
  {
    $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $userLogged = $this->service->login(LoginUsersDto::make([
         'login' => $user->email,
         'password' => '123456aa',
      ]));

    // Remove todos os tokens
    UsersAccessTokens::query()->delete();

    $user = $this->service->checkValidToken(TokenVerifyUsersDto::make([
        'access_token' => $userLogged['access_token']
    ]));
    $this->assertEquals($user, false);
  }

  /**
   * @testdox forgetPassword - Gerar token para troca de senha
   */
  public function testForgetPassword()
  {
    $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $forgetPassword = $this->service->forgetPassword(ForgetPasswordUsersDto::make(['login' => $user->login]));
    $this->assertEquals($forgetPassword['email'], $user->email);
    $this->assertEquals(isset(Users::find($user->id)->remember_token), true);
    $this->assertEquals(! empty(Users::find($user->id)->remember_token), true);
  }

  /**
   * @testdox forgetPassword - Com Email
   */
  public function testForgetPasswordLoginWithEmail()
  {
    $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $forgetPassword = $this->service->forgetPassword(ForgetPasswordUsersDto::make(['login' => $user->email]));
    $this->assertEquals($forgetPassword['email'], $user->email);
  }

  /**
   * @testdox forgetPassword - email incorreto
   */
  public function testForgetPasswordNotFoundLogin()
  {
    $this->expectException(\App\Exceptions\Exception::class);
    $this->expectExceptionCode(\App\Exceptions\Exception::RECOVERY_PASSWORD_LOGIN_INVALID);

    $this->service->forgetPassword(ForgetPasswordUsersDto::make(['login' => 'login']));
  }

  /**
   * @testdox changePasswordByToken - Mudar senha
   */
  public function testChangePasswordByToken()
  {
    $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $this->service->forgetPassword(ForgetPasswordUsersDto::make(['login' => $user->login]));

    $rememberToken = Users::find($user->id)->remember_token;
    $changed = $this->service->changePasswordByToken(ResetPasswordUsersDto::make([
         'remember_token' => Users::find($user->id)->remember_token,
         'new_password' => 'newPassword',
      ]));

    $this->assertEquals(true, $changed['changed']);
    $this->assertTrue(Hash::check('newPassword', Users::find($user->id)->password));
  }

  /**
   * @testdox changePasswordByToken - Token inválido
   */
  public function testChangePasswordByTokenInvalidToken()
  {
    $this->expectException(\App\Exceptions\Exception::class);
    $this->expectExceptionCode(\App\Exceptions\Exception::NOT_FOUND_RESOURCE);

    $this->service->changePasswordByToken(ResetPasswordUsersDto::make([
         'remember_token' => 'token',
         'new_password' => 'newPassword',
      ]));
  }

  /**
   * @testdox verifyRememberPassword - Verificar se token é valido (não encontrado)
   */
  public function testCheckTokenForgetPasswordValid()
  {
    $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $forgetPassword = $this->service->forgetPassword(ForgetPasswordUsersDto::make(['login' => $user->email]));
    $this->assertEquals($forgetPassword['email'], $user->email);

    $this->expectException(\App\Exceptions\Exception::class);
    $this->expectExceptionCode(\App\Exceptions\Exception::RECOVERY_PASSWORD_TOKEN_INVALID);

    $this->service->verifyRememberPassword(VerifyRememberPasswordUsersDto::make(['remember_token' => 'token']));
  }

  /**
   * @testdox verifyRememberPassword - Verificar se token é valido (vazio)
   */
  public function testCheckTokenForgetPasswordValidEmpty()
  {
    Users::factory()->create();
      Users::factory()->create();

    $this->expectException(\App\Exceptions\Exception::class);
    $this->expectExceptionCode(\App\Exceptions\Exception::RECOVERY_PASSWORD_TOKEN_INVALID);

    $this->service->verifyRememberPassword(VerifyRememberPasswordUsersDto::make(['remember_token' => '']));
  }

  /**
   * @testdox verifyRememberPassword - Verificar se token é valido (expirado)
   */
  public function testCheckTokenForgetPasswordValidExpired()
  {
    $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $this->service->forgetPassword(ForgetPasswordUsersDto::make(['login' => $user->login]));

    Users::find($user->id)->update([
         'remember_token_at' => Carbon::now()
            ->subDay(1)
            ->subMinutes(1)
            ->format('YmdHis'),
      ]);

    $this->expectException(\App\Exceptions\Exception::class);
    $this->expectExceptionCode(\App\Exceptions\Exception::RECOVERY_PASSWORD_TOKEN_EXPIRED);

    $this->service->verifyRememberPassword(VerifyRememberPasswordUsersDto::make([
         'remember_token' => Users::find($user->id)->remember_token,
      ]));
  }

  /**
   * @testdox verifyRememberPassword - Verificar se token é valido (valido)
   */
  public function testCheckTokenForgetPasswordValidIsValid()
  {
    $user = Users::factory([
          'password' => Hash::make('123456aa'),
      ])->create();

    $this->service->forgetPassword(ForgetPasswordUsersDto::make(['login' => $user->login]));

    Users::find($user->id)->update([
         'remember_token_at' => Carbon::now()
            ->subHour(23)
            ->format('YmdHis'),
      ]);

    $remember_token = Users::find($user->id)->remember_token;
    $tokenValid = $this->service->verifyRememberPassword(VerifyRememberPasswordUsersDto::make([
         'remember_token' => Users::find($user->id)->remember_token,
      ]));

    $this->assertEquals($remember_token, $tokenValid['remember_token']);
  }
}
