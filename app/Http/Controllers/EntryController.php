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

        $newTicket = null;
        if (session('new_ticket_id')) {
            $newTicket = ParkingTransaction::find(session('new_ticket_id'));
        }

        return view('entry.index', compact('activeTransactions', 'newTicket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => ['required', 'string', 'regex:/^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$/'],
            'vehicle_type' => ['required', 'string'],
        ], [
            'plate_number.regex' => 'Format plat nomor tidak valid. Gunakan format seperti B 1234 KNJ (1–2 huruf, 3–4 angka, 2–3 huruf).'
        ]);

        
        $normalizedPlate = strtoupper(preg_replace('/\s+/', '', $request->plate_number));

        
        $existing = ParkingTransaction::where('plate_number', $normalizedPlate)
                        ->where('status', 'parked')
                        ->first();

        if ($existing) {
            return back()->withInput()->withErrors([
                'plate_number' => 'Kendaraan dengan pelat nomor ini masih berstatus parkir.'
            ]);
        }

        $transaction = ParkingTransaction::create([
            'plate_number' => $normalizedPlate,
            'entry_user_id' => auth()->id(),
            'vehicle_type' => $request->vehicle_type,
            'entry_time' => now(),
            'status' => 'parked',
        ]);

        return redirect()->route('kendaraan-masuk')->with('success', 'Kendaraan berhasil dicatat.')->with('new_ticket_id', $transaction->id);
    }

    public function destroy($id)
    {

        $transaction = ParkingTransaction::findOrFail($id);
        
        if ($transaction->status !== 'parked') {
            return back()->with('error', 'Hanya kendaraan yang masih parkir yang dapat dihapus.');
        }

        $transaction->delete();

        return back()->with('success', 'Data kendaraan berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'plate_number' => ['required', 'string', 'regex:/^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$/'],
            'vehicle_type' => ['required', 'string'],
        ], [
            'plate_number.regex' => 'Format plat nomor tidak valid. Gunakan format seperti B 1234 KNJ (1–2 huruf, 3–4 angka, 2–3 huruf).'
        ]);

        $transaction = ParkingTransaction::findOrFail($id);
        
        if ($transaction->status !== 'parked') {
            return back()->with('error', 'Hanya kendaraan yang masih parkir yang dapat diubah.');
        }

        $normalizedPlate = strtoupper(preg_replace('/\s+/', '', $request->plate_number));

        // Check for existing active transaction with the same plate (other than this one)
        $existing = ParkingTransaction::where('plate_number', $normalizedPlate)
                        ->where('status', 'parked')
                        ->where('id', '!=', $id)
                        ->first();

        if ($existing) {
            return back()->with('error', 'Kendaraan dengan pelat nomor ini masih berstatus parkir.');
        }

        $transaction->update([
            'plate_number' => $normalizedPlate,
            'vehicle_type' => $request->vehicle_type,
        ]);

        return back()->with('success', 'Data kendaraan berhasil diperbarui.');
    }
}
