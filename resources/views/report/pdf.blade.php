<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ ucfirst(str_replace('_', ' ', $type)) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo-container {
            margin-bottom: 10px;
        }
        .logo-img {
            max-width: 100px;
            max-height: 100px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 5px 0;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #374151;
        }
        .report-period {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-container">
            @if(isset($business->business_logo) && $business->business_logo)
                <!-- Assuming logo is stored in public/storage -->
                @php
                    $logoPath = public_path('storage/' . $business->business_logo);
                    $logoData = '';
                    if(file_exists($logoPath)) {
                        $logoData = base64_encode(file_get_contents($logoPath));
                    }
                @endphp
                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" class="logo-img" alt="Logo">
                @else
                    <h2 style="margin:0; color:#3b82f6;">🏍️</h2>
                @endif
            @else
                <h2 style="margin:0; color:#3b82f6;">🏍️</h2>
            @endif
        </div>
        <h1 class="company-name">{{ $business->business_name ?? 'Penitipan Motor' }}</h1>
        <h2 class="report-title">Laporan {{ ucfirst(str_replace('_', ' ', $type)) }}</h2>
        <p class="report-period">Periode: {{ $periodeStr }}</p>
    </div>

    <!-- Ringkasan -->
    <table style="width: 50%; margin: 0 auto 30px auto;">
        <tr>
            <th colspan="2" class="text-center">Ringkasan Analitik</th>
        </tr>
        @if($type === 'pendapatan')
            <tr><td>Total Pendapatan</td><td class="text-right">Rp {{ number_format($metrics['total_pendapatan'], 0, ',', '.') }}</td></tr>
            <tr><td>Jumlah Transaksi</td><td class="text-right">{{ number_format($metrics['jumlah_transaksi'], 0, ',', '.') }}</td></tr>
            <tr><td>Rata-rata per Transaksi</td><td class="text-right">Rp {{ number_format($metrics['rata_pendapatan'], 0, ',', '.') }}</td></tr>
        @elseif($type === 'kendaraan')
            <tr><td>Total Kendaraan Masuk</td><td class="text-right">{{ number_format($metrics['total_masuk'], 0, ',', '.') }}</td></tr>
            <tr><td>Total Kendaraan Keluar</td><td class="text-right">{{ number_format($metrics['total_keluar'], 0, ',', '.') }}</td></tr>
            <tr><td>Total Kendaraan Aktif</td><td class="text-right">{{ number_format($metrics['total_aktif'], 0, ',', '.') }}</td></tr>
            <tr><td>Rata-rata Durasi (Jam)</td><td class="text-right">{{ $metrics['rata_durasi'] }}</td></tr>
        @elseif($type === 'karcis_hilang')
            <tr><td>Jumlah Karcis Hilang</td><td class="text-right">{{ number_format($metrics['jumlah_hilang'], 0, ',', '.') }}</td></tr>
            <tr><td>Total Denda Hilang</td><td class="text-right">Rp {{ number_format($metrics['total_denda'], 0, ',', '.') }}</td></tr>
            <tr><td>Persentase Kasus</td><td class="text-right">{{ $metrics['persentase'] }}%</td></tr>
        @elseif($type === 'petugas')
            <tr><td>Total Petugas Terdaftar</td><td class="text-right">{{ count($petugasData) }} Orang</td></tr>
        @endif
    </table>

    <!-- Data Table -->
    <table>
        <thead>
            @if($type === 'petugas')
                <tr>
                    <th>Nama Petugas</th>
                    <th class="text-center">K. Diinput</th>
                    <th class="text-center">K. Diproses</th>
                    <th class="text-right">Pendapatan</th>
                    <th class="text-center">Karcis Hilang</th>
                </tr>
            @else
                <tr>
                    <th>Waktu Keluar</th>
                    <th>Plat Nomor</th>
                    <th>Durasi (Jam)</th>
                    <th class="text-right">Biaya</th>
                    <th>Metode</th>
                    <th>Petugas Masuk</th>
                    <th>Petugas Keluar</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @if($type === 'petugas')
                @foreach($petugasData as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td class="text-center">{{ number_format($p->input_count, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($p->checkout_count, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($p->revenue, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($p->lost_tickets, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                @foreach($transactions as $t)
                <tr>
                    <td>{{ $t->exit_time->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->formatted_plate }}</td>
                    <td>{{ round($t->parking_duration / 60, 2) }}</td>
                    <td class="text-right">Rp {{ number_format($t->parking_fee, 0, ',', '.') }}</td>
                    <td>{{ $t->payment_method == 'cash' ? 'Tunai' : 'QRIS' }} {!! $t->lost_ticket ? '<b>(Hilang)</b>' : '' !!}</td>
                    <td>{{ $t->entryUser->name ?? '-' }}</td>
                    <td>{{ $t->exitUser->name ?? '-' }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; color: #6b7280; font-size: 10px;">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name }}
    </div>

</body>
</html>
