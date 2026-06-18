<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Tariff::create([
            'base_rate' => 3000,
            'hourly_rate' => 500,
            'overnight_rate' => 15000,
            'lost_ticket_fine' => 5000,
        ]);
    }
}
