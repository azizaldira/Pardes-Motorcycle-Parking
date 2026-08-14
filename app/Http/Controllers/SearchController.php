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
        $plate = $request->query('plate');
        $transaction = null;
        $searched = false;

        if ($plate) {
            $request->validate([
                'plate' => ['required', 'string', 'regex:/^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$/']
            ], [
                'plate.regex' => 'Format plat nomor tidak valid. Gunakan format seperti B 1234 KNJ (1–2 huruf, 3–4 angka, 2–3 huruf).'
            ]);

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

        return view('search.index', compact('transaction', 'searched', 'plate'));
    }
}
