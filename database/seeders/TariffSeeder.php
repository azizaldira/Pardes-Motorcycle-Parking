<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tariff;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tariffs = [
            [
                'category' => 'Golongan I',
                'base_rate' => 3000,
                'hourly_rate' => 500,
                'overnight_rate' => 15000,
                'lost_ticket_fine' => 15000,
            ],
            [
                'category' => 'Golongan II',
                'base_rate' => 6000,
                'hourly_rate' => 1000,
                'overnight_rate' => 30000,
                'lost_ticket_fine' => 30000,
            ],
            [
                'category' => 'Golongan III',
                'base_rate' => 9000,
                'hourly_rate' => 1500,
                'overnight_rate' => 45000,
                'lost_ticket_fine' => 45000,
            ],
        ];

        foreach ($tariffs as $data) {
            Tariff::updateOrCreate(['category' => $data['category']], $data);
        }
    }
}
