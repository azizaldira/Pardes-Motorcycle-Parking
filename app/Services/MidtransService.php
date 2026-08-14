<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Transaction;

class MidtransService
{
    /**
     * Inisialisasi konfigurasi Midtrans menggunakan parameter dari config/midtrans.php.
     */
    public static function configure(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');
    }

    /**
     * Meminta status pembayaran dari Midtrans Status API berdasarkan order_id.
     */
    public static function getTransactionStatus(string $orderId)
    {
        self::configure();
        return Transaction::status($orderId);
    }
}
