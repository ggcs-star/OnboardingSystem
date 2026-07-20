<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin role if it doesn't exist
        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Create admin user if it doesn't exist
        $user = User::firstOrCreate(
            [
                'email' => 'nc13760@gmail.com',
            ],
            [
                'name' => 'Navin Chaudhary',
                'password' => Hash::make('12345678'),
            ]
        );

        // Assign role
        $user->assignRole($role);

        // Create second admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            [
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Admin User',
                'password' => Hash::make('12345678'),
            ]
        );

        // Assign role
        $adminUser->assignRole($role);
    }
}