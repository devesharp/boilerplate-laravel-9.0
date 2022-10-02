<?php

namespace Tests\Unit\Users;

use App\Modules\Uploads\Models\S3Files;
use App\Modules\Uploads\Services\UploadsAWSService;
use App\Modules\Users\Dto\CreateUsersDto;
use App\Modules\Users\Dto\SearchUsersDto;
use App\Modules\Users\Dto\UpdateUsersDto;
use App\Modules\Users\Dto\UploadAvatarDtoUsersDto;
use App\Modules\Users\Interfaces\UsersPermissions;
use App\Modules\Users\Models\Users;
use App\Modules\Users\Services\UsersService;
use Illuminate\Http\Testing\File;
use Mockery\MockInterface;
use Tests\TestCase;

class UsersTest extends TestCase
{
    public UsersService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(UsersService::class);
    }

    /**
     * @testdox create - default
     */
    public function testCreateUsers()
    {
        $userAdmin = Users::factory()->create();
        $UsersData = Users::factory()->raw();

        $userAdmin->allow([UsersPermissions::USERS_CREATE]);

        $resource = $this->service->create(CreateUsersDto::make($UsersData), $userAdmin);

        $this->assertGreaterThanOrEqual(1, $resource['id']);
        $this->assertEqualsArrayLeft($UsersData, $resource, ['email_verified_at']);
    }

    /**
     * @testdox update - default
     */
    public function testUpdateUsers()
    {
        $userAdmin = Users::factory()->create();
        $UsersData = Users::factory()->raw();

        $userAdmin->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_UPDATE]);

        $resource = $this->service->create(CreateUsersDto::make($UsersData), $userAdmin);

        $UsersDataUpdate = Users::factory()->raw();

        $resourceUpdated = $this->service->update($resource['id'], UpdateUsersDto::make($UsersDataUpdate), $userAdmin);

        $this->assertEqualsArrayLeft($UsersDataUpdate, $resourceUpdated);
    }

    /**
     * @testdox get - default
     */
    public function testGetUsers()
    {
        $userAdmin = Users::factory()->create();
        $UsersData = Users::factory()->raw();

        $userAdmin->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_VIEW]);

        $resourceCreated = $this->service->create(CreateUsersDto::make($UsersData), $userAdmin);

        $resource = $this->service->get($resourceCreated->id, $userAdmin);

        $this->assertGreaterThanOrEqual(1, $resource['id']);
        $this->assertEqualsArrayLeft($UsersData, $resource, ['password']);
    }

    /**
     * @testdox search - default
     */
    public function testSearchUsers()
    {
        $userAdmin = Users::factory()->create();
        Users::factory()->count(5)->create();

        $userAdmin->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_SEARCH]);

        $results = $this->service->search(SearchUsersDto::make([
            'filters' => [
                'id' => 1,
            ],
        ]), $userAdmin);
        $this->assertEquals(1, $results['count']);
    }

    /**
     * @testdox delete - default
     */
    public function testDeleteUsers()
    {
        $userAdmin = Users::factory()->create();
        $UsersData = Users::factory()->raw();

        $userAdmin->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_DELETE]);

        $resource = $this->service->create(CreateUsersDto::make($UsersData), $userAdmin);

        $this->service->delete($resource['id'], $userAdmin);

//        // If softDelete = false
//        $this->assertNull(Users::query()->find($resource['id']));

        // If softDelete = true
        $this->assertFalse((bool) Users::query()->find($resource['id'])->enabled);
    }

    /**
     * @testdox upload - default
     */
    public function testUploadAvatarUsers()
    {
        $this->mock(UploadsAWSService::class, function (MockInterface $mock) {
            $mock->shouldReceive('uploadPublicFile')->once()->andReturn([
                'key' => 'avatar/image.png',
                'url' => 'https://example.s3.us-east-2.amazonaws.com/avatar/image.png',
            ]);
        });
        $this->service = app(UsersService::class);

        $userAdmin = Users::factory()->create();
        $user = Users::factory()->create();
        $UsersData = Users::factory()->raw();
        $userAdmin->allow([UsersPermissions::USERS_CREATE, UsersPermissions::USERS_UPDATE]);

        $response = $this->service->uploadAvatar($user->id, UploadAvatarDtoUsersDto::make([
            'file' => File::fake()->image('avatar.png'),
        ]), $userAdmin);

        $this->assertEquals('avatar/image.png', $response['key']);
        $this->assertEquals('https://example.s3.us-east-2.amazonaws.com/avatar/image.png', $response['url']);
        $this->assertDatabaseHas(S3Files::getTableName(), [
            'target' => 's3',
            'user_id' => $userAdmin->id,
            'path' => 'avatar/image.png',
            'original_name' => 'avatar.png',
            'size' => 91,
        ]);
    }
}
