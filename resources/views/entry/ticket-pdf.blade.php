<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Karcis Parkir - {{ $transaction->plate_number }}</title>
    <style>
        @page {
            margin: 10px;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            line-height: 1.3;
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
        .qr-section {
            text-align: center;
            margin: 8px 0;
        }
        .plate-number {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 4px 0 8px 0;
            letter-spacing: 2px;
            border: 1px solid #000;
            padding: 4px;
            background: #f0f0f0;
        }
        .info-table {
            width: 100%;
            font-size: 9pt;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 45%;
            color: #333;
        }
        .info-table td.value {
            width: 55%;
            text-align: right;
            font-weight: bold;
        }
        .tariff-box {
            border: 1px dashed #000;
            padding: 6px;
            margin: 8px 0;
            font-size: 8.5pt;
            background-color: #fafafa;
        }
        .tariff-box h4 {
            margin: 0 0 4px 0;
            font-size: 9pt;
            text-align: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
            text-transform: uppercase;
        }
        .tariff-table {
            width: 100%;
            font-size: 8pt;
        }
        .tariff-table td {
            padding: 1px 0;
        }
        .tariff-table td.amount {
            text-align: right;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 8pt;
        }
        .footer .petugas {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer .note {
            font-style: italic;
            color: #333;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h2>{{ $business->business_name ?? "Penitipan Motor Parde's" }}</h2>
        <p>Karcis Bukti Parkir Resmi</p>
    </div>

    <!-- QR Code Section -->
    <div class="qr-section">
        <img src="{{ $qrBase64 }}" alt="QR Code Plat Nomor" style="width: 135px; height: 135px;">
        <div style="font-size: 7.5pt; margin-top: 2px;">Scan QR saat kendaraan keluar</div>
    </div>

    <!-- Nomor Plat -->
    <div class="plate-number">
        {{ $transaction->plate_number }}
    </div>

    <!-- Informasi Kendaraan & Waktu -->
    <table class="info-table">
        <tr>
            <td class="label">Jenis Motor</td>
            <td class="value">{{ $transaction->vehicle_type }}</td>
        </tr>
        <tr>
            <td class="label">Golongan</td>
            <td class="value">{{ $transaction->vehicleType->category ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Slot Digunakan</td>
            <td class="value">{{ $transaction->slot_used }} Slot</td>
        </tr>
        <tr>
            <td class="label">Waktu Masuk</td>
            <td class="value">{{ $transaction->entry_time->format('d/m/Y H:i') }} WIB</td>
        </tr>
    </table>

    <!-- Transparansi Tarif -->
    <div class="tariff-box">
        <h4>Tarif Berlaku ({{ $transaction->vehicle_type }})</h4>
        <table class="tariff-table">
            @if($transaction->vehicleType && $transaction->vehicleType->tariff)
            <tr>
                <td>6 Jam Pertama</td>
                <td class="amount">Rp {{ number_format($transaction->vehicleType->tariff->base_rate, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Per Jam Berikutnya</td>
                <td class="amount">Rp {{ number_format($transaction->vehicleType->tariff->hourly_rate, 0, ',', '.') }}/jam</td>
            </tr>
            <tr>
                <td>Tarif Menginap</td>
                <td class="amount">Rp {{ number_format($transaction->vehicleType->tariff->overnight_rate, 0, ',', '.') }}/hari</td>
            </tr>
            @else
            <tr>
                <td colspan="2" style="text-align: center;">Tarif standar sistem berlaku</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="petugas">Petugas: {{ $transaction->entryUser->name ?? 'Staff' }}</div>
        <div class="note">
            Harap simpan karcis ini sebagai bukti sah pengambilan motor. Kehilangan karcis akan dikenakan denda kehilangan sesuai aturan.
        </div>
        <div style="margin-top: 6px; font-size: 7pt; color: #666;">
            &copy; {{ date('Y') }} {{ $business->business_name ?? "Parde's" }} &bull; Terima Kasih
        </div>
    </div>

</body>
</html>
