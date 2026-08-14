@extends('layouts.app')

@section('content')
    <div class="table-card" style="margin: 0 auto; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="page-title" style="color: #1f2937; margin: 0;">Pengaturan Tarif Parkir per Golongan</h1>
                <p class="page-subtitle" style="margin: 0; margin-top: 0.25rem;">Kelola tarif 6 jam pertama, tarif per jam, menginap, dan denda karcis berdasarkan Golongan Kendaraan</p>
            </div>
            <div>
                <a href="{{ route('vehicle-types.index') }}" class="btn" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 0.65rem 1.25rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; border-radius: var(--radius-sm);">
                    <i class="fas fa-motorcycle" style="margin-right: 0.5rem; color: var(--primary-color);"></i> Lihat Jenis Motor
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem; border-left: 4px solid #10b981;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
            </div>
        @endif

        <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1rem;">
            <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 1.5rem; margin-top: 0.1rem;"></i>
            <div style="font-size: 0.875rem; color: #1e3a8a; line-height: 1.6;">
                <strong>Sistem Tarif Terpusat Berdasarkan Golongan:</strong><br>
                Tarif parkir kini dikelola terpusat pada 3 (tiga) kategori Golongan Kendaraan. Seluruh jenis motor yang terdaftar pada menu Manajemen Jenis Motor secara otomatis menggunakan tarif dari golongan yang bersangkutan tanpa perlu menyetel nominal tarif terpisah untuk setiap motor. Anda cukup mengubah nominal per Golongan di bawah ini, dan perubahan langsung berlaku pada operasional transaksi.
            </div>
        </div>

        <!-- Kartu Ringkasan 3 Golongan -->
        <div style="display: flex; gap: 1.5rem; margin-bottom: 2.5rem; flex-wrap: wrap;">
            @foreach($tariffs as $t)
            <div style="flex: 1; min-width: 260px; background-color: #f8fafc; padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid #e2e8f0; border-top: 4px solid {{ $t->category === 'Golongan I' ? '#3b82f6' : ($t->category === 'Golongan II' ? '#f59e0b' : '#8b5cf6') }}; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <span style="font-size: 1.1rem; font-weight: 800; color: #1e293b;">
                        <i class="fas fa-layer-group" style="color: {{ $t->category === 'Golongan I' ? '#3b82f6' : ($t->category === 'Golongan II' ? '#f59e0b' : '#8b5cf6') }}; margin-right: 0.35rem;"></i> {{ $t->category }}
                    </span>
                    <span style="background: white; border: 1px solid #cbd5e1; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; color: #475569;">
                        <i class="fas fa-parking text-primary" style="margin-right: 0.2rem;"></i> {{ $t->slot_required }} Slot
                    </span>
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.25rem; font-weight: 600; text-transform: uppercase;">Tarif 6 Jam Pertama</div>
                <div style="font-size: 1.65rem; font-weight: 900; color: #0f172a;">Rp {{ number_format($t->base_rate, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>

        <div class="table-responsive">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; background-color: #f8fafc;">
                        <th style="color: #475569; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; padding: 1rem; width: 5%;">No</th>
                        <th style="color: #475569; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; padding: 1rem; width: 22%;">Golongan Kendaraan</th>
                        <th style="color: #475569; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; padding: 1rem; width: 14%;">Jumlah Slot</th>
                        <th style="color: #475569; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; padding: 1rem; width: 15%;">Tarif 6 Jam Pertama</th>
                        <th style="color: #475569; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; padding: 1rem; width: 15%;">Tarif Per Jam Berikut</th>
                        <th style="color: #475569; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; padding: 1rem; width: 15%;">Tarif Menginap / Malam</th>
                        <th style="color: #475569; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; padding: 1rem; width: 14%;">Denda Hilang Karcis</th>
                        <th style="color: #475569; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; padding: 1rem; width: 10%; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tariffs as $index => $tariff)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                        <td style="color: #64748b; font-weight: 600; padding: 1.25rem 1rem;">{{ $index + 1 }}</td>
                        <td style="padding: 1.25rem 1rem;">
                            <span style="display: inline-flex; align-items: center; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 700; 
                                {{ $tariff->category === 'Golongan I' ? 'background-color: #e0f2fe; color: #0369a1;' : ($tariff->category === 'Golongan II' ? 'background-color: #fef3c7; color: #b45309;' : 'background-color: #f3e8ff; color: #6b21a8;') }}">
                                <i class="fas fa-tag" style="margin-right: 0.35rem; font-size: 0.75rem;"></i> {{ $tariff->category }}
                            </span>
                        </td>
                        <td style="padding: 1.25rem 1rem;">
                            <span style="background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 0.35rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 700; color: #334155;">
                                <i class="fas fa-parking text-primary" style="margin-right: 0.25rem;"></i> {{ $tariff->slot_required }} Slot
                            </span>
                        </td>
                        <td style="padding: 1.25rem 1rem; font-weight: 700; font-size: 0.95rem; color: #0f172a;">
                            Rp {{ number_format($tariff->base_rate, 0, ',', '.') }}
                        </td>
                        <td style="padding: 1.25rem 1rem; font-weight: 700; font-size: 0.95rem; color: #0f172a;">
                            Rp {{ number_format($tariff->hourly_rate, 0, ',', '.') }}
                        </td>
                        <td style="padding: 1.25rem 1rem; font-weight: 700; font-size: 0.95rem; color: #0f172a;">
                            Rp {{ number_format($tariff->overnight_rate, 0, ',', '.') }}
                        </td>
                        <td style="padding: 1.25rem 1rem; font-weight: 700; font-size: 0.95rem; color: #dc2626;">
                            Rp {{ number_format($tariff->lost_ticket_fine, 0, ',', '.') }}
                        </td>
                        <td style="padding: 1.25rem 1rem; text-align: right;">
                            <a href="{{ route('pengaturan-tarif.edit', $tariff) }}" class="btn" style="background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 0.55rem 1rem; font-size: 0.875rem; font-weight: 600; border-radius: var(--radius-sm); text-decoration: none; display: inline-flex; align-items: center; transition: all 0.2s;">
                                <i class="fas fa-edit" style="margin-right: 0.35rem;"></i> Ubah
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: #64748b;">
                            <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #94a3b8; margin-bottom: 0.5rem; display: block;"></i>
                            Data tarif golongan tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
