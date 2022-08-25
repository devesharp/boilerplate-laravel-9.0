<?php

namespace Tests\Routes\Users;

use App\Modules\Users\Dto\ChangePasswordDtoUsersDto;
use App\Modules\Users\Dto\CreateUsersDto;
use App\Modules\Users\Dto\LoginUsersDto;
use App\Modules\Users\Dto\SearchUsersDto;
use App\Modules\Users\Dto\UpdateUsersDto;
use App\Modules\Users\Models\Users;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UsersRouteTest extends TestCase
{
    /**
     * @testdox [POST] /v1/users
     */
    public function testRouteUsersCreate()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        $UsersData = Users::factory()->raw();

        $response = $this->withPost('/v1/users')
            ->addRouteName('CreateUsers')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->addGroups(['Users'])
            ->addBody($UsersData, CreateUsersDto::class)
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($UsersData, $responseData['data'], ['password']);
    }

    /**
     * @testdox [POST] /v1/users/:id
     */
    public function testRouteUsersUpdate()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        $UsersData = Users::factory()->raw();
        $resource = Users::factory()->create();

        $response = $this->withPost('/v1/users/:id')
            ->addPath('id', $resource->id, 'Id do Usuário')
            ->addRouteName('UpdateUsers')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->addGroups(['Users'])
            ->addBody($UsersData, UpdateUsersDto::class)
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($UsersData, $responseData['data'], ['password']);
    }

    /**
     * @testdox [GET] /v1/users/:id
     */
    public function testRouteUsersGet()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);

        $resource = Users::factory()->create();

        $response = $this->withGet('/v1/users/:id')
            ->addPath('id', $resource->id, 'Id do Usuário')
            ->addRouteName('GetUsers')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->addGroups(['Users'])
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($resource->getAttributes(), $responseData['data'], ['password']);
    }

    /**
     * @testdox [POST] /v1/users/me
     */
    public function testRouteUsersUpdateMe()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        $UsersData = Users::factory()->raw();
        $resource = Users::factory()->create();

        $response = $this->withPost('/v1/users/me')
            ->addRouteName('UpdateUsersMe')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->addGroups(['Users'])
            ->addBody($UsersData, UpdateUsersDto::class)
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($UsersData, $responseData['data'], ['password']);
    }

    /**
     * @testdox [GET] /v1/users/me
     */
    public function testRouteUsersGetMe()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);

        $response = $this->withGet('/v1/users/me')
            ->addRouteName('GetUsersMe')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->addGroups(['Users'])
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($user->getAttributes(), $responseData['data'], ['password', 'access_token']);
    }

    /**
     * @testdox [POST] /v1/users/search
     */
    public function testRouteUsersSearch()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        Users::factory()->count(3)->create();

        $response = $this->withPost('/v1/users/search')
            ->addRouteName('SearchUsers')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->addGroups(['Users'])
            ->addBody([
                'filters' => [
                ]
            ], SearchUsersDto::class)
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertEquals(4, $responseData['data']['count']);
        $this->assertEquals(4, count($responseData['data']['results']));
    }

    /**
     * @testdox [DELETE] /v1/users/:id
     */
    public function testRouteUsersDelete()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);

        $resource = Users::factory()->create();

        $response = $this->withDelete('/v1/users/:id')
            ->addPath('id', $resource->id, 'Id do Usuário')
            ->addRouteName('GetUsers')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->addGroups(['Users'])
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertTrue(!!$responseData['data']);
    }


    /**
     * @testdox [POST]  /v1/users/change-password
     */
    public function testAdminUsersChangePassword()
    {
        $user = Users::factory([
            'password' => Hash::make('123456aa'),
        ])->create();
        $user->access_token = JWTAuth::fromUser($user);

        $resource = Users::factory()->create();

        $response = $this->withPost('/v1/users/change-password')
            ->addRouteName('ChangePasswordUsers')
            ->addHeader('Authorization', 'Bearer ' . $user->access_token, 'Authorization')
            ->addGroups(['Users'])
            ->addBody([
                'old_password' => '123456aa',
                'new_password' => 'newPassword',
            ], ChangePasswordDtoUsersDto::class)
            ->run();

        $body = json_decode((string) $response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertTrue($body['data']['changed']);
        $this->assertTrue(Hash::check('newPassword', Users::find($user->id)->password));
    }
}
