<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\ParkingTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $total_kendaraan_hari_ini = ParkingTransaction::whereDate('entry_time', $today)->count();
        $kendaraan_parkir_aktif = ParkingTransaction::where('status', 'parked')->count();
        $karcis_hilang_hari_ini = ParkingTransaction::whereDate('entry_time', $today)->where('lost_ticket', true)->count();
        $pendapatan_hari_ini = ParkingTransaction::whereDate('exit_time', $today)->sum('parking_fee');

        $aktivitas_terbaru = ParkingTransaction::orderBy('entry_time', 'desc')
                                ->take(10)
                                ->get()
                                ->map(function ($item) {
                                    if ($item->status === 'parked') {
                                        $diff = $item->entry_time->diff(now());
                                        $hours = ($diff->days * 24) + $diff->h;
                                        $minutes = $diff->i;
                                        $item->formatted_duration = "{$hours} Jam {$minutes} Menit";
                                    } else {
                                        $diff = $item->entry_time->diff($item->exit_time);
                                        $hours = ($diff->days * 24) + $diff->h;
                                        $minutes = $diff->i;
                                        $item->formatted_duration = "{$hours} Jam {$minutes} Menit";
                                    }
                                    return $item;
                                });

        $data = [
            'total_kendaraan_hari_ini' => $total_kendaraan_hari_ini,
            'kendaraan_parkir_aktif' => $kendaraan_parkir_aktif,
            'pendapatan_hari_ini' => $pendapatan_hari_ini,
            'karcis_hilang_hari_ini' => $karcis_hilang_hari_ini,
            'aktivitas_terbaru' => $aktivitas_terbaru
        ];

        return view('dashboard.index', compact('data'));
    }
}
