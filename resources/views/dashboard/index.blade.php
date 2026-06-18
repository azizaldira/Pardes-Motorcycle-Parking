@extends('layouts.app')

@section('content')
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Ringkasan aktivitas parkir hari ini</p>

    <div class="dashboard-grid">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #eff6ff; color: var(--primary-color);">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div>
                    <p class="stat-title">Total Kendaraan Hari Ini</p>
                    <h3 class="stat-value">{{ $data['total_kendaraan_hari_ini'] }}</h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #fef2f2; color: #ef4444;">
                    <i class="fas fa-parking"></i>
                </div>
                <div>
                    <p class="stat-title">Kendaraan Parkir Aktif</p>
                    <h3 class="stat-value">{{ $data['kendaraan_parkir_aktif'] }}</h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #ecfdf5; color: #10b981;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <p class="stat-title">Pendapatan Hari Ini</p>
                    <h3 class="stat-value">Rp {{ number_format($data['pendapatan_hari_ini'], 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #fffbeb; color: #f59e0b;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <p class="stat-title">Karcis Hilang</p>
                    <h3 class="stat-value">{{ $data['karcis_hilang_hari_ini'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Recent Activity Table -->
        <div class="table-card" style="margin-top: 2rem;">
            <div class="table-header">
                <h3 class="table-title">Aktivitas Terbaru</h3>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Plat Nomor</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Waktu Masuk</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Durasi</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['aktivitas_terbaru'] as $transaction)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">{{ $transaction->formatted_plate }}</td>
                            <td style="padding: 1rem; color: var(--text-muted);">{{ $transaction->entry_time->format('H:i') }} <span style="font-size: 0.75rem;">WIB</span></td>
                            <td style="padding: 1rem; color: var(--text-main); font-weight: 500;">{{ $transaction->formatted_duration }}</td>
                            <td style="padding: 1rem;">
                                @if($transaction->status === 'parked')
                                    <span style="background-color: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">Parked</span>
                                @else
                                    <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">Completed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                                Belum ada aktivitas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
@endsection
