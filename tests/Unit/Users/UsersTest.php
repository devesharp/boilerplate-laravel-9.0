<?php

namespace Tests\Unit\Users;

use App\Modules\Users\Dto\CreateUsersDto;
use App\Modules\Users\Dto\SearchUsersDto;
use App\Modules\Users\Dto\UpdateUsersDto;
use App\Modules\Users\Interfaces\UsersPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Silber\Bouncer\Bouncer;
use Tests\TestCase;
use \App\Modules\Users\Models\Users;
use \App\Modules\Users\Services\UsersService;

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
            "filters" => [
                "id" => 1
            ]
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
        $this->assertFalse(!!Users::query()->find($resource['id'])->enabled);
    }
}
