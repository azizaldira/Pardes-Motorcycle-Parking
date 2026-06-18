<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ParkingTransaction;
use App\Models\User;
use App\Models\Tariff;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan kita memiliki petugas
        $staff = User::where('role', 'staff')->first();
        if (!$staff) {
            $staff = User::first(); // fallback
        }

        // Ambil data tarif aktif
        $tariff = Tariff::first();
        if (!$tariff) {
            $tariff = Tariff::create([
                'base_rate' => 2000,
                'hourly_rate' => 1000,
                'overnight_rate' => 10000,
                'lost_ticket_fine' => 50000,
            ]);
        }

        // Hapus data lama agar tidak menumpuk saat di-run ulang
        ParkingTransaction::truncate();

        $vehicleTypes = ['Matic', 'Bebek', 'Sport'];
        $platPrefixes = ['B', 'D', 'F', 'T', 'Z'];

        // Helper untuk plat nomor acak
        $generatePlate = function() use ($platPrefixes) {
            $prefix = $platPrefixes[array_rand($platPrefixes)];
            $num = rand(1000, 9999);
            $chars = chr(rand(65, 90)) . chr(rand(65, 90));
            return $prefix . $num . $chars;
        };

        $now = Carbon::now();

        // 1. Buat 15 kendaraan PARKIR AKTIF hari ini
        for ($i = 0; $i < 15; $i++) {
            $entryTime = $now->copy()->subMinutes(rand(10, 300));
            ParkingTransaction::create([
                'plate_number' => $generatePlate(),
                'vehicle_type' => $vehicleTypes[array_rand($vehicleTypes)],
                'entry_time' => $entryTime,
                'status' => 'parked',
                'entry_user_id' => $staff->id,
            ]);
        }

        // 2. Buat 85 transaksi SELESAI di 30 hari terakhir (Maju-Mundur)
        for ($i = 0; $i < 85; $i++) {
            $daysAgo = rand(0, 30);
            $entryTime = $now->copy()->subDays($daysAgo)->subHours(rand(1, 10))->subMinutes(rand(0, 59));
            $durationHours = rand(1, 48); // Ada yang menginap
            $exitTime = $entryTime->copy()->addHours($durationHours)->addMinutes(rand(10, 50));
            
            // Hitung biaya (simple hitung kasar untuk seeder)
            $fee = $tariff->base_rate + (($durationHours - 1) * $tariff->hourly_rate);
            if ($durationHours > 24) {
                $fee += $tariff->overnight_rate;
            }

            // Kemungkinan 5% karcis hilang
            $isLost = rand(1, 100) <= 5;
            $fine = $isLost ? $tariff->lost_ticket_fine : 0;
            $totalFee = $fee + $fine;

            ParkingTransaction::create([
                'plate_number' => $generatePlate(),
                'vehicle_type' => $vehicleTypes[array_rand($vehicleTypes)],
                'entry_time' => $entryTime,
                'exit_time' => $exitTime,
                'status' => 'completed',
                'parking_fee' => $fee,
                'lost_ticket_fine_amount' => $fine,
                'payment_method' => rand(1, 100) <= 30 ? 'QRIS' : 'Tunai',
                'lost_ticket' => $isLost,
                'entry_user_id' => $staff->id,
                'exit_user_id' => $staff->id,
            ]);
        }
        
        $this->command->info('Seeder berhasil: 100 data transaksi simulasi telah disuntikkan!');
    }
}
