<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $golonganI = [
            ['name' => 'Matic kecil', 'category' => 'Golongan I', 'slot_required' => 1],
            ['name' => 'Bebek', 'category' => 'Golongan I', 'slot_required' => 1],
            ['name' => 'Vespa kecil', 'category' => 'Golongan I', 'slot_required' => 1],
        ];

        $golonganII = [
            ['name' => 'Matic besar', 'category' => 'Golongan II', 'slot_required' => 2],
            ['name' => 'Sport', 'category' => 'Golongan II', 'slot_required' => 2],
            ['name' => 'Ninja', 'category' => 'Golongan II', 'slot_required' => 2],
            ['name' => 'Moge', 'category' => 'Golongan II', 'slot_required' => 2],
        ];

        $golonganIII = [
            ['name' => 'Motor roda tiga', 'category' => 'Golongan III', 'slot_required' => 3],
        ];

        foreach ($golonganI as $data) {
            VehicleType::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['status' => 'active'])
            );
        }

        foreach ($golonganII as $data) {
            VehicleType::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['status' => 'active'])
            );
        }

        foreach ($golonganIII as $data) {
            VehicleType::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['status' => 'active'])
            );
        }
    }
}
