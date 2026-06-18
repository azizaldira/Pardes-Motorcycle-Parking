@extends('layouts.app')

@section('content')
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Ringkasan aktivitas parkir hari ini</p>

    <div class="dashboard-grid">
        <!-- Card 1 -->
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-motorcycle"></i>
            </div>
            <div class="stat-label">Total Kendaraan Hari Ini</div>
            <div class="stat-value">{{ $data['total_kendaraan_hari_ini'] }}</div>
        </div>

        <!-- Card 2 -->
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-parking"></i>
            </div>
            <div class="stat-label">Kendaraan Parkir Aktif</div>
            <div class="stat-value">{{ $data['kendaraan_parkir_aktif'] }}</div>
        </div>

        <!-- Card 3 -->
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-label">Pendapatan Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($data['pendapatan_hari_ini'], 0, ',', '.') }}</div>
        </div>

        <!-- Card 4 -->
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-label">Karcis Hilang Hari Ini</div>
            <div class="stat-value">{{ $data['karcis_hilang_hari_ini'] }}</div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-card">
        <div class="table-header">
            <div>
                <h2 class="table-title">Aktivitas Terkini</h2>
                <p class="table-subtitle">Kendaraan yang baru masuk dan keluar</p>
            </div>
            <i class="far fa-clock" style="color: var(--text-muted); font-size: 1.25rem;"></i>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Plat Nomor</th>
                        <th>Tipe Motor</th>
                        <th>Waktu Masuk</th>
                        <th>Durasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['aktivitas_terbaru'] as $aktivitas)
                        <!-- Data will go here in the future -->
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada aktivitas terkini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
