@extends('layouts.app')

@section('content')
    <h1 class="page-title" style="color: #1f2937;">Dashboard</h1>
    <p class="page-subtitle" style="margin-bottom: 2rem;">Ringkasan aktivitas parkir hari ini</p>

    @if(session('success'))
        <div style="background-color: #e8de16ff; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem;">
            <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Alert Notifikasi Kapasitas Parkir (>= 80%, >= 90%, >= 100%) -->
    @if($data['percentage'] >= 100)
        <div style="background-color: #b10808ff; border: 1px solid #f87171; color: #991b1b; padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; align-items: center; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1);">
            <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #dc2626; margin-right: 1.25rem;"></i>
            <div>
                <h4 style="margin: 0; font-weight: 700; font-size: 1.1rem;">PERINGATAN PENUH (100%): Kapasitas Parkir Global Telah Penuh!</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.9rem; line-height: 1.4;">Seluruh slot parkir saat ini telah terisi. Sistem akan secara otomatis menolak pencatatan transaksi kendaraan masuk baru hingga ada motor yang selesai parkir (keluar).</p>
            </div>
        </div>
    @elseif($data['percentage'] >= 90)
        <div style="background-color: #c97c1cff; border: 1px solid #fb923c; color: #9a3412; padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; align-items: center; box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.1);">
            <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #ea580c; margin-right: 1.25rem;"></i>
            <div>
                <h4 style="margin: 0; font-weight: 700; font-size: 1.1rem;">PERINGATAN KRITIS (≥ 90%): Kapasitas Parkir Hampir Habis!</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.9rem; line-height: 1.4;">Tingkat keterisian lapangan telah mencapai <b>{{ $data['percentage'] }}%</b> (Sisa {{ $data['available_slots'] }} slot lagi). Mohon bersiap mengatur kepadatan area atau menyesuaikan kuota parkir.</p>
            </div>
        </div>
    @elseif($data['percentage'] >= 80)
        <div style="background-color: #928929ff; border: 1px solid #facc15; color: #854d0e; padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; display: flex; align-items: center; box-shadow: 0 4px 6px -1px rgba(234, 179, 8, 0.1);">
            <i class="fas fa-info-circle" style="font-size: 2rem; color: #ca8a04; margin-right: 1.25rem;"></i>
            <div>
                <h4 style="margin: 0; font-weight: 700; font-size: 1.1rem;">HAMPIR PENUH (≥ 80%): Penggunaan Slot Parkir Mulai Tinggi</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.9rem; line-height: 1.4;">Kapasitas slot parkir telah terpakai sebanyak <b>{{ $data['percentage'] }}%</b> ({{ $data['used_slots'] }} dari {{ $data['total_slots'] }} slot). Perhatikan arus masuk dan keluar motor.</p>
            </div>
        </div>
    @endif

    <!-- Monitor Status Kapasitas Slot Parkir Real-Time -->
    <div class="table-card" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border-radius: var(--radius-lg); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #f8fafc; display: flex; align-items: center;">
                    <i class="fas fa-chart-pie" style="color: #38bdf8; margin-right: 0.75rem;"></i> Monitor Kapasitas Slot Parkir Global
                </h3>
                <p style="margin: 0.25rem 0 0 2rem; font-size: 0.85rem; color: #94a3b8;">Status ketersediaan ruang parkir berdasarkan seluruh transaksi yang sedang berlangsung</p>
            </div>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('pengaturan-slot') }}" style="background: rgba(255,255,255,0.1); color: #38bdf8; padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-size: 0.8rem; font-weight: 600; text-decoration: none; border: 1px solid rgba(56, 189, 248, 0.3); transition: all 0.2s;">
                <i class="fas fa-cog" style="margin-right: 0.4rem;"></i> Atur Kapasitas
            </a>
            @endif
        </div>

        @php
            $pct = $data['percentage'];
            if ($pct >= 100) {
                $progressBg = '#ef4444';
                $statusColor = '#f87171';
            } elseif ($pct >= 90) {
                $progressBg = 'linear-gradient(90deg, #f97316 0%, #ea580c 100%)';
                $statusColor = '#f87171';
            } elseif ($pct >= 80) {
                $progressBg = 'linear-gradient(90deg, #eab308 0%, #ca8a04 100%)';
                $statusColor = '#fde047';
            } else {
                $progressBg = 'linear-gradient(90deg, #10b981 0%, #059669 100%)';
                $statusColor = '#34d399';
            }
            $progressWidth = min(100, $pct);
        @endphp

        <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600;">
                <span style="color: #cbd5e1;">Tingkat Keterisian: <span style="color: {{ $statusColor }}; font-weight: 700; font-size: 1.1rem;">{{ $pct }}%</span></span>
                <span style="color: #94a3b8;">{{ number_format($data['used_slots'], 0, ',', '.') }} dari {{ number_format($data['total_slots'], 0, ',', '.') }} Slot Terpakai</span>
            </div>
            <div style="width: 100%; background-color: rgba(255,255,255,0.15); border-radius: 9999px; height: 16px; overflow: hidden; padding: 2px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);">
                <div style="background: {{ $progressBg }}; width: {{ $progressWidth }}%; height: 100%; border-radius: 9999px; transition: width 0.5s ease;"></div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 180px; background: rgba(0,0,0,0.2); padding: 0.85rem 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.05);">
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Total Kapasitas</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin-top: 0.2rem;">{{ number_format($data['total_slots'], 0, ',', '.') }} <span style="font-size: 0.85rem; font-weight: 500; color: #94a3b8;">Slot</span></div>
            </div>
            <div style="flex: 1; min-width: 180px; background: rgba(0,0,0,0.2); padding: 0.85rem 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.05);">
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Slot Terisi (Parked)</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: {{ $statusColor }}; margin-top: 0.2rem;">{{ number_format($data['used_slots'], 0, ',', '.') }} <span style="font-size: 0.85rem; font-weight: 500; color: #94a3b8;">Slot</span></div>
            </div>
            <div style="flex: 1; min-width: 180px; background: rgba(0,0,0,0.2); padding: 0.85rem 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.05);">
                <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Sisa Slot Tersedia</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #34d399; margin-top: 0.2rem;">{{ number_format($data['available_slots'], 0, ',', '.') }} <span style="font-size: 0.85rem; font-weight: 500; color: #94a3b8;">Slot</span></div>
            </div>
        </div>
    </div>

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
                                <span class="badge" style="background-color: #0733c4ff; color: #4f46e5;">Parkir</span>
                            @else
                                <span class="badge" style="background-color: #02dc4eff; color: #16a34a;">Selesai</span>
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
