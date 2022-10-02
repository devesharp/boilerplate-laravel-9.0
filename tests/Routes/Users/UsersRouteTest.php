<?php

namespace Tests\Routes\Users;

use App\Modules\Uploads\Services\UploadsAWSService;
use App\Modules\Users\Docs\UsersRouteDoc;
use App\Modules\Users\Dto\ChangePasswordDtoUsersDto;
use App\Modules\Users\Dto\CreateUsersDto;
use App\Modules\Users\Dto\SearchUsersDto;
use App\Modules\Users\Dto\UpdateUsersDto;
use App\Modules\Users\Interfaces\UsersPermissions;
use App\Modules\Users\Models\Users;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class UsersRouteTest extends TestCase
{
    /**
     * @testdox [POST] /v1/users
     */
    public function testRouteUsersCreate()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        $user->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_VIEW]);

        $UsersData = Users::factory()->raw();

        $response = $this->withPost('/v1/users')
            ->setRouteInfo('CreateUsers', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
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
        $user->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_UPDATE, UsersPermissions::USERS_VIEW]);

        $UsersData = Users::factory()->raw();
        $resource = Users::factory()->create();

        $response = $this->withPost('/v1/users/:id')
            ->addPath('id', $resource->id, 'Id do Usuário')
            ->setRouteInfo('UpdateUsers', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
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
        $user->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_VIEW]);

        $resource = Users::factory()->create();

        $response = $this->withGet('/v1/users/:id')
            ->setRouteInfo('GetUsers', UsersRouteDoc::class)
            ->addPath('id', $resource->id, 'Id do Usuário')
            ->setRouteInfo('GetUsers', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($resource->getAttributes(), $responseData['data'], ['password', 'created_at', 'updated_at']);
    }

    /**
     * @testdox [POST] /v1/users/me
     */
    public function testRouteUsersUpdateMe()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        $user->allow([UsersPermissions::USERS_CREATE]);

        $UsersData = Users::factory()->raw();
        $resource = Users::factory()->create();

        $response = $this->withPost('/v1/users/me')
            ->setRouteInfo('UpdateUsersMe', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
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
        $user->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_VIEW]);

        $response = $this->withGet('/v1/users/me')
            ->setRouteInfo('GetUsersMe', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertEqualsArrayLeft($user->getAttributes(), $responseData['data'], ['password', 'access_token', 'created_at', 'updated_at']);
    }

    /**
     * @testdox [POST] /v1/users/search
     */
    public function testRouteUsersSearch()
    {
        $user = Users::factory()->create();
        $user->access_token = JWTAuth::fromUser($user);
        $user->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_SEARCH]);

        Users::factory()->count(3)->create();

        $response = $this->withPost('/v1/users/search')
            ->setRouteInfo('SearchUsers', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
            ->addBody([
                'filters' => [
                ],
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
        $user->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_DELETE]);

        $resource = Users::factory()->create();

        $response = $this->withDelete('/v1/users/:id')
            ->addPath('id', $resource->id, 'Id do Usuário')
            ->setRouteInfo('GetUsers', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
            ->run();

        $responseData = json_decode($response->getContent(), true);

        $response->assertStatus(200);
        $this->assertTrue($responseData['success']);
        $this->assertTrue((bool) $responseData['data']);
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
            ->setRouteInfo('ChangePasswordUsers', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
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

    /**
     * @testdox [POST]  /v1/users/upload-avatar
     */
    public function testUsersUploadAvatar()
    {
        $user = Users::factory([
            'password' => Hash::make('123456aa'),
        ])->create();
        $user->access_token = JWTAuth::fromUser($user);
        $user->allow([UsersPermissions::USERS_UPDATE]);

        $this->mock(UploadsAWSService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadPublicFile')->once()->andReturn([
                'key' => 'avatar/image.png',
                'url' => 'https://example.s3.us-east-2.amazonaws.com/avatar/image.png',
            ]);
        });

        $resource = Users::factory()->create();

        $response = $this->withPost('/v1/users/:id/upload-avatar')
            ->setRouteInfo('UploadAvatar', UsersRouteDoc::class)
            ->addPath('id', $resource->id, 'Id do Usuário')
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
            ->addBody([
                'file' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->run();

        $body = json_decode((string) $response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertEquals('avatar/image.png', $body['data']['key']);
        $this->assertEquals('https://example.s3.us-east-2.amazonaws.com/avatar/image.png', $body['data']['url']);
    }

    /**
     * @testdox [POST]  /v1/users/me/upload-avatar
     */
    public function testUsersUploadAvatarMe()
    {
        $user = Users::factory([
            'password' => Hash::make('123456aa'),
        ])->create();
        $user->access_token = JWTAuth::fromUser($user);

        $this->mock(UploadsAWSService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadPublicFile')->once()->andReturn([
                'key' => 'avatar/image.png',
                'url' => 'https://example.s3.us-east-2.amazonaws.com/avatar/image.png',
            ]);
        });

        $resource = Users::factory()->create();

        $response = $this->withPost('/v1/users/me/upload-avatar')
            ->setRouteInfo('UploadAvatarMe', UsersRouteDoc::class)
            ->addHeader('Authorization', 'Bearer '.$user->access_token, 'Authorization')
            ->addGroups(['Usuários'])
            ->addBody([
                'file' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->run();

        $body = json_decode((string) $response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertEquals('avatar/image.png', $body['data']['key']);
        $this->assertEquals('https://example.s3.us-east-2.amazonaws.com/avatar/image.png', $body['data']['url']);
    }
}
