<?php

namespace Database\Seeders;

use App\Models\SalesEmployee;
use Illuminate\Database\Seeder;

class SalesEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        SalesEmployee::create([
            'name' => 'Joydeep',
            'phone' => '8866373077',
            'email' => 'joydeep@ggconsultancyservices.com',
            'status' => 'active',
        ]);

        SalesEmployee::create([
            'name' => 'Priya Solanki',
            'phone' => '9825612345',
            'email' => 'priya@ggconsultancyservices.com',
            'status' => 'active',
        ]);

        SalesEmployee::create([
            'name' => 'Karan Vora',
            'phone' => '9998765432',
            'email' => 'karan@ggconsultancyservices.com',
            'status' => 'active',
        ]);
    }
}
