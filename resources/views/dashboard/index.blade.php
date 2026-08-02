@extends('layouts.app')

@section('content')
    <h1 class="page-title" style="color: #1f2937;">Dashboard</h1>
    <p class="page-subtitle" style="margin-bottom: 2rem;">Ringkasan aktivitas parkir hari ini</p>

    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem;">
            <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-grid">
        <!-- Stats Cards -->
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-bicycle"></i>
            </div>
            <div class="stat-label">Total Kendaraan Hari Ini</div>
            <div class="stat-value">{{ $data['total_kendaraan_hari_ini'] }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-parking"></i>
            </div>
            <div class="stat-label">Kendaraan Parkir Aktif</div>
            <div class="stat-value">{{ $data['kendaraan_parkir_aktif'] }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($data['pendapatan_hari_ini'], 0, ',', '.') }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-label">Karcis Hilang Hari Ini</div>
            <div class="stat-value">{{ $data['karcis_hilang_hari_ini'] }}</div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="table-card" style="margin-top: 1rem;">
        <div class="table-header">
            <div>
                <h3 class="table-title" style="color: #1f2937;">Aktivitas Terkini</h3>
                <p class="table-subtitle" style="margin: 0; margin-top: 0.25rem;">Kendaraan yang baru masuk dan keluar</p>
            </div>
            <i class="far fa-clock" style="color: var(--text-muted); font-size: 1.25rem;"></i>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="color: #1f2937; text-transform: none; font-size: 0.875rem; font-weight: 600;">Plat Nomor</th>
                        <th style="color: #1f2937; text-transform: none; font-size: 0.875rem; font-weight: 600;">Tipe Motor</th>
                        <th style="color: #1f2937; text-transform: none; font-size: 0.875rem; font-weight: 600;">Waktu Masuk</th>
                        <th style="color: #1f2937; text-transform: none; font-size: 0.875rem; font-weight: 600;">Durasi</th>
                        <th style="color: #1f2937; text-transform: none; font-size: 0.875rem; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['aktivitas_terbaru'] as $transaction)
                    <tr>
                        <td style="color: #374151;">{{ $transaction->formatted_plate }}</td>
                        <td style="color: #4b5563;">{{ $transaction->vehicle_type ?? 'Matic' }}</td>
                        <td style="color: #4b5563;">{{ $transaction->entry_time->format('j/n/Y, H.i') }}</td>
                        <td style="color: #4b5563;">{{ $transaction->formatted_duration }}</td>
                        <td>
                            @if($transaction->status === 'parked')
                                <span class="badge" style="background-color: #e0e7ff; color: #4f46e5;">Parkir</span>
                            @else
                                <span class="badge" style="background-color: #dcfce7; color: #16a34a;">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
                            <i class="fas fa-folder-open" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
                            <p style="margin: 0; font-size: 1rem; font-weight: 500;">Belum ada aktivitas hari ini</p>
                            <p style="margin: 0; font-size: 0.875rem;">Data kendaraan masuk dan keluar akan muncul di sini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
