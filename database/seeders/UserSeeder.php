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
        \App\Models\User::create([
            'name' => 'Admin Pemilik',
            'username' => 'admin',
            'role' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
        ]);

        \App\Models\User::create([
            'name' => 'Staff Petugas',
            'username' => 'staff',
            'role' => 'staff',
            'password' => \Illuminate\Support\Facades\Hash::make('staff123'),
        ]);
    }
}
