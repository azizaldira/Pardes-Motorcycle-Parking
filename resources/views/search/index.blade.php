@extends('layouts.app')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <h1 class="page-title" style="margin-bottom: 0.25rem;">Cari Kendaraan</h1>
        <p class="page-subtitle" style="margin-bottom: 2rem;">Cari kendaraan berdasarkan plat nomor</p>

        <!-- Search Form Card -->
        <div class="table-card" style="padding: 1.5rem; margin-bottom: 2rem;">
            <form action="{{ route('cari-kendaraan') }}" method="GET" style="display: flex; gap: 1rem; align-items: stretch; flex-wrap: wrap;">
                <div class="input-wrapper" style="flex: 1; min-width: 200px; margin: 0;">
                    <i class="fas fa-search" style="left: 1.5rem; color: var(--text-muted);"></i>
                    <input type="text" name="plate" class="form-control" 
                           placeholder="Contoh: B 1234 KKN" 
                           pattern="^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$"
                           title="Format plat nomor tidak valid. Gunakan format seperti B 1234 KNJ (1–2 huruf, 3–4 angka, 2–3 huruf)."
                           value="{{ old('plate', $plate ?? '') }}" required 
                           style="padding-left: 3.5rem; margin: 0; height: 100%; border-radius: var(--radius-md); text-transform: uppercase; width: 100%;">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; border-radius: var(--radius-md); font-weight: 500;">
                    Cari
                </button>
            </form>
            @error('plate')
                <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.75rem;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.25rem;"></i> {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Search Results -->
        @if($searched)
            @if($transaction)
                <div class="table-card" style="padding: 2rem; border-top: 4px solid var(--primary-color);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                        <div>
                            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.25rem;">{{ $transaction->formatted_plate }}</h2>
                            <span style="background-color: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Tipe Motor</p>
                            <p style="font-weight: 600; color: var(--text-main);">{{ $transaction->vehicle_type }}</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; background-color: var(--bg-main); padding: 1.5rem; border-radius: var(--radius-md);">
                        <div>
                            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Waktu Masuk</p>
                            <p style="font-weight: 600; color: var(--text-main);">
                                {{ $transaction->entry_time->format('d M Y, H:i') }} <span style="font-size: 0.75rem; font-weight: normal;">WIB</span>
                            </p>
                        </div>
                        <div>
                            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Durasi Saat Ini</p>
                            <p style="font-weight: 600; color: var(--text-main);">
                                <i class="far fa-clock" style="margin-right: 0.25rem; color: var(--primary-color);"></i>
                                {{ $transaction->formatted_duration }}
                            </p>
                        </div>
                        <div>
                            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Petugas Input</p>
                            <p style="font-weight: 600; color: var(--text-main);">
                                <i class="far fa-user-circle" style="margin-right: 0.25rem; color: var(--text-muted);"></i>
                                {{ $transaction->entryUser->name ?? 'Sistem' }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div style="background-color: #fef2f2; border: 1px solid #fca5a5; border-radius: var(--radius-md); padding: 2rem; text-align: center;">
                    <div style="width: 48px; height: 48px; background-color: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 1.25rem;"></i>
                    </div>
                    <h3 style="color: #991b1b; font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">Kendaraan Tidak Ditemukan</h3>
                    <p style="color: #b91c1c; font-size: 0.875rem;">Kendaraan dengan plat nomor <strong>{{ strtoupper($plate) }}</strong> tidak ditemukan atau sudah keluar dari area penitipan.</p>
                </div>
            @endif
        @endif
    </div>
@endsection
