<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Keluar - {{ $transaction->plate_number }}</title>
    <style>
        @page {
            margin: 10px;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #000000;
            margin: 0;
            padding: 5px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header h2 {
            font-size: 13pt;
            margin: 0 0 2px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            font-size: 8pt;
            margin: 0;
        }
        .plate-number {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 8px 0;
            letter-spacing: 2px;
            border: 1px solid #000;
            padding: 5px;
            background: #f0f0f0;
        }
        .info-table {
            width: 100%;
            font-size: 8.5pt;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 45%;
            color: #222;
        }
        .info-table td.value {
            width: 55%;
            text-align: right;
            font-weight: bold;
        }
        .tariff-box {
            border: 1px dashed #000;
            padding: 6px;
            margin: 10px 0;
            font-size: 8.5pt;
            background-color: #fafafa;
        }
        .tariff-box h4 {
            margin: 0 0 4px 0;
            font-size: 9.5pt;
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 4px;
            text-transform: uppercase;
        }
        .tariff-table {
            width: 100%;
            font-size: 8.5pt;
            border-collapse: collapse;
        }
        .tariff-table td {
            padding: 3px 0;
        }
        .tariff-table td.amount {
            text-align: right;
            font-weight: bold;
        }
        .total-row {
            border-top: 1px solid #000;
            font-size: 10pt;
            font-weight: bold;
        }
        .total-row td {
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 8px;
            margin-top: 10px;
            font-size: 8pt;
        }
        .footer .petugas {
            font-size: 8.5pt;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .footer .note {
            font-style: italic;
            color: #333;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    @php
        // 1. Perhitungan durasi bersih dari data tersimpan (tanpa hitung ulang tarif)
        $durationMinutes = (int) ($transaction->parking_duration ?? 0);
        if ($durationMinutes > 0) {
            $hours = (int) floor($durationMinutes / 60);
            $mins = $durationMinutes % 60;
            $durationFormatted = "{$hours} Jam {$mins} Menit";
        } elseif ($transaction->entry_time && $transaction->exit_time) {
            $diff = $transaction->entry_time->diff($transaction->exit_time);
            $hours = ($diff->days * 24) + $diff->h;
            $durationFormatted = "{$hours} Jam {$diff->i} Menit";
        } else {
            $durationFormatted = "-";
        }

        // 2. Nominal pembayaran langsung dari kolom tersimpan di database
        $totalFeeStored = (float) ($transaction->parking_fee ?? 0);
        $fineStored = $transaction->lost_ticket ? (float) ($transaction->lost_ticket_fine_amount ?? 0) : 0;
        $parkingOnlyFee = max(0, $totalFeeStored - $fineStored);

        // 3. Status Pembayaran
        if ($transaction->payment_method === 'QRIS' && $transaction->payment_status === 'paid') {
            $statusBadge = 'LUNAS (QRIS PAID)';
        } elseif ($transaction->payment_method === 'Tunai' && $transaction->status === 'completed') {
            $statusBadge = 'LUNAS (TUNAI)';
        } else {
            $statusBadge = strtoupper($transaction->payment_status ?? $transaction->status ?? 'PROSES');
        }
    @endphp

    <!-- Header Usaha -->
    <div class="header">
        <h2>{{ $business->business_name ?? "Penitipan Motor Parde's" }}</h2>
        <p>Struk Bukti Pembayaran Parkir Keluar</p>
    </div>

    <!-- Nomor Plat -->
    <div class="plate-number">
        {{ $transaction->formatted_plate ?? $transaction->plate_number }}
    </div>

    <!-- Informasi Kendaraan & Waktu -->
    <table class="info-table">
        @if($transaction->midtrans_order_id)
        <tr>
            <td class="label">Order ID</td>
            <td class="value">{{ $transaction->midtrans_order_id }}</td>
        </tr>
        @endif
        <tr>
            <td class="label">Jenis Motor</td>
            <td class="value">{{ $transaction->vehicle_type ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Golongan</td>
            <td class="value">{{ $transaction->vehicleType->category ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Slot Digunakan</td>
            <td class="value">{{ $transaction->slot_used ?? 1 }} Slot</td>
        </tr>
        <tr>
            <td class="label">Waktu Masuk</td>
            <td class="value">{{ $transaction->entry_time ? $transaction->entry_time->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Keluar</td>
            <td class="value">{{ $transaction->exit_time ? $transaction->exit_time->format('d/m/Y H:i') . ' WIB' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Durasi Parkir</td>
            <td class="value">{{ $durationFormatted }}</td>
        </tr>
    </table>

    <!-- Transparansi Rincian Pembayaran (100% dari Data Tersimpan di Database) -->
    <div class="tariff-box">
        <h4>Rincian Pembayaran</h4>
        <table class="tariff-table">
            <tr>
                <td>Biaya Parkir Tersimpan</td>
                <td class="amount">Rp {{ number_format($parkingOnlyFee, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->lost_ticket && $fineStored > 0)
            <tr>
                <td>Denda Kehilangan Karcis</td>
                <td class="amount">Rp {{ number_format($fineStored, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Pembayaran</td>
                <td class="amount">Rp {{ number_format($totalFeeStored, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Metode Pembayaran</td>
                <td class="amount" style="font-weight: normal;">{{ $transaction->payment_method ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td class="amount" style="font-weight: bold;">{{ $statusBadge }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="petugas">
            Petugas Masuk : {{ $transaction->entryUser->name ?? 'Staff Masuk' }}<br>
            Petugas Keluar: {{ $transaction->exitUser->name ?? (auth()->user() ? auth()->user()->name : 'Staff Keluar') }}
        </div>
        <div style="font-size: 7.5pt; color: #444; margin-top: 4px;">
            Waktu Cetak: {{ now()->format('d/m/Y H:i:s') }} WIB
        </div>
        <div class="note">
            Terima Kasih Atas Kunjungan Anda<br>
            Semoga Selamat Sampai Tujuan
        </div>
        <div style="margin-top: 6px; font-size: 7pt; color: #666;">
            &copy; {{ date('Y') }} {{ $business->business_name ?? "Parde's" }} &bull; Struk Sah
        </div>
    </div>
</body>
</html>
