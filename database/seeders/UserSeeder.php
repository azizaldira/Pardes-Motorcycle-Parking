<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Pemilik',
                'role' => 'admin',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Staff Petugas',
                'role' => 'staff',
                'password' => \Illuminate\Support\Facades\Hash::make('staff123'),
            ]
        );
    }
}
