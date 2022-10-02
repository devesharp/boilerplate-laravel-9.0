<?php

namespace Database\Seeders\DeployUpdate;

use App\Modules\Users\Interfaces\UsersPermissions;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Bouncer;

class UpdatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (UsersPermissions::cases() as $case) {
            $ban = Bouncer::ability()->firstOrCreate([
                'name' => $case->name,
                'title' => __('permissions.'.$case->name),
            ]);
        }
    }
}
