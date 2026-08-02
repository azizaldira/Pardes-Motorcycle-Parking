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
                    <input type="text" name="plate" id="plate" class="form-control" 
                           placeholder="Contoh: B1234KKN" 
                           pattern="^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$"
                           title="Format plat nomor tidak valid. Gunakan format seperti B 1234 KNJ (1–2 huruf, 3–4 angka, 2–3 huruf)."
                           value="{{ old('plate', request('plate')) }}" style="text-transform: uppercase;">
                    @error('plate')
                        <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
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
                        <option value="">Semua Tipe</option>
                        <option value="Matic" {{ request('vehicle_type') == 'Matic' ? 'selected' : '' }}>Matic</option>
                        <option value="Bebek" {{ request('vehicle_type') == 'Bebek' ? 'selected' : '' }}>Bebek</option>
                        <option value="Sport" {{ request('vehicle_type') == 'Sport' ? 'selected' : '' }}>Sport</option>
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
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Plat Nomor</th>
                            <th>Tipe Motor</th>
                            <th>Waktu Masuk</th>
                            <th>Durasi</th>
                            <th>Estimasi Biaya</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->formatted_plate }}</td>
                            <td style="color: var(--text-muted);">{{ $transaction->vehicle_type }}</td>
                            <td style="color: var(--text-muted);">{{ $transaction->entry_time->format('d/m/Y, H.i') }}</td>
                            <td style="color: var(--text-muted);">{{ $transaction->formatted_duration }}</td>
                            <td>Rp {{ number_format($transaction->estimasi_biaya, 0, ',', '.') }}</td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a href="{{ route('kendaraan-keluar.process', $transaction->id) }}" class="btn btn-primary" title="Proses Keluar" style="padding: 0; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </a>
                                    
                                    <button onclick="openEditKendaraanModal({{ $transaction->id }}, '{{ $transaction->plate_number }}', '{{ $transaction->vehicle_type }}')" class="btn" title="Edit Kendaraan" style="padding: 0; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md); background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db;">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('kendaraan.destroy', $transaction->id) }}" method="POST" style="margin: 0;" onsubmit="event.preventDefault(); showConfirmModal('Apakah Anda yakin ingin menghapus data kendaraan ini? Tindakan ini hanya boleh dilakukan jika terjadi kesalahan input dan tidak dapat dibatalkan.', () => this.submit());">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn" title="Hapus Kendaraan" style="padding: 0; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md); background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
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

    <!-- Edit Kendaraan Modal -->
    <div id="editKendaraanModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
        <div class="modal-content" style="background: white; padding: 2rem; border-radius: var(--radius-lg); width: 100%; max-width: 400px; box-shadow: var(--shadow-lg); margin: 1rem;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--text-main);">Edit Data Kendaraan</h3>
            <form id="editKendaraanForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.875rem;">Plat Nomor</label>
                    <input type="text" name="plate_number" id="editPlateNumber" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md); text-transform: uppercase;" required pattern="^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$" title="Contoh: B 1234 KKN">
                </div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.875rem;">Tipe Motor</label>
                    <select name="vehicle_type" id="editVehicleType" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md);" required>
                        <option value="Bebek">Bebek</option>
                        <option value="Matic">Matic</option>
                        <option value="Sport">Sport</option>
                    </select>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" onclick="closeEditKendaraanModal()" style="padding: 0.625rem 1.25rem; border: 1px solid var(--border); border-radius: var(--radius-md); background: white;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem; border-radius: var(--radius-md); background: var(--primary); color: white; border: none;">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditKendaraanModal(id, plate, type) {
            const form = document.getElementById('editKendaraanForm');
            form.action = `/kendaraan/${id}`;
            document.getElementById('editPlateNumber').value = plate;
            document.getElementById('editVehicleType').value = type;
            
            const modal = document.getElementById('editKendaraanModal');
            modal.style.display = 'flex';
        }

        function closeEditKendaraanModal() {
            document.getElementById('editKendaraanModal').style.display = 'none';
        }

        // Tutup modal jika klik di luar modal
        document.getElementById('editKendaraanModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditKendaraanModal();
            }
        });
    </script>
@endsection
