<?php

namespace Tests\Routes\Users;

use App\Modules\Users\Docs\UsersAuthRouteDoc;
use App\Modules\Users\Dto\LogoutUsersDto;
use App\Modules\Users\Dto\ResetPasswordUsersDto;
use App\Modules\Users\Dto\ForgetPasswordUsersDto;
use App\Modules\Users\Dto\LoginUsersDto;
use App\Modules\Users\Dto\TokenVerifyUsersDto;
use App\Modules\Users\Dto\VerifyRememberPasswordUsersDto;
use App\Modules\Users\Models\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\Units\TestDocsRoute\Mocks\ValidatorStubWithGenerator;

class UserAuthRouteTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

//    /**
//     * @testdox /v1/auth/register (POST)
//     */
//    public function testAdminUsersAuthRegister()
//    {
//        $response = $this->post('/v1/auth/register', [
//            'name' => 'John Imóveis',
//            'user' => [
//                'name' => 'John',
//                'email' => 'john@gmail.com',
//                'password' => '123456aa',
//            ],
//        ]);
//
//        $data = json_decode($response->getContent(), true);
//
////        $this->assertContainsArray($data['data'], [
////            'realestate' => [
////                'name' => 'John Imóveis',
////                'type' => \App\Core\RealEstates\Models\RealEstate::TYPE_DEMO,
////            ],
////            'user' => [
////                'name' => 'John',
////                'email' => 'john@gmail.com',
////                'realestate' => [
////                    'id' => $data['data']['realestate']['id'],
////                ],
////            ],
////        ]);
//    }

    /**
     * @testdox /v1/auth/login (POST)
     */
    public function testAdminUsersAuth2()
    {
        $user = Users::factory([
            'password' => Hash::make('123456aa'),
        ])->create();

        $response = $this->withPost('/v1/auth/login')
            ->setRouteInfo('Login', UsersAuthRouteDoc::class)
            ->addGroups(['auth'])
            ->addBody([
                'login' => $user->login,
                'password' => '123456aa',
            ], LoginUsersDto::class)
            ->run();

        $data = json_decode((string) $response->getContent(), true)['data'];

        $this->assertEquals(1, $data['user']['id']);
        $this->assertEquals($user->name, $data['user']['name']);
    }

    /**
     * @testdox /v1/auth/check (POST)
     */
    public function testAdminUsersAuthCheck()
    {
        $user = Users::factory([
            'password' => Hash::make('123456aa'),
        ])->create();
        $user->access_token = JWTAuth::fromUser($user);

        $response = $this->withPost('/v1/auth/check')
            ->setRouteInfo('AuthCheck', UsersAuthRouteDoc::class)
            ->addGroups(['auth'])
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->run();

        $data = json_decode((string) $response->getContent(), true)['data'];

        $this->assertEquals(1, $data['user']['id']);
        $this->assertEquals($user->name, $data['user']['name']);
    }

    /**
     * @testdox /v1/auth/password-recover (POST)
     */
    public function testAdminPasswordRecover()
    {
        $user = Users::factory()->create();

        $response = $this->withPost('/v1/auth/password-recover')
            ->setRouteInfo('PasswordReset', UsersAuthRouteDoc::class)
            ->addGroups(['auth'])
            ->addBody([
                'login' => $user->login,
            ], ForgetPasswordUsersDto::class)
            ->run();

        $body = json_decode((string) $response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertEquals($user->email, $body['data']['email']);
    }

    /**
     * @testdox /v1/auth/password-reset/:token (POST)
     */
    public function testAdminPasswordReset()
    {
        $user = Users::factory([
            'remember_token' => \Str::random(20),
        ])->create();

        $response = $this->withPost('/v1/auth/password-reset')
            ->setRouteInfo('PasswordReset', UsersAuthRouteDoc::class)
            ->addGroups(['auth'])
            ->addBody([
                'remember_token' => $user->remember_token,
                'new_password' => 'newPassword',
            ], ResetPasswordUsersDto::class)
            ->run();

        $body = json_decode((string) $response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertEquals($body['data']['changed'], true);
    }

    /**
     * @testdox /v1/auth/verify-remember-token (POST)
     */
    public function testAdminCheckPasswordReset()
    {
        /**
         * Token valid.
         */
        $user = Users::factory([
            'remember_token' => \Str::random(20),
            'remember_token_at' => Carbon::now(),
        ])->create();

        $response = $this->withPost('/v1/auth/verify-remember-token')
            ->setRouteInfo('VerifyRememberToken', UsersAuthRouteDoc::class)
            ->addGroups(['auth'])
            ->addBody([
                'remember_token' => $user->remember_token,
            ], VerifyRememberPasswordUsersDto::class)
            ->run();

        $body = json_decode((string) $response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertEquals($body['data']['remember_token'], $user->remember_token);
    }

    /**
     * @testdox /v1/auth/logout (POST)
     */
    public function testAdminAuthLogout()
    {
        $user = Users::factory([
            'password' => Hash::make('123456aa'),
        ])->create();
        $user->access_token = JWTAuth::fromUser($user);

        $response = $this->withPost('/v1/auth/logout')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->setRouteInfo('Logout', UsersAuthRouteDoc::class)
            ->addGroups(['auth'])
            ->addBody([
            ], LogoutUsersDto::class)
            ->run();

        $body = json_decode((string) $response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertTrue($body['data']['logout']);
    }
}
