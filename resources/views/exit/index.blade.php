@extends('layouts.app')

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding: 2rem;">
        <h1 class="page-title" style="margin-bottom: 0.25rem;">Kendaraan Keluar</h1>
        <p class="page-subtitle" style="margin-bottom: 2rem;">Daftar kendaraan yang sedang parkir</p>

        @if(session('error'))
            <div style="background-color: #fef2f2; color: #991b1b; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem; border: 1px solid #fca5a5;">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Filter Card -->
        <div class="table-card" style="padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-main); display: flex; align-items: center;">
                <i class="fas fa-filter" style="margin-right: 0.5rem; color: var(--text-muted);"></i> Filter Kendaraan
            </h3>
            
            <form action="{{ route('kendaraan-keluar') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: end;">
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="plate" class="form-label" style="font-size: 0.875rem;">Cari Plat Nomor</label>
                    <input type="text" name="plate" id="plate" class="form-control" placeholder="Contoh: B 1234 XYZ" value="{{ request('plate') }}">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="duration" class="form-label" style="font-size: 0.875rem;">Durasi Parkir</label>
                    <select name="duration" id="duration" class="form-control" onchange="this.form.submit()">
                        <option value="Semua Durasi" {{ request('duration') == 'Semua Durasi' ? 'selected' : '' }}>Semua Durasi</option>
                        <option value="Kurang dari 6 Jam" {{ request('duration') == 'Kurang dari 6 Jam' ? 'selected' : '' }}>Kurang dari 6 Jam</option>
                        <option value="Lebih dari 6 Jam" {{ request('duration') == 'Lebih dari 6 Jam' ? 'selected' : '' }}>Lebih dari 6 Jam</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="vehicle_type" class="form-label" style="font-size: 0.875rem;">Tipe Motor</label>
                    <select name="vehicle_type" id="vehicle_type" class="form-control" onchange="this.form.submit()">
                        <option value="Semua Tipe" {{ request('vehicle_type') == 'Semua Tipe' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="Matic" {{ request('vehicle_type') == 'Matic' ? 'selected' : '' }}>Matic</option>
                        <option value="Manual" {{ request('vehicle_type') == 'Manual' ? 'selected' : '' }}>Manual</option>
                        <option value="Kopling" {{ request('vehicle_type') == 'Kopling' ? 'selected' : '' }}>Kopling</option>
                    </select>
                </div>
                
                <!-- Hidden submit button in case user presses enter in text input -->
                <button type="submit" style="display: none;"></button>
            </form>
        </div>

        <!-- Tabel Kendaraan Aktif -->
        <div class="table-card">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.25rem;">Kendaraan Parkir Aktif</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted);">Menampilkan {{ $activeTransactions->count() }} kendaraan</p>
                </div>
                <i class="far fa-clock" style="color: var(--text-muted); font-size: 1.25rem;"></i>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); background-color: #f9fafb;">
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.875rem; color: var(--text-main); font-weight: 600;">Plat Nomor</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.875rem; color: var(--text-main); font-weight: 600;">Tipe Motor</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.875rem; color: var(--text-main); font-weight: 600;">Waktu Masuk</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.875rem; color: var(--text-main); font-weight: 600;">Durasi</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.875rem; color: var(--text-main); font-weight: 600;">Estimasi Biaya</th>
                            <th style="padding: 1rem 1.5rem; text-align: right; font-size: 0.875rem; color: var(--text-main); font-weight: 600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeTransactions as $transaction)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem 1.5rem; color: var(--text-main);">{{ $transaction->formatted_plate }}</td>
                            <td style="padding: 1rem 1.5rem; color: var(--text-muted);">{{ $transaction->vehicle_type }}</td>
                            <td style="padding: 1rem 1.5rem; color: var(--text-muted);">{{ $transaction->entry_time->format('d/m/Y, H.i') }}</td>
                            <td style="padding: 1rem 1.5rem; color: var(--text-muted);">{{ $transaction->formatted_duration }}</td>
                            <td style="padding: 1rem 1.5rem; color: var(--text-main);">Rp {{ number_format($transaction->estimasi_biaya, 0, ',', '.') }}</td>
                            <td style="padding: 1rem 1.5rem; text-align: right;">
                                <a href="{{ route('kendaraan-keluar.process', $transaction->id) }}" class="btn btn-primary" style="padding: 0.5rem 1.5rem; font-size: 0.875rem; font-weight: 500; border-radius: var(--radius-md);">
                                    Proses
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding: 3rem 2rem; text-align: center; color: var(--text-muted);">
                                <i class="fas fa-motorcycle" style="font-size: 2rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
                                Tidak ada kendaraan yang sesuai filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
