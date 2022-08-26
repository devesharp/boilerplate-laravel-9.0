<?php

namespace Database\Seeders;

use Database\Seeders\DeployUpdate\UpdatePermissionsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UpdatePermissionsSeeder::class);
    }
}
