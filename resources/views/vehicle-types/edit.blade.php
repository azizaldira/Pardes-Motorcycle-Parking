@extends('layouts.app')

@section('content')
    <div class="table-card" style="max-width: 700px; margin: 0 auto; padding: 2rem; background: white; border-radius: var(--radius-lg); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
            <div>
                <h1 class="page-title" style="color: #1e293b; margin: 0; font-size: 1.5rem;">Edit Jenis Motor</h1>
                <p class="page-subtitle" style="margin: 0; margin-top: 0.25rem; font-size: 0.875rem;">Perbarui data tipe motor atau ubah status aktivasi</p>
            </div>
            <a href="{{ route('vehicle-types.index') }}" class="btn" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; border-radius: var(--radius-sm);">
                <i class="fas fa-arrow-left" style="margin-right: 0.4rem;"></i> Kembali
            </a>
        </div>

        <form action="{{ route('vehicle-types.update', $vehicleType) }}" method="POST" id="vehicleTypeEditForm">
            @csrf
            @method('PUT')

            <!-- Nama Jenis Motor -->
            <div class="form-group mb-5" style="margin-bottom: 1.5rem;">
                <label for="name" class="form-label" style="font-weight: 600; color: #334155; display: block; margin-bottom: 0.5rem;">Nama Jenis Motor <span style="color: #ef4444;">*</span></label>
                <div style="position: relative;">
                    <i class="fas fa-motorcycle" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                           style="padding-left: 3.25rem; width: 100%; font-size: 1rem;" 
                           placeholder="Contoh: NMAX, PCX, Vario 160, Beat, Ninja 250, dll." 
                           value="{{ old('name', $vehicleType->name) }}" required maxlength="100" autocomplete="off">
                </div>
                @error('name')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Golongan Kendaraan -->
            <div class="form-group mb-5" style="margin-bottom: 1.5rem;">
                <label for="category" class="form-label" style="font-weight: 600; color: #334155; display: block; margin-bottom: 0.5rem;">Golongan Kendaraan <span style="color: #ef4444;">*</span></label>
                <div style="position: relative;">
                    <i class="fas fa-tag" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <select id="category" name="category" class="form-control @error('category') is-invalid @enderror" style="padding-left: 3.25rem; width: 100%; font-size: 1rem; background-color: white;" required>
                        <option value="Golongan I" {{ old('category', $vehicleType->category) === 'Golongan I' ? 'selected' : '' }}>Golongan I (Motor Kecil / Matic / Bebek)</option>
                        <option value="Golongan II" {{ old('category', $vehicleType->category) === 'Golongan II' ? 'selected' : '' }}>Golongan II (Motor Besar / Sport / Moge)</option>
                        <option value="Golongan III" {{ old('category', $vehicleType->category) === 'Golongan III' ? 'selected' : '' }}>Golongan III (Motor Roda Tiga / Sespan)</option>
                    </select>
                </div>
                @error('category')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Slot yang Digunakan (READONLY - Auto-updated via JS) -->
            <div class="form-group mb-5" style="margin-bottom: 1.5rem;">
                <label for="slot_display" class="form-label" style="font-weight: 600; color: #334155; display: block; margin-bottom: 0.5rem;">
                    Slot yang Digunakan <span style="font-size: 0.75rem; font-weight: 500; color: #3b82f6; background: #e0f2fe; padding: 0.2rem 0.5rem; border-radius: 4px; margin-left: 0.5rem;"><i class="fas fa-lock"></i> Otomatis mengikuti aturan golongan</span>
                </label>
                <div style="position: relative;">
                    <i class="fas fa-parking" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input type="text" id="slot_display" readonly class="form-control" 
                           style="padding-left: 3.25rem; width: 100%; font-size: 1rem; font-weight: 700; background-color: #f8fafc; color: #475569; border: 1px dashed #cbd5e1; cursor: not-allowed;" 
                           value="{{ $vehicleType->slot_required }} Slot Parkir">
                </div>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.4rem; line-height: 1.4;">
                    <i class="fas fa-info-circle text-primary"></i> Aturan mutlak sistem: Golongan I = <b>1 Slot</b>, Golongan II = <b>2 Slot</b>, Golongan III = <b>3 Slot</b> (Tidak dapat diubah manual).
                </p>
            </div>

            <!-- Status Aktivasi -->
            <div class="form-group mb-6" style="margin-bottom: 2rem;">
                <label for="status" class="form-label" style="font-weight: 600; color: #334155; display: block; margin-bottom: 0.5rem;">Status Aktivasi <span style="color: #ef4444;">*</span></label>
                <div style="position: relative;">
                    <i class="fas fa-toggle-on" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" style="padding-left: 3.25rem; width: 100%; font-size: 1rem; background-color: white;" required>
                        <option value="active" {{ old('status', $vehicleType->status) === 'active' ? 'selected' : '' }}>Aktif (Tersedia untuk dipilih saat motor masuk)</option>
                        <option value="inactive" {{ old('status', $vehicleType->status) === 'inactive' ? 'selected' : '' }}>Nonaktif (Sembunyikan dari pilihan motor masuk)</option>
                    </select>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem;">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Sesuai kebijakan integritas data, jenis motor tidak dapat dihapus permanen namun dapat dinonaktifkan agar tidak muncul di form baru.
                </p>
                @error('status')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 1.5rem; gap: 1rem;">
                <a href="{{ route('vehicle-types.index') }}" class="btn" style="background: #e2e8f0; color: #475569; text-decoration: none; padding: 0.75rem 1.5rem; font-weight: 600;">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem; font-weight: 600; border-radius: var(--radius-md);">
                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Vanilla JS Script for Automatic Slot Display -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');
            const slotDisplay = document.getElementById('slot_display');

            function updateSlotDisplay() {
                const val = categorySelect.value;
                if (val === 'Golongan I') {
                    slotDisplay.value = '1 Slot Parkir (Aturan Golongan I)';
                    slotDisplay.style.color = '#0369a1';
                    slotDisplay.style.backgroundColor = '#f0f9ff';
                } else if (val === 'Golongan II') {
                    slotDisplay.value = '2 Slot Parkir (Aturan Golongan II)';
                    slotDisplay.style.color = '#b45309';
                    slotDisplay.style.backgroundColor = '#fffbeb';
                } else if (val === 'Golongan III') {
                    slotDisplay.value = '3 Slot Parkir (Aturan Golongan III)';
                    slotDisplay.style.color = '#6b21a8';
                    slotDisplay.style.backgroundColor = '#faf5ff';
                } else {
                    slotDisplay.value = 'Pilih golongan di atas terlebih dahulu...';
                    slotDisplay.style.color = '#475569';
                    slotDisplay.style.backgroundColor = '#f8fafc';
                }
            }

            categorySelect.addEventListener('change', updateSlotDisplay);
            updateSlotDisplay(); // Initialize on load
        });
    </script>
@endsection
