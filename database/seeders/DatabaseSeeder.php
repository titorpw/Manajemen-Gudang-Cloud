<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::truncate();

        User::create([
            'name' => 'Budi Manager',
            'email' => 'budimanager@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
        ]);

        User::create([
            'name' => 'Siti Staff',
            'email' => 'sitistaff@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'staf',
        ]);
    }
}
