@extends('layouts.app')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <h1 class="page-title" style="margin-bottom: 0.25rem;">Input Kendaraan</h1>
        <p class="page-subtitle" style="margin-bottom: 2rem;">Catat kendaraan yang masuk ke area parkir</p>

        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
            </div>
        @endif

        <div class="table-card" style="padding: 2rem; margin-bottom: 2rem;">
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">Kolom bertanda <span style="color: #ef4444;">*</span> wajib diisi.</p>

            <form action="{{ route('kendaraan-masuk.store') }}" method="POST">
                @csrf

                <!-- Plat Nomor -->
                <div class="form-group mb-6">
                    <label for="plate_number" class="form-label" style="font-weight: 600;">Plat Nomor <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="plate_number" name="plate_number" class="form-control" 
                           placeholder="Contoh: B 1234 XYZ" 
                           value="{{ old('plate_number') }}" required style="text-transform: uppercase;">
                    @error('plate_number')
                        <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipe Motor -->
                <div class="form-group mb-6">
                    <label for="vehicle_type" class="form-label" style="font-weight: 600;">Tipe Motor</label>
                    <select id="vehicle_type" name="vehicle_type" class="form-control">
                        <option value="Matic" {{ old('vehicle_type') == 'Matic' ? 'selected' : '' }}>Matic</option>
                        <option value="Bebek" {{ old('vehicle_type') == 'Bebek' ? 'selected' : '' }}>Bebek</option>
                        <option value="Sport" {{ old('vehicle_type') == 'Sport' ? 'selected' : '' }}>Sport</option>
                        <option value="Kopling" {{ old('vehicle_type') == 'Kopling' ? 'selected' : '' }}>Kopling</option>
                        <option value="Lainnya" {{ old('vehicle_type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <!-- Info Box -->
                <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="fas fa-biking" style="color: var(--primary-color); margin-top: 0.125rem;"></i>
                    <div>
                        <h4 style="color: var(--primary-dark); font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Waktu Masuk Otomatis</h4>
                        <p style="color: #3b82f6; font-size: 0.75rem; margin: 0;">Sistem akan mencatat waktu masuk secara otomatis saat Anda menekan tombol Input Kendaraan</p>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.875rem; font-size: 1rem; border-radius: var(--radius-md);">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> Input Kendaraan
                </button>
            </form>
        </div>

        <!-- Tabel Kendaraan Aktif -->
        <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-main); margin-bottom: 1rem;">Kendaraan Parkir Aktif</h2>
        <div class="table-card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Plat Nomor</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Jenis Motor</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Waktu Masuk</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Durasi Saat Ini</th>
                            <th style="padding: 1rem; text-align: left; font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeTransactions as $transaction)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">{{ $transaction->formatted_plate }}</td>
                            <td style="padding: 1rem; color: var(--text-main);">{{ $transaction->vehicle_type }}</td>
                            <td style="padding: 1rem; color: var(--text-muted);">{{ $transaction->entry_time->format('H:i') }} <span style="font-size: 0.75rem;">WIB</span></td>
                            <td style="padding: 1rem; color: var(--text-main); font-weight: 500;">{{ $transaction->formatted_duration }}</td>
                            <td style="padding: 1rem;">
                                <span style="background-color: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                                Tidak ada kendaraan yang sedang parkir.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
