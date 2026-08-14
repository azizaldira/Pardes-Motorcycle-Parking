<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProcessExitCheckoutRequest;
use App\Http\Requests\CancelTransactionRequest;
use App\Models\BusinessSetting;
use App\Models\ParkingTransaction;
use App\Models\Tariff;
use App\Models\VehicleType;
use Carbon\Carbon;
use App\Services\MidtransService;
use Midtrans\Snap;
use Barryvdh\DomPDF\Facade\Pdf;

class ExitController extends Controller
{
    /**
     * Helper untuk mengambil tarif spesifik berdasarkan category golongan transaksi (dengan fallback kompatibilitas).
     */
    private function getTransactionTariff(ParkingTransaction $transaction): Tariff
    {
        // 1. Ambil berdasarkan relasi vehicleType -> category
        if ($transaction->vehicleType && $transaction->vehicleType->category) {
            $tariff = Tariff::where('category', $transaction->vehicleType->category)->first();
            if ($tariff) {
                return $tariff;
            }
        }

        // 2. Backward compatibility: Jika relasi vehicleType belum ter-load tapi vehicle_type_id ada
        if ($transaction->vehicle_type_id) {
            $vt = VehicleType::find($transaction->vehicle_type_id);
            if ($vt && $vt->category) {
                $tariff = Tariff::where('category', $vt->category)->first();
                if ($tariff) {
                    return $tariff;
                }
            }
        }

        // 3. Backward compatibility dari nama jenis motor
        if (!empty($transaction->vehicle_type)) {
            $vt = VehicleType::where('name', $transaction->vehicle_type)->first();
            if ($vt && $vt->category) {
                $tariff = Tariff::where('category', $vt->category)->first();
                if ($tariff) {
                    // Sekalian sinkronisasikan vehicle_type_id
                    $transaction->update(['vehicle_type_id' => $vt->id]);
                    return $tariff;
                }
            }
        }

        // 4. Safety fallback (default Golongan I)
        return Tariff::where('category', 'Golongan I')->first() ?? Tariff::first() ?? new Tariff([
            'category' => 'Golongan I',
            'base_rate' => 3000,
            'hourly_rate' => 500,
            'overnight_rate' => 15000,
            'lost_ticket_fine' => 15000,
        ]);
    }

    public function index(Request $request)
    {
        if (auth()->user()->role !== 'staff') {
            abort(403, 'Fitur pencarian dan pemrosesan kendaraan keluar hanya dapat diakses oleh Staff (Petugas).');
        }

        $searchedTransaction = null;
        $searchMethod = null;
        $searchError = null;

        $targetPlate = $request->input('search_plate', $request->input('plate'));
        if (!empty($targetPlate)) {
            // Normalisasi plat (hilangkan seluruh spasi dan ubah ke huruf kapital)
            $normalizedPlate = strtoupper(preg_replace('/\s+/', '', $targetPlate));
            $searchMethod = $request->input('search_method', 'Input Manual Plat');

            // 1. Cari apakah ada transaksi aktif dengan status 'parked'
            $searchedTransaction = ParkingTransaction::with(['entryUser', 'vehicleType.tariff'])
                ->where('status', 'parked')
                ->where('plate_number', $normalizedPlate)
                ->first();

            if (!$searchedTransaction) {
                // 2. Jika tidak berstatus parked, periksa apakah plat ini ada di database tapi berstatus completed / cancelled
                $pastTx = ParkingTransaction::where('plate_number', $normalizedPlate)
                    ->orderBy('updated_at', 'desc')
                    ->first();

                if ($pastTx) {
                    $statusName = $pastTx->status === 'completed' ? 'Selesai / Sudah Keluar' : 'Dibatalkan';
                    $waktu = $pastTx->updated_at ? $pastTx->updated_at->format('d/m/Y H:i') : '-';
                    $searchError = "Kendaraan dengan plat nomor '" . strtoupper($targetPlate) . "' sudah berstatus '{$statusName}' pada {$waktu} WIB. Tidak ada sesi parkir yang sedang berlangsung.";
                } else {
                    $searchError = "Kendaraan dengan plat nomor '" . strtoupper($targetPlate) . "' tidak ditemukan dalam database transaksi penitipan motor.";
                }
            } else {
                // Hitung durasi parkir sementara & rincian tarif untuk ditampilkan
                $searchedTransaction->tarif_berlaku = $this->getTransactionTariff($searchedTransaction);
                $feeDetails = $this->calculateFeeDetails($searchedTransaction, $searchedTransaction->tarif_berlaku, now());
                
                $searchedTransaction->durasi_sementara = $feeDetails['formatted_duration'];
                $searchedTransaction->estimasi_biaya = $feeDetails['total_before_fine'];
                $searchedTransaction->tarif_applied_name = $feeDetails['tarif_applied_name'];
                $searchedTransaction->can_cancel = ($searchedTransaction->entry_time->diffInMinutes(now()) <= 15);
            }
        }

        // Query daftar kendaraan parkir aktif
        $query = ParkingTransaction::where('status', 'parked');

        // Filter tabel bawah
        if (!empty($targetPlate)) {
            $normalizedPlate = strtoupper(preg_replace('/\s+/', '', $targetPlate));
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
                                ->map(function ($item) {
                                    $itemTariff = $this->getTransactionTariff($item);
                                    $feeDetails = $this->calculateFeeDetails($item, $itemTariff, now());
                                    
                                    $item->formatted_duration = $feeDetails['formatted_duration'];
                                    $item->estimasi_biaya = $feeDetails['total_before_fine'];
                                    $item->tarif_applied_name = $feeDetails['tarif_applied_name'];
                                    $item->can_cancel = ($item->entry_time->diffInMinutes(now()) <= 15);

                                    return $item;
                                });

        $vehicleTypes = VehicleType::where('status', 'active')->orderBy('id')->get();

        return view('exit.index', compact('activeTransactions', 'request', 'searchedTransaction', 'searchMethod', 'searchError', 'vehicleTypes'));
    }

    private function calculateFeeDetails(ParkingTransaction $transaction, Tariff $tariff, Carbon $exitTime)
    {
        $entryTime = $transaction->entry_time;
        
        // 1. Durasi dalam menit
        $durationMinutes = (int) $entryTime->diffInMinutes($exitTime);
        if ($durationMinutes < 0) $durationMinutes = 0;
        
        // 2. Cek apakah melintasi pukul 24.00 (berdasarkan perbedaan tanggal murni)
        $entryStartOfDay = $entryTime->copy()->startOfDay();
        $exitStartOfDay = $exitTime->copy()->startOfDay();
        $overnightCount = (int) $entryStartOfDay->diffInDays($exitStartOfDay);
        
        $baseFee = 0;
        $hourlyFee = 0;
        $extraHours = 0;
        $overnightFee = 0;
        $tarifAppliedName = '';

        if ($overnightCount > 0) {
            // Aturan mutlak: Apabila melewati pukul 24.00, tarif berubah menjadi overnight_rate per malam
            // dan tarif per jam / 6 jam pertama tidak lagi dihitung setelah masuk kategori menginap
            $overnightFee = $overnightCount * $tariff->overnight_rate;
            $tarifAppliedName = "Tarif Menginap per Malam (x {$overnightCount} Malam)";
        } else {
            // Hari yang sama (tidak menginap)
            if ($durationMinutes <= 360) {
                $baseFee = $tariff->base_rate;
                $tarifAppliedName = "Tarif 6 Jam Pertama (Base Rate)";
            } else {
                $baseFee = $tariff->base_rate;
                $extraMinutes = $durationMinutes - 360;
                $extraHours = (int) ceil($extraMinutes / 60);
                $hourlyFee = $extraHours * $tariff->hourly_rate;
                $tarifAppliedName = "Tarif 6 Jam Pertama + Per Jam (x {$extraHours} Jam)";
            }
        }
        
        $totalBeforeFine = $baseFee + $hourlyFee + $overnightFee;

        // Format durasi bersih dalam Jam dan Menit (Tanpa angka desimal)
        $diff = $entryTime->diff($exitTime);
        $hours = ($diff->days * 24) + $diff->h;
        $minutes = $diff->i;
        $formattedDuration = "{$hours} Jam {$minutes} Menit";

        return [
            'duration_minutes' => $durationMinutes,
            'formatted_duration' => $formattedDuration,
            'base_fee' => $baseFee,
            'extra_hours' => $extraHours,
            'hourly_fee' => $hourlyFee,
            'overnight_count' => $overnightCount,
            'overnight_fee' => $overnightFee,
            'total_before_fine' => $totalBeforeFine,
            'tarif_applied_name' => $tarifAppliedName,
        ];
    }

    public function process($id, Request $request)
    {
        if (auth()->user()->role !== 'staff') {
            abort(403, 'Fitur operasional kendaraan keluar hanya dapat diakses oleh Staff (Petugas).');
        }

        $transaction = ParkingTransaction::with('entryUser')->findOrFail($id);
        
        if ($transaction->status !== 'parked') {
            return redirect()->route('kendaraan-keluar')->with('error', 'Kendaraan sudah diproses keluar sebelumnya.');
        }

        $tariff = $this->getTransactionTariff($transaction);
        $exitTime = now();
        
        $feeDetails = $this->calculateFeeDetails($transaction, $tariff, $exitTime);
        $formattedDuration = $feeDetails['formatted_duration'];
        $searchMethod = $request->query('method', 'Input Manual / Daftar Parkir');
        $canCancel = ($transaction->entry_time->diffInMinutes($exitTime) <= 15);

        return view('exit.process', compact('transaction', 'tariff', 'feeDetails', 'formattedDuration', 'searchMethod', 'canCancel'));
    }

    public function checkout(ProcessExitCheckoutRequest $request, $id)
    {
        $transaction = ParkingTransaction::findOrFail($id);
        
        if ($transaction->status !== 'parked') {
            return redirect()->route('kendaraan-keluar')->with('error', 'Kendaraan sudah diproses keluar atau dibatalkan sebelumnya.');
        }

        $tariff = $this->getTransactionTariff($transaction);
        $exitTime = now();
        
        // RECALCULATE AT BACKEND
        $feeDetails = $this->calculateFeeDetails($transaction, $tariff, $exitTime);
        $totalFee = $feeDetails['total_before_fine'];
        
        $isLostTicket = $request->has('lost_ticket') && $request->lost_ticket;
        if ($isLostTicket) {
            $totalFee += $tariff->lost_ticket_fine;
        }

        $updateData = [
            'exit_time' => $exitTime,
            'parking_duration' => $feeDetails['duration_minutes'],
            'parking_fee' => $totalFee,
            'payment_method' => $request->payment_method,
            'lost_ticket' => $isLostTicket,
            'lost_ticket_fine_amount' => $isLostTicket ? $tariff->lost_ticket_fine : 0,
            'exit_user_id' => auth()->id(),
            'status' => 'completed',
        ];

        if ($request->payment_method === 'QRIS') {
            $updateData['payment_status'] = 'paid';
        }

        $transaction->update($updateData);

        if ($request->payment_method === 'QRIS') {
            $successMsg = "Pembayaran QRIS Berhasil! Transaksi kendaraan berplat {$transaction->formatted_plate} berhasil diselesaikan ({$feeDetails['formatted_duration']} - Rp " . number_format($totalFee, 0, ',', '.') . "). Kapasitas {$transaction->slot_used} slot telah otomatis dikembalikan ke kuota global garasi.";
        } else {
            $successMsg = "Transaksi kendaraan berplat {$transaction->formatted_plate} berhasil diselesaikan ({$feeDetails['formatted_duration']} - Rp " . number_format($totalFee, 0, ',', '.') . "). Kapasitas {$transaction->slot_used} slot telah otomatis dikembalikan ke kuota global garasi.";
        }

        return redirect()->route('kendaraan-keluar')
            ->with('success', $successMsg)
            ->with('completed_tx_id', $transaction->id);
    }

    /**
     * Membatalkan transaksi kendaraan jika baru berlangsung <= 15 menit dari waktu masuk.
     */
    public function cancel(CancelTransactionRequest $request, $id)
    {
        $transaction = ParkingTransaction::findOrFail($id);
        
        if ($transaction->status !== 'parked') {
            return redirect()->route('kendaraan-keluar')->with('error', 'Transaksi untuk kendaraan ini sudah tidak aktif (sudah selesai keluar atau dibatalkan sebelumnya).');
        }

        $diffMinutes = (int) $transaction->entry_time->diffInMinutes(now());
        if ($diffMinutes > 15) {
            return redirect()->route('kendaraan-keluar')->with('error', "Pembatalan transaksi ditolak! Kendaraan dengan plat {$transaction->formatted_plate} telah berstatus parkir di garasi selama {$diffMinutes} menit (melebihi batas maksimal 15 menit). Silakan proses keluar dan pembayaran normal.");
        }

        $transaction->update([
            'exit_time' => now(),
            'parking_duration' => $diffMinutes,
            'parking_fee' => 0,
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
            'status' => 'cancelled',
        ]);

        return redirect()->route('kendaraan-keluar')->with('success', "Transaksi kendaraan berplat {$transaction->formatted_plate} berhasil dibatalkan (Durasi Parkir: {$diffMinutes} menit <= 15 menit). Biaya parkir diset Rp 0 dan kapasitas {$transaction->slot_used} slot telah seketika dikembalikan ke kuota global garasi.");
    }

    /**
     * Membuat Snap Token Midtrans Sandbox untuk metode pembayaran QRIS.
     */
    public function generateSnapToken(Request $request, $id)
    {
        if (auth()->user()->role !== 'staff') {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $transaction = ParkingTransaction::findOrFail($id);
        
        if ($transaction->status !== 'parked') {
            return response()->json(['error' => 'Kendaraan sudah diproses keluar atau dibatalkan sebelumnya.'], 400);
        }

        $tariff = $this->getTransactionTariff($transaction);
        $exitTime = now();
        $feeDetails = $this->calculateFeeDetails($transaction, $tariff, $exitTime);
        $totalFee = $feeDetails['total_before_fine'];

        // Perhitungkan denda karcis hilang bila dicentang oleh petugas
        $isLostTicket = $request->has('lost_ticket') && ($request->lost_ticket == 1 || $request->lost_ticket === 'true');
        if ($isLostTicket) {
            $totalFee += $tariff->lost_ticket_fine;
        }

        // Buat midtrans_order_id yang unik untuk setiap transaksi / usaha pembayaran
        $orderId = 'PM-' . $transaction->id . '-' . time();

        // Simpan midtrans_order_id ke database sebelum mengirim permintaan ke Midtrans
        $transaction->update([
            'midtrans_order_id' => $orderId,
            'payment_method' => 'QRIS',
        ]);

        try {
            MidtransService::configure();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $totalFee,
                ],
                'callbacks' => [
                    'finish' => route('midtrans.finish'),
                    'unfinish' => route('midtrans.unfinish'),
                    'error' => route('midtrans.error'),
                ],
                'item_details' => [
                    [
                        'id' => 'PARK-' . $transaction->id,
                        'price' => (int) $totalFee,
                        'quantity' => 1,
                        'name' => substr("Parkir ({$transaction->plate_number} - {$transaction->vehicle_type})", 0, 50),
                    ]
                ],
                'customer_details' => [
                    'first_name' => 'Plat: ' . $transaction->plate_number,
                    'last_name' => "({$transaction->vehicle_type})",
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'midtrans_order_id' => $orderId,
                'total_fee' => $totalFee,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat Snap Token dari Midtrans: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sinkronisasi Status Pembayaran menggunakan Midtrans Status API secara idempotent
     * tanpa mengubah data transaksi kendaraan (durasi, tarif, waktu keluar, atau status) dari Tahap 7.
     */
    public function syncPaymentStatus($id)
    {
        if (auth()->user()->role !== 'staff') {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses Ditolak: Hanya Staff (Petugas) yang berwenang melakukan sinkronisasi status.'
            ], 403);
        }

        $transaction = ParkingTransaction::findOrFail($id);
        
        if (empty($transaction->midtrans_order_id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Belum ada Order ID Midtrans yang terkait dengan transaksi ini. Silakan pilih metode QRIS dan buka popup terlebih dahulu.'
            ], 400);
        }

        try {
            // Panggil Midtrans Status API via Service Class
            $response = MidtransService::getTransactionStatus($transaction->midtrans_order_id);
            
            // Konversikan ke bentuk array/objek standar untuk kemudahan pembuktian atribut
            $statusResponse = (object) $response;

            if (!isset($statusResponse->transaction_status)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Respons dari Midtrans tidak memuat informasi status transaksi yang valid.'
                ], 500);
            }

            $txStatus = $statusResponse->transaction_status;
            $fraudStatus = $statusResponse->fraud_status ?? null;
            $txId = $statusResponse->transaction_id ?? null;

            // Pemetaan status pembayaran sesuai kaidah Midtrans
            $newPaymentStatus = $txStatus;
            if ($txStatus === 'capture') {
                $newPaymentStatus = ($fraudStatus === 'challenge') ? 'pending' : 'paid';
            } elseif ($txStatus === 'settlement') {
                $newPaymentStatus = 'paid';
            } elseif ($txStatus === 'pending') {
                $newPaymentStatus = 'pending';
            } elseif (in_array($txStatus, ['deny', 'cancel', 'expire', 'failure'])) {
                $newPaymentStatus = $txStatus;
            }

            // Pengecekan sifat Idempotent:
            // Jika status dan transaction_id sama dengan database, tidak usah mengubah apa-apa
            $isModified = false;
            if ($transaction->payment_status !== $newPaymentStatus) {
                $transaction->payment_status = $newPaymentStatus;
                $isModified = true;
            }
            if ($txId && $transaction->midtrans_transaction_id !== $txId) {
                $transaction->midtrans_transaction_id = $txId;
                $isModified = true;
            }

            // Simpan HANYA informasi pembayaran apabila terdapat perubahan (TANPA sentuh durasi, biaya, atau status Tahap 7)
            if ($isModified) {
                $transaction->save();
            }

            // Siapkan pesan yang ramah untuk Petugas
            $statusMessage = "Status pembayaran Midtrans dipercayakan: " . strtoupper($newPaymentStatus);
            if ($newPaymentStatus === 'paid') {
                $statusMessage = "Pembayaran BERHASIL (PAID). Sistem memproses penyelesaian pengeluaran kendaraan dan mengaktifkan penanda lunas.";
            } elseif ($newPaymentStatus === 'pending') {
                $statusMessage = "Pembayaran masih menunggu (PENDING). Pelanggan belum menyelesaikan pembayaran di aplikasi QRIS/Wallet.";
            } elseif ($newPaymentStatus === 'expire') {
                $statusMessage = "Waktu pembayaran telah KADALUWARSA (EXPIRE).";
            }

            return response()->json([
                'status' => 'success',
                'payment_status' => $newPaymentStatus,
                'is_paid' => ($newPaymentStatus === 'paid'),
                'midtrans_transaction_id' => $transaction->midtrans_transaction_id,
                'midtrans_order_id' => $transaction->midtrans_order_id,
                'message' => $statusMessage,
            ]);
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, '404') !== false || stripos($errorMsg, "doesn't exist") !== false || stripos($errorMsg, "not found") !== false) {
                return response()->json([
                    'status' => 'success',
                    'payment_status' => $transaction->payment_status ?? 'pending',
                    'is_paid' => ($transaction->payment_status === 'paid'),
                    'midtrans_transaction_id' => $transaction->midtrans_transaction_id,
                    'midtrans_order_id' => $transaction->midtrans_order_id,
                    'message' => 'Transaksi belum diproses di jendela Midtrans Snap atau sedang menunggu pembayaran (Status: PENDING).'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke server Midtrans Status API: ' . $errorMsg,
            ], 500);
        }
    }

    /**
     * Tahap 9 - Cetak Struk Keluar (PDF) Thermal secara Read-Only
     */
    public function printReceiptPdf(Request $request, $id)
    {
        if (auth()->user() && auth()->user()->role !== 'staff') {
            abort(403, 'Fitur pencetakan struk keluar hanya dapat diakses oleh Staff (Petugas).');
        }

        $transaction = ParkingTransaction::with(['entryUser', 'exitUser', 'vehicleType'])->findOrFail($id);

        // Validasi syarat pencetakan struk ketat sesuai aturan bisnis:
        // - Tunai: payment_method = 'Tunai' DAN status = 'completed'
        // - QRIS: payment_method = 'QRIS' DAN payment_status = 'paid'
        $isCashValid = ($transaction->payment_method === 'Tunai' && $transaction->status === 'completed');
        $isQrisValid = ($transaction->payment_method === 'QRIS' && $transaction->payment_status === 'paid');

        if (!$isCashValid && !$isQrisValid) {
            return redirect()->route('kendaraan-keluar')->with('error', 'Struk Keluar belum dapat dicetak. Syarat: Pembayaran Tunai harus berstatus completed atau pembayaran QRIS harus berstatus PAID.');
        }

        // Ambil pengaturan bisnis (opsional, tidak mengubah data)
        $business = BusinessSetting::first();

        // Load PDF tanpa melakukan perhitungan ulang (read-only dari data yang tersimpan)
        $pdf = Pdf::loadView('exit.receipt-pdf', compact('transaction', 'business'));
        
        // Ukuran kertas custom menyerupai struk thermal kasir 80mm (~226.77 pt width)
        $pdf->setPaper([0, 0, 226.77, 600], 'portrait');

        return $pdf->stream('Struk_Keluar_' . $transaction->plate_number . '_' . date('YmdHis') . '.pdf');
    }
}
