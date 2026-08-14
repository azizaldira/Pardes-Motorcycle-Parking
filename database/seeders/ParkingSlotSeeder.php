<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingSlot;

class ParkingSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ParkingSlot::create([
            'name' => 'Kapasitas Parkir Global',
            'total_slots' => 200,
            'status' => 'active',
        ]);
    }
}
