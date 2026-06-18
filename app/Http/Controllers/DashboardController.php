<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;

class DashboardController extends Controller
{
    public function index()
    {
        $business = BusinessSetting::first();
        
        $data = [
            'total_kendaraan_hari_ini' => 0,
            'kendaraan_parkir_aktif' => 0,
            'pendapatan_hari_ini' => 0,
            'karcis_hilang_hari_ini' => 0,
            'aktivitas_terbaru' => []
        ];

        return view('dashboard.index', compact('business', 'data'));
    }
}
