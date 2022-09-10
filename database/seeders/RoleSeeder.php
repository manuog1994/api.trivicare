<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(['name' => 'admin']);

        $create = Permission::create(['name' => 'create']);
        $update = Permission::create(['name' => 'update']);
        $delete = Permission::create(['name' => 'delete']);

        $admin->syncPermissions([$create, $update, $delete]);
    }
}
