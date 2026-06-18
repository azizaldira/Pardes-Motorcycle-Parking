<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\ParkingTransaction;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $business = BusinessSetting::first();
        $plate = $request->query('plate');
        $transaction = null;
        $searched = false;

        if ($plate) {
            $searched = true;
            // Normalize plate
            $normalizedPlate = strtoupper(preg_replace('/\s+/', '', $plate));

            $transaction = ParkingTransaction::with('entryUser')
                            ->where('plate_number', $normalizedPlate)
                            ->where('status', 'parked')
                            ->first();

            if ($transaction) {
                // Calculate duration
                $diff = $transaction->entry_time->diff(now());
                $hours = ($diff->days * 24) + $diff->h;
                $minutes = $diff->i;
                $transaction->formatted_duration = "{$hours} Jam {$minutes} Menit";
            }
        }

        return view('search.index', compact('business', 'transaction', 'searched', 'plate'));
    }
}
