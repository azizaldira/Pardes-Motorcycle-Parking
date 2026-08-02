<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\ParkingTransaction;
use App\Models\Tariff;
use Carbon\Carbon;

class ExitController extends Controller
{
    public function index(Request $request)
    {
        $tariff = Tariff::first();
        
        $query = ParkingTransaction::where('status', 'parked');

        // Apply filters
        if ($request->filled('plate')) {
            $request->validate([
                'plate' => ['required', 'string', 'regex:/^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$/']
            ], [
                'plate.regex' => 'Format plat nomor tidak valid. Gunakan format seperti B 1234 KNJ (1–2 huruf, 3–4 angka, 2–3 huruf).'
            ]);
            $normalizedPlate = strtoupper(preg_replace('/\s+/', '', $request->plate));
            $query->where('plate_number', 'like', '%' . $normalizedPlate . '%');
        }

        if ($request->filled('vehicle_type') && $request->vehicle_type !== 'Semua Tipe') {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->filled('duration') && $request->duration !== 'Semua Durasi') {
            if ($request->duration === 'Kurang dari 6 Jam') {
                $query->where('entry_time', '>=', now()->subHours(6));
            } elseif ($request->duration === 'Lebih dari 6 Jam') {
                $query->where('entry_time', '<', now()->subHours(6));
            }
        }

        $activeTransactions = $query->orderBy('entry_time', 'desc')
                                ->get()
                                ->map(function ($item) use ($tariff) {
                                    $diff = $item->entry_time->diff(now());
                                    $hours = ($diff->days * 24) + $diff->h;
                                    $minutes = $diff->i;
                                    $item->formatted_duration = "{$hours} Jam {$minutes} Menit";
                                    
                                    $feeDetails = $this->calculateFeeDetails($item, $tariff, now());
                                    $item->estimasi_biaya = $feeDetails['total_before_fine'];

                                    return $item;
                                });

        return view('exit.index', compact('activeTransactions', 'request'));
    }

    private function calculateFeeDetails(ParkingTransaction $transaction, Tariff $tariff, Carbon $exitTime)
    {
        $entryTime = $transaction->entry_time;
        
        // 1. Duration in minutes
        $durationMinutes = $entryTime->diffInMinutes($exitTime);
        
        // 2. Base Fee (First 6 hours = 360 minutes)
        $baseFee = $tariff->base_rate;
        
        // 3. Hourly Fee (After 6 hours, ceiling)
        $hourlyFee = 0;
        $extraHours = 0;
        if ($durationMinutes > 360) {
            $extraMinutes = $durationMinutes - 360;
            $extraHours = (int) ceil($extraMinutes / 60);
            $hourlyFee = $extraHours * $tariff->hourly_rate;
        }
        
        // 4. Overnight Fee (Midnight crossings)
        $entryStartOfDay = $entryTime->copy()->startOfDay();
        $exitStartOfDay = $exitTime->copy()->startOfDay();
        $overnightCount = $entryStartOfDay->diffInDays($exitStartOfDay);
        $overnightFee = $overnightCount * $tariff->overnight_rate;
        
        return [
            'duration_minutes' => $durationMinutes,
            'base_fee' => $baseFee,
            'extra_hours' => $extraHours,
            'hourly_fee' => $hourlyFee,
            'overnight_count' => $overnightCount,
            'overnight_fee' => $overnightFee,
            'total_before_fine' => $baseFee + $hourlyFee + $overnightFee,
        ];
    }

    public function process($id)
    {
        $transaction = ParkingTransaction::with('entryUser')->findOrFail($id);
        
        if ($transaction->status !== 'parked') {
            return redirect()->route('kendaraan-keluar')->with('error', 'Kendaraan sudah diproses keluar sebelumnya.');
        }

        $tariff = Tariff::first();
        $exitTime = now();
        
        $feeDetails = $this->calculateFeeDetails($transaction, $tariff, $exitTime);
        
        // Format duration for display
        $diff = $transaction->entry_time->diff($exitTime);
        $hours = ($diff->days * 24) + $diff->h;
        $minutes = $diff->i;
        $formattedDuration = "{$hours} Jam {$minutes} Menit";

        return view('exit.process', compact('transaction', 'tariff', 'feeDetails', 'formattedDuration'));
    }

    public function checkout(Request $request, $id)
    {
        $request->validate([
            'payment_method' => ['required', 'in:Tunai,QRIS'],
            'lost_ticket' => ['nullable', 'boolean'],
        ]);

        $transaction = ParkingTransaction::findOrFail($id);
        
        if ($transaction->status !== 'parked') {
            return redirect()->route('kendaraan-keluar')->with('error', 'Kendaraan sudah diproses keluar sebelumnya.');
        }

        $tariff = Tariff::first();
        $exitTime = now();
        
        // RECALCULATE AT BACKEND
        $feeDetails = $this->calculateFeeDetails($transaction, $tariff, $exitTime);
        $totalFee = $feeDetails['total_before_fine'];
        
        $isLostTicket = $request->has('lost_ticket') && $request->lost_ticket;
        if ($isLostTicket) {
            $totalFee += $tariff->lost_ticket_fine;
        }

        $transaction->update([
            'exit_time' => $exitTime,
            'parking_duration' => $feeDetails['duration_minutes'],
            'parking_fee' => $totalFee,
            'payment_method' => $request->payment_method,
            'lost_ticket' => $isLostTicket,
            'lost_ticket_fine_amount' => $isLostTicket ? $tariff->lost_ticket_fine : 0,
            'exit_user_id' => auth()->id(),
            'status' => 'completed',
        ]);

        return redirect()->route('kendaraan-keluar')->with('success', 'Transaksi berhasil diselesaikan.');
    }
}
