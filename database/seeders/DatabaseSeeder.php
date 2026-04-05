<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ornek.com'],
            [
                'name'     => 'Super Admin',
                'password' => 'Admin123!',
                'role'     => 'super_admin',
            ]
        );
    }
}
