@extends('layouts.app')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title" style="color: #1f2937; margin: 0;">Manajemen Jenis Motor</h1>
            <p class="page-subtitle" style="margin: 0; margin-top: 0.25rem;">Kelola daftar tipe motor, klasifikasi golongan, dan status aktivasi</p>
        </div>
        <div>
            <a href="{{ route('vehicle-types.create') }}" class="btn btn-primary" style="padding: 0.65rem 1.25rem; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center;">
                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Tambah Jenis Motor
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem; border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
        </div>
    @endif

    <div class="table-card" style="margin-bottom: 1.5rem; padding: 1.5rem; background: white; border-radius: var(--radius-lg); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form action="{{ route('vehicle-types.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 220px;">
                <label for="search" style="font-size: 0.75rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.25rem;">CARI NAMA MOTOR</label>
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.875rem;"></i>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Contoh: NMAX, Beat, Ninja..." style="padding-left: 2.5rem; width: 100%;">
                </div>
            </div>
            <div style="flex: 1; min-width: 160px;">
                <label for="category" style="font-size: 0.75rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.25rem;">GOLONGAN</label>
                <select name="category" id="category" class="form-control" style="width: 100%;">
                    <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>Semua Golongan</option>
                    <option value="Golongan I" {{ request('category') == 'Golongan I' ? 'selected' : '' }}>Golongan I (1 Slot)</option>
                    <option value="Golongan II" {{ request('category') == 'Golongan II' ? 'selected' : '' }}>Golongan II (2 Slot)</option>
                    <option value="Golongan III" {{ request('category') == 'Golongan III' ? 'selected' : '' }}>Golongan III (3 Slot)</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 140px;">
                <label for="status" style="font-size: 0.75rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.25rem;">STATUS</label>
                <select name="status" id="status" class="form-control" style="width: 100%;">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.25rem; font-weight: 500; height: 38px;">
                    <i class="fas fa-filter" style="margin-right: 0.4rem;"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'category', 'status']) && (request('search') != '' || request('category') != 'all' || request('status') != 'all'))
                    <a href="{{ route('vehicle-types.index') }}" class="btn" style="padding: 0.6rem 1rem; background: #e2e8f0; color: #475569; text-decoration: none; font-weight: 500; height: 38px; display: inline-flex; align-items: center;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div>
                <h3 class="table-title" style="color: #1f2937;">Daftar Tipe Kendaraan</h3>
                <p class="table-subtitle" style="margin: 0; margin-top: 0.25rem;">Total {{ count($vehicleTypes) }} jenis motor terdaftar dalam sistem</p>
            </div>
            <i class="fas fa-motorcycle" style="color: var(--text-muted); font-size: 1.25rem;"></i>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="color: #1f2937; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; width: 5%;">No</th>
                        <th style="color: #1f2937; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; width: 30%;">Nama Jenis Motor</th>
                        <th style="color: #1f2937; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; width: 25%;">Golongan</th>
                        <th style="color: #1f2937; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; width: 15%;">Slot yang Digunakan</th>
                        <th style="color: #1f2937; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; width: 10%;">Status</th>
                        <th style="color: #1f2937; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; width: 15%; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicleTypes as $index => $vt)
                    <tr>
                        <td style="color: #64748b; font-weight: 500;">{{ $index + 1 }}</td>
                        <td style="color: #1e293b; font-weight: 600; font-size: 0.95rem;">{{ $vt->name }}</td>
                        <td>
                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; 
                                {{ $vt->category === 'Golongan I' ? 'background-color: #e0f2fe; color: #0369a1;' : ($vt->category === 'Golongan II' ? 'background-color: #fef3c7; color: #b45309;' : 'background-color: #f3e8ff; color: #6b21a8;') }}">
                                <i class="fas fa-tag" style="margin-right: 0.3rem; font-size: 0.75rem;"></i> {{ $vt->category }}
                            </span>
                        </td>
                        <td style="color: #334155; font-weight: 600;">
                            <span style="background-color: #f1f5f9; border: 1px solid #cbd5e1; padding: 0.25rem 0.6rem; border-radius: 0.375rem; font-size: 0.85rem;">
                                <i class="fas fa-parking text-primary" style="margin-right: 0.2rem;"></i> {{ $vt->slot_required }} Slot
                            </span>
                        </td>
                        <td>
                            @if($vt->status === 'active')
                                <span class="badge" style="background-color: #dcfce7; color: #16a34a; font-weight: 600;">
                                    <i class="fas fa-check-circle" style="margin-right: 0.3rem;"></i> Aktif
                                </span>
                            @else
                                <span class="badge" style="background-color: #f1f5f9; color: #64748b; font-weight: 600; border: 1px solid #cbd5e1;">
                                    <i class="fas fa-ban" style="margin-right: 0.3rem;"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('vehicle-types.edit', $vt) }}" class="btn btn-sm" style="background: #e2e8f0; color: #334155; padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.8rem; text-decoration: none; font-weight: 500;">
                                    <i class="fas fa-edit" style="margin-right: 0.2rem;"></i> Edit
                                </a>

                                <form action="{{ route('vehicle-types.toggle-status', $vt) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ $vt->status === 'active' ? 'Yakin ingin menonaktifkan jenis motor ini? Motor nonaktif tidak dapat dipilih di form Kendaraan Masuk namun tetap tersimpan pada riwayat data.' : 'Aktifkan kembali jenis motor ini?' }}');">
                                    @csrf
                                    @method('PATCH')
                                    @if($vt->status === 'active')
                                        <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #b91c1c; padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.8rem; border: none; font-weight: 500; cursor: pointer;" title="Nonaktifkan (Tanpa Hapus)">
                                            <i class="fas fa-power-off" style="margin-right: 0.2rem;"></i> Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm" style="background: #dcfce7; color: #15803d; padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.8rem; border: none; font-weight: 500; cursor: pointer;" title="Aktifkan Kembali">
                                            <i class="fas fa-check" style="margin-right: 0.2rem;"></i> Aktifkan
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
                            <i class="fas fa-motorcycle" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
                            <p style="margin: 0; font-size: 1rem; font-weight: 500;">Belum ada jenis motor yang ditemukan</p>
                            <p style="margin: 0; font-size: 0.875rem;">Silakan tambahkan jenis motor baru atau sesuaikan filter pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
