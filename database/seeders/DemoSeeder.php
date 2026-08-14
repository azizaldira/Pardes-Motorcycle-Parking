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

        // Ambil daftar tipe kendaraan beserta tarifnya
        $vehicleTypes = \App\Models\VehicleType::with('tariff')->where('status', 'active')->get();
        if ($vehicleTypes->isEmpty()) {
            $this->call(\Database\Seeders\VehicleTypeSeeder::class);
            $vehicleTypes = \App\Models\VehicleType::with('tariff')->where('status', 'active')->get();
        }

        // Hapus data lama agar tidak menumpuk saat di-run ulang
        ParkingTransaction::truncate();

        $platPrefixes = ['B', 'D', 'F', 'T', 'Z'];

        // Helper untuk plat nomor acak
        $generatePlate = function() use ($platPrefixes) {
            $prefix = $platPrefixes[array_rand($platPrefixes)];
            $num = rand(1000, 9999);
            $chars = chr(rand(65, 90)) . chr(rand(65, 90));
            return $prefix . $num . $chars;
        };

        $now = Carbon::now();

        $generateTicketNumber = function($date) {
            return 'TIX-' . $date->format('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        };

        // 1. Buat 60 kendaraan PARKIR AKTIF hari ini (20 + 40 tambahan)
        for ($i = 0; $i < 60; $i++) {
            $vt = $vehicleTypes->random();
            $entryTime = $now->copy()->subMinutes(rand(10, 300));
            $plate = $generatePlate();
            ParkingTransaction::create([
                'ticket_number' => $generateTicketNumber($entryTime),
                'qr_code' => $plate,
                'plate_number' => $plate,
                'vehicle_type' => $vt->name,
                'vehicle_type_id' => $vt->id,
                'slot_used' => $vt->slot_required,
                'entry_time' => $entryTime,
                'status' => 'parked',
                'payment_status' => 'pending',
                'entry_user_id' => $staff->id,
            ]);
        }

        // 2. Buat 80 transaksi SELESAI di 30 hari terakhir (Maju-Mundur)
        for ($i = 0; $i < 80; $i++) {
            $vt = $vehicleTypes->random();
            $tariff = $vt->tariff ?? Tariff::first();

            $daysAgo = rand(0, 30);
            $entryTime = $now->copy()->subDays($daysAgo)->subHours(rand(1, 10))->subMinutes(rand(0, 59));
            $durationHours = rand(1, 48); // Ada yang menginap
            $exitTime = $entryTime->copy()->addHours($durationHours)->addMinutes(rand(10, 50));
            $durationMinutes = $entryTime->diffInMinutes($exitTime);
            
            // Hitung biaya berdasarkan tarif jenis motor yang bersangkutan
            $fee = $tariff ? $tariff->base_rate : 3000;
            if ($durationHours > 6) {
                $fee += ($durationHours - 6) * ($tariff ? $tariff->hourly_rate : 500);
            }
            if ($durationHours > 24) {
                $fee += ($tariff ? $tariff->overnight_rate : 15000);
            }

            // Kemungkinan 5% karcis hilang
            $isLost = rand(1, 100) <= 5;
            $fine = ($isLost && $tariff) ? $tariff->lost_ticket_fine : 0;
            $totalFee = $fee + $fine;
            $plate = $generatePlate();

            ParkingTransaction::create([
                'ticket_number' => $generateTicketNumber($entryTime),
                'qr_code' => $plate,
                'plate_number' => $plate,
                'vehicle_type' => $vt->name,
                'vehicle_type_id' => $vt->id,
                'slot_used' => $vt->slot_required,
                'entry_time' => $entryTime,
                'exit_time' => $exitTime,
                'parking_duration' => $durationMinutes,
                'status' => 'completed',
                'payment_status' => 'paid',
                'parking_fee' => $fee,
                'lost_ticket_fine_amount' => $fine,
                'payment_method' => rand(1, 100) <= 30 ? 'QRIS' : 'Tunai',
                'lost_ticket' => $isLost,
                'entry_user_id' => $staff->id,
                'exit_user_id' => $staff->id,
            ]);
        }
        
        $this->command->info('Seeder berhasil: 140 data transaksi simulasi (60 aktif + 80 selesai) telah disuntikkan dengan QR Code murni dan rincian tarif per jenis motor!');
    }
}
