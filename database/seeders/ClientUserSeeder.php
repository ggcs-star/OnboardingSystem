<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ClientUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create client role if it doesn't exist
        $role = Role::firstOrCreate([
            'name' => 'client',
            'guard_name' => 'web',
        ]);

        // Create client user if it doesn't exist
        $user = User::firstOrCreate(
            [
                'email' => 'client@example.com',
            ],
            [
                'name' => 'Client User',
                'password' => Hash::make('12345678'),
            ]
        );

        // Assign role
        $user->assignRole($role);
    }
}
