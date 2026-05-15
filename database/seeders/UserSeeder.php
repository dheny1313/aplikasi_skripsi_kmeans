<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat Akun Admin
        User::create([
            'name' => 'Admin Codero',
            'email' => 'admin@codero.com',
            'password' => Hash::make('password123'), // Password dummy: password123
            'role' => 'admin',
        ]);

        // // 2. Membuat Akun Teacher
        // User::create([
        //     'name' => 'Dani Cahyono',
        //     'email' => 'dani@codero.com',
        //     'password' => Hash::make('password123'), // Password dummy: password123
        //     'role' => 'teacher',
        // ]);
    }
}
