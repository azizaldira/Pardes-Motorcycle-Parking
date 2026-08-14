<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingTransaction;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    /**
     * Webhook Notification Callback (HTTP POST) untuk Midtrans Server (via Ngrok / Domain public).
     * Rute ini dipanggil secara otomatis oleh Midtrans saat status pembayaran berubah.
     */
    public function notification(Request $request)
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');
        $txStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');
        $txId = $request->input('transaction_id');

        if (!$orderId || !$txStatus) {
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        // Verifikasi Signature Key Midtrans untuk keamanan dari serangan request palsu
        $serverKey = config('midtrans.server_key');
        $calculatedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (!empty($signatureKey) && $signatureKey !== $calculatedSignature) {
            Log::warning('Midtrans Webhook: Invalid signature key untuk Order ID ' . $orderId);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature key'], 403);
        }

        $transaction = ParkingTransaction::where('midtrans_order_id', $orderId)->first();

        if (!$transaction) {
            Log::warning('Midtrans Webhook: Transaksi tidak ditemukan untuk Order ID ' . $orderId);
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        // Pemetaan status pembayaran sesuai ketentuan Midtrans
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

        // Perbarui status secara idempotent
        $isModified = false;
        if ($transaction->payment_status !== $newPaymentStatus) {
            $transaction->payment_status = $newPaymentStatus;
            $isModified = true;
        }
        if ($txId && $transaction->midtrans_transaction_id !== $txId) {
            $transaction->midtrans_transaction_id = $txId;
            $isModified = true;
        }

        if ($isModified) {
            $transaction->save();
            Log::info("Midtrans Webhook: Order ID {$orderId} diperbarui ke status {$newPaymentStatus}.");
        }

        return response()->json(['status' => 'success', 'message' => 'Notification processed successfully.']);
    }

    /**
     * Finish Redirect URL (HTTP GET): Dipanggil oleh browser pelanggan/petugas setelah pembayaran QRIS berhasil.
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id', $request->input('order_id'));

        if (!$orderId) {
            return redirect()->route('kendaraan-keluar')->with('success', 'Proses pembayaran digital telah selesai.');
        }

        $transaction = ParkingTransaction::where('midtrans_order_id', $orderId)->first();

        if (!$transaction) {
            return redirect()->route('kendaraan-keluar')->with('success', "Pembayaran dengan Order ID {$orderId} selesai diproses.");
        }

        // Secara proaktif sinkronisasikan status ke server Midtrans agar UI langsung mutakhir tanpa menunggu webhook
        try {
            $response = MidtransService::getTransactionStatus($orderId);
            $statusResponse = (object) $response;

            if (isset($statusResponse->transaction_status)) {
                $txStatus = $statusResponse->transaction_status;
                $fraudStatus = $statusResponse->fraud_status ?? null;
                $txId = $statusResponse->transaction_id ?? null;

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

                if ($transaction->payment_status !== $newPaymentStatus || ($txId && $transaction->midtrans_transaction_id !== $txId)) {
                    $transaction->update([
                        'payment_status' => $newPaymentStatus,
                        'midtrans_transaction_id' => $txId ?? $transaction->midtrans_transaction_id,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika koneksi lambat, fallback ke status di database
        }

        // Jika transaksi masih aktif di garasi (parked), arahkan ke halaman proses dengan pesan sukses
        if ($transaction->status === 'parked') {
            if ($transaction->payment_status === 'paid') {
                return redirect()->route('kendaraan-keluar.process', $transaction->id)
                    ->with('success', 'Pembayaran QRIS via Midtrans BERHASIL (PAID / LUNAS)! Sistem melanjutkan penyelesaian pengeluaran kendaraan...');
            } else {
                return redirect()->route('kendaraan-keluar.process', $transaction->id)
                    ->with('warning', 'Pembayaran telah diproses namun saat ini berstatus PENDING (menunggu konfirmasi penyelesaian dari bank/dompet digital).');
            }
        }

        return redirect()->route('kendaraan-keluar')
            ->with('success', "Pembayaran QRIS Berhasil! Transaksi kendaraan berplat {$transaction->formatted_plate} berhasil diselesaikan. Kapasitas {$transaction->slot_used} slot telah otomatis dikembalikan ke kuota global garasi.")
            ->with('completed_tx_id', $transaction->id);
    }

    /**
     * Unfinish Redirect URL (HTTP GET): Dipanggil saat jendela pembayaran ditutup sebelum lunas / masih tertunda.
     */
    public function unfinish(Request $request)
    {
        $orderId = $request->query('order_id', $request->input('order_id'));

        if ($orderId) {
            $transaction = ParkingTransaction::where('midtrans_order_id', $orderId)->first();
            if ($transaction && $transaction->status === 'parked') {
                return redirect()->route('kendaraan-keluar.process', $transaction->id)
                    ->with('warning', 'Proses pembayaran QRIS belum selesai atau jendela pembayaran ditutup. Silakan lanjutkan pembayaran di aplikasi atau tekan tombol Bayar dengan QRIS kembali.');
            }
        }

        return redirect()->route('kendaraan-keluar')->with('warning', 'Proses pembayaran QRIS belum diselesaikan oleh pelanggan.');
    }

    /**
     * Error Redirect URL (HTTP GET): Dipanggil jika terjadi kesalahan teknis atau kegagalan saat pembayaran QRIS.
     */
    public function error(Request $request)
    {
        $orderId = $request->query('order_id', $request->input('order_id'));

        if ($orderId) {
            $transaction = ParkingTransaction::where('midtrans_order_id', $orderId)->first();
            if ($transaction && $transaction->status === 'parked') {
                $transaction->update(['payment_status' => 'failure']);

                return redirect()->route('kendaraan-keluar.process', $transaction->id)
                    ->with('error', 'Pembayaran QRIS mengalami kegagalan atau ditolak (FAILED/ERROR). Silakan coba ulangi generate QRIS atau gunakan metode pembayaran Tunai.');
            }
        }

        return redirect()->route('kendaraan-keluar')->with('error', 'Terjadi kesalahan saat melakukan pemrosesan pembayaran QRIS.');
    }
}
