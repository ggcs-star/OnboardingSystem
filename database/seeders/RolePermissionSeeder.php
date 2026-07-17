<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }

        $admin = Role::create([
            'name' => 'admin'
        ]);

        $manager = Role::create([
            'name' => 'manager'
        ]);

        $employee = Role::create([
            'name' => 'employee'
        ]);

        $admin->givePermissionTo(Permission::all());

        $manager->givePermissionTo([
            'users.view'
        ]);

        $employee->givePermissionTo([
            'users.view'
        ]);
    }
}
