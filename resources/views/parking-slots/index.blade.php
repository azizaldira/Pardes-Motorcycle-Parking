@extends('layouts.app')

@section('content')
    <div class="table-card" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <h1 class="page-title">Pengaturan Slot Parkir Global</h1>
        <p class="page-subtitle">Kelola batas kapasitas maksimal seluruh area penitipan motor Anda.</p>

        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
            </div>
        @endif

        <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px; background-color: #f8fafc; padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Kapasitas Total Global</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-top: 0.25rem;">{{ number_format($slot->total_slots, 0, ',', '.') }} <span style="font-size: 1rem; font-weight: 500;">Slot</span></div>
            </div>
            <div style="flex: 1; min-width: 200px; background-color: #f8fafc; padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid #e2e8f0; border-left: 4px solid {{ $percentage >= 90 ? '#ef4444' : ($percentage >= 80 ? '#f59e0b' : '#10b981') }};">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Slot Terpakai Saat Ini</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-top: 0.25rem;">{{ number_format($usedSlots, 0, ',', '.') }} <span style="font-size: 1rem; font-weight: 500;">({{ $percentage }}%)</span></div>
            </div>
            <div style="flex: 1; min-width: 200px; background-color: #f8fafc; padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid #e2e8f0; border-left: 4px solid #10b981;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Sisa Slot Tersedia</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #10b981; margin-top: 0.25rem;">{{ number_format($availableSlots, 0, ',', '.') }} <span style="font-size: 1rem; font-weight: 500;">Slot</span></div>
            </div>
        </div>

        <form action="{{ route('pengaturan-slot.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-6">
                <label for="total_slots" class="form-label" style="font-size: 1rem; font-weight: 600;">Kapasitas Total Slot Parkir</label>
                <div class="input-wrapper" style="margin-top: 0.5rem; position: relative;">
                    <i class="fas fa-cubes" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="number" id="total_slots" name="total_slots" class="form-control" 
                           style="padding-left: 3.25rem; font-size: 1.125rem; font-weight: 600;" 
                           value="{{ old('total_slots', $slot->total_slots ?? 200) }}" min="1" required>
                </div>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.5rem; line-height: 1.4;">
                    <i class="fas fa-info-circle" style="color: #3b82f6;"></i> Anda dapat menyesuaikan angka kapasitas ini sewaktu-waktu sesuai kondisi nyata lapangan atau perubahan kapasitas parkir tanpa harus mengelola area fisik yang rumit.
                </p>
                @error('total_slots')
                    <span style="color: #ef4444; font-size: 0.75rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 2rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem; font-weight: 600; border-radius: var(--radius-md);">
                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Simpan Perubahan Kapasitas
                </button>
            </div>
        </form>
    </div>
@endsection
