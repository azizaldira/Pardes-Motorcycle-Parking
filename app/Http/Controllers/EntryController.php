<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\ParkingTransaction;
use Carbon\Carbon;

class EntryController extends Controller
{
    public function index()
    {
        $business = BusinessSetting::first();
        
        $activeTransactions = ParkingTransaction::where('status', 'parked')
                                ->orderBy('entry_time', 'desc')
                                ->get()
                                ->map(function ($item) {
                                    $diff = $item->entry_time->diff(now());
                                    $hours = ($diff->days * 24) + $diff->h;
                                    $minutes = $diff->i;
                                    $item->formatted_duration = "{$hours} Jam {$minutes} Menit";
                                    return $item;
                                });

        return view('entry.index', compact('business', 'activeTransactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => ['required', 'string'],
            'vehicle_type' => ['required', 'string'],
        ]);

        // Normalize plate number: remove all spaces and convert to uppercase
        $normalizedPlate = strtoupper(preg_replace('/\s+/', '', $request->plate_number));

        // Check for existing active transaction with the same plate
        $existing = ParkingTransaction::where('plate_number', $normalizedPlate)
                        ->where('status', 'parked')
                        ->first();

        if ($existing) {
            return back()->withInput()->withErrors([
                'plate_number' => 'Kendaraan dengan pelat nomor ini masih berstatus parkir.'
            ]);
        }

        ParkingTransaction::create([
            'plate_number' => $normalizedPlate,
            'entry_user_id' => auth()->id(),
            'vehicle_type' => $request->vehicle_type,
            'entry_time' => now(),
            'status' => 'parked',
        ]);

        return redirect()->route('kendaraan-masuk')->with('success', 'Kendaraan berhasil dicatat.');
    }
}
