<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '1234567890'
        ]);

        User::create([
            'name' => 'Usuario Demo',
            'email' => 'user@hotel.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'phone' => '0987654321'
        ]);
    }
}
