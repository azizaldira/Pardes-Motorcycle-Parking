@extends('layouts.app')

@section('content')
    <div style="max-width: 1050px; margin: 0 auto; padding: 1.5rem;">
        <div style="margin-bottom: 2rem;">
            @if(auth()->user()->role === 'admin')
                <h1 class="page-title" style="margin-bottom: 0.25rem; font-size: 1.85rem;">Pemantauan Kendaraan Masuk</h1>
                <p class="page-subtitle">Pantau daftar kendaraan yang sedang berstatus parkir aktif di area garasi secara real-time</p>
            @else
                <h1 class="page-title" style="margin-bottom: 0.25rem; font-size: 1.85rem;">Input Kendaraan Masuk</h1>
                <p class="page-subtitle">Catat kedatangan kendaraan ke area parkir dan cetak karcis otomatis</p>
            @endif
        </div>

        @if(session('success'))
            <div style="background-color: #17bc6fff; border: 1px solid #6ee7b7; color: #065f46; padding: 1.125rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 1.75rem; font-size: 0.95rem; display: flex; align-items: center; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);">
                <div style="background: #10b981; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0;">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <strong style="display: block; font-weight: 700;">Berhasil Mencatat Transaksi!</strong>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div style="background-color: #a41a1aff; border: 1px solid #f87171; color: #991b1b; padding: 1.125rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 1.75rem; font-size: 0.9rem; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1);">
                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-weight: 700; font-size: 1rem; color: #7f1d1d;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem; font-size: 1.25rem; color: #dc2626;"></i> 
                    Pencatatan Gagal Diteruskan
                </div>
                <ul style="margin: 0; padding-left: 1.5rem; color: #991b1b;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Widget Monitor Ketersediaan Slot Parkir -->
        <div style="background: {{ $percentage >= 100 ? 'linear-gradient(135deg, #ca1616ff 0%, #e61010ff 100%)' : ($percentage >= 90 ? 'linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%)' : ($percentage >= 80 ? 'linear-gradient(135deg, #fefce8 0%, #fef9c3 100%)' : 'linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%)')) }}; border: 1px solid {{ $percentage >= 100 ? '#f87171' : ($percentage >= 90 ? '#fb923c' : ($percentage >= 80 ? '#facc15' : '#34d399')) }}; padding: 1.5rem; border-radius: var(--radius-lg); margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 56px; height: 56px; border-radius: 12px; background: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <i class="fas {{ $percentage >= 100 ? 'fa-ban' : 'fa-parking' }}" style="font-size: 2rem; color: {{ $percentage >= 100 ? '#dc2626' : ($percentage >= 90 ? '#ea580c' : ($percentage >= 80 ? '#ca8a04' : '#059669')) }};"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-weight: 800; font-size: 1.15rem; color: #1e293b; letter-spacing: -0.02em;">
                        {{ $percentage >= 100 ? 'KAPASITAS PARKIR PENUH!' : ($percentage >= 90 ? 'KAPASITAS KRITIS (≥90%)' : ($percentage >= 80 ? 'AREA HAMPIR PENUH (≥80%)' : 'RUANG PARKIR TERSEDIA')) }}
                    </h3>
                    <p style="margin: 0.25rem 0 0; font-size: 0.9rem; color: #475569;">
                        Sisa ruang tersisa: <strong style="font-size: 1.05rem; color: #1e293b;">{{ $available_slots }} Slot</strong> dari total {{ $total_slots }} Slot global (Terisi {{ $percentage }}%).
                    </p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="background: white; padding: 0.5rem 1rem; border-radius: 9999px; border: 1px solid rgba(0,0,0,0.05); font-weight: 700; font-size: 0.85rem; color: #334155;">
                    Terisi: {{ $used_slots }} / {{ $total_slots }} Slot
                </div>
                @if($percentage >= 100)
                    <span style="background: #dc2626; color: white; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                        <i class="fas fa-times-circle" style="margin-right: 0.3rem;"></i> Ditutup
                    </span>
                @endif
            </div>
        </div>

        @if(auth()->user()->role === 'admin')
            <!-- Card Informasi Mode Pemantauan Khusus Admin -->
            <div class="table-card" style="padding: 2.25rem; margin-bottom: 2.5rem; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white; border-radius: var(--radius-lg); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; align-items: center; gap: 1.75rem; flex-wrap: wrap;">
                    <div style="background: rgba(255, 255, 255, 0.1); padding: 1.25rem; border-radius: 16px; display: flex; align-items: center; justify-content: center; width: 70px; height: 70px; border: 1px solid rgba(255, 255, 255, 0.15);">
                        <i class="fas fa-user-shield" style="font-size: 2.2rem; color: #60a5fa;"></i>
                    </div>
                    <div style="flex: 1; min-width: 280px;">
                        <span style="background: rgba(96, 165, 250, 0.2); color: #93c5fd; padding: 0.3rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; display: inline-block;">
                            Mode Read-Only / Monitor Only
                        </span>
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.35rem; font-weight: 800; color: #f8fafc; letter-spacing: -0.02em;">
                            Akses Pengawas (Admin)
                        </h3>
                        <p style="margin: 0; color: #cbd5e1; font-size: 0.95rem; line-height: 1.6;">
                            Proses input dan pencatatan kedatangan kendaraan masuk hanya dapat dilakukan oleh <strong style="color: #38bdf8;">Staff (Petugas Lapangan)</strong> demi menjaga integritas tanggung jawab operasional. Di halaman ini, Anda berwenang memantau histori kendaraan parkir aktif secara real-time.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <!-- Form Input Khusus Petugas -->
            <div class="table-card" style="padding: 2.5rem; margin-bottom: 2.5rem; background: white; border-radius: var(--radius-lg); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0,0,0,0.05);">
                <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1.25rem; margin-bottom: 1.75rem; display: flex; align-items: center; justify-content: space-between;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main); margin: 0;">
                        <i class="fas fa-plus-circle" style="color: #3b82f6; margin-right: 0.5rem;"></i> Formulir Kedatangan Motor
                    </h2>
                    <span style="font-size: 0.8rem; color: var(--text-muted); background: #f1f5f9; padding: 0.4rem 0.8rem; border-radius: 6px;">
                        Kolom bertanda <span style="color: #ef4444; font-weight: 700;">*</span> wajib diisi
                    </span>
                </div>

                <form action="{{ route('kendaraan-masuk.store') }}" method="POST">
                    @csrf

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.75rem; margin-bottom: 1.75rem;">
                        <!-- Plat Nomor -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="plate_number" class="form-label" style="font-weight: 700; color: #334155; display: block; margin-bottom: 0.5rem;">
                                Plat Nomor Kendaraan <span style="color: #ef4444;">*</span>
                            </label>
                            <div style="position: relative;">
                                <input type="text" id="plate_number" name="plate_number" 
                                       class="form-control @error('plate_number') is-invalid @enderror" 
                                       placeholder="Contoh: B 1234 KNJ" 
                                       value="{{ old('plate_number') }}" required 
                                       style="text-transform: uppercase; font-size: 1.15rem; font-weight: 700; padding: 0.875rem 1rem; letter-spacing: 2px;" 
                                       {{ $available_slots <= 0 ? 'disabled' : '' }}>
                            </div>
                            <span style="color: #64748b; font-size: 0.8rem; margin-top: 0.35rem; display: block;">
                                <i class="fas fa-info-circle"></i> Gunakan format plat Indonesia (1-2 huruf, 3-4 angka, 2-3 huruf)
                            </span>
                            @error('plate_number')
                                <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.35rem; display: block; font-weight: 600;">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Tipe Motor & Golongan -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="vehicle_type_id" class="form-label" style="font-weight: 700; color: #334155; display: block; margin-bottom: 0.5rem;">
                                Tipe & Golongan Motor <span style="color: #ef4444;">*</span>
                            </label>
                            <select id="vehicle_type_id" name="vehicle_type_id" class="form-control @error('vehicle_type_id') is-invalid @enderror" required style="padding: 0.875rem 1rem; font-size: 1rem; font-weight: 600; cursor: pointer; height: auto;" {{ $available_slots <= 0 ? 'disabled' : '' }}>
                                <option value="" disabled {{ old('vehicle_type_id') ? '' : 'selected' }}>-- Pilih Tipe & Golongan Motor --</option>
                                @foreach($vehicleTypes as $vt)
                                    <option value="{{ $vt->id }}" 
                                            data-name="{{ $vt->name }}"
                                            data-category="{{ $vt->category }}" 
                                            data-slot="{{ $vt->slot_required }}" 
                                            data-baserate="{{ $vt->tariff ? number_format($vt->tariff->base_rate, 0, ',', '.') : '0' }}" 
                                            data-hourlyrate="{{ $vt->tariff ? number_format($vt->tariff->hourly_rate, 0, ',', '.') : '0' }}" 
                                            data-overnightrate="{{ $vt->tariff ? number_format($vt->tariff->overnight_rate, 0, ',', '.') : '0' }}" 
                                            data-finerate="{{ $vt->tariff ? number_format($vt->tariff->lost_ticket_fine, 0, ',', '.') : '0' }}"
                                            {{ old('vehicle_type_id') == $vt->id ? 'selected' : '' }}>
                                        {{ $vt->name }} ({{ $vt->category }} - Butuh {{ $vt->slot_required }} Slot)
                                    </option>
                                @endforeach
                            </select>
                            <span style="color: #64748b; font-size: 0.8rem; margin-top: 0.35rem; display: block;">
                                <i class="fas fa-sync-alt"></i> Tarif dan konsumsi slot akan diisi otomatis sesuai jenis motor yang dipilih
                            </span>
                            @error('vehicle_type_id')
                                <span style="color: #ef4444; font-size: 0.85rem; margin-top: 0.35rem; display: block; font-weight: 600;">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- Dynamic Profile Card: Tipe Motor & Tarif yang Disorot -->
                    <div id="tariffInfoCard" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-left: 5px solid #3b82f6; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; transition: all 0.3s ease;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                            <div>
                                <h4 id="displayVehicleName" style="margin: 0; color: #1e293b; font-weight: 800; font-size: 1.15rem;">-</h4>
                                <span id="displayCategory" style="color: #64748b; font-size: 0.85rem; font-weight: 600;">-</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: #475569;">Kebutuhan Kapasitas:</span>
                                <span id="displaySlot" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 0.35rem 0.85rem; border-radius: 9999px; font-weight: 800; font-size: 0.85rem;">
                                    - Slot
                                </span>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                            <div style="background: white; padding: 0.875rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; display: block;">Tarif 6 Jam Pertama</span>
                                <strong id="displayBaseRate" style="color: #059669; font-size: 1.1rem; font-weight: 800;">Rp 0</strong>
                            </div>
                            <div style="background: white; padding: 0.875rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; display: block;">Tarif per Jam Berikut</span>
                                <strong id="displayHourlyRate" style="color: #2563eb; font-size: 1.1rem; font-weight: 800;">Rp 0</strong>
                            </div>
                            <div style="background: white; padding: 0.875rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; display: block;">Tarif Menginap (/Hari)</span>
                                <strong id="displayOvernightRate" style="color: #7c3aed; font-size: 1.1rem; font-weight: 800;">Rp 0</strong>
                            </div>
                            <div style="background: white; padding: 0.875rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; display: block;">Denda Tiket Hilang</span>
                                <strong id="displayFineRate" style="color: #dc2626; font-size: 1.1rem; font-weight: 800;">Rp 0</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1.25rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                        <div style="background: #22c55e; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h4 style="color: #166534; font-size: 0.95rem; font-weight: 700; margin-bottom: 0.25rem;">Pencatatan Bersifat Permanen & Otomatis</h4>
                            <p style="color: #15803d; font-size: 0.85rem; margin: 0; line-height: 1.4;">
                                Setelah dicatat, data tidak dapat diedit atau dihapus. Sistem akan merekam data kendaraan, mencetak jam masuk otomatis (<strong style="color: #14532d;">{{ date('H:i') }} WIB</strong>), dan mengikatkan identitas Anda (<strong style="color: #14532d;">{{ auth()->user()->name }}</strong>) sebagai petugas pembuat.
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1.125rem; font-size: 1.1rem; font-weight: 700; border-radius: var(--radius-md); box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); transition: all 0.2s;" {{ $available_slots <= 0 ? 'disabled' : '' }}>
                        <i class="fas fa-check-circle" style="margin-right: 0.5rem; font-size: 1.2rem;"></i> Catat Kendaraan & Terbitan Tiket
                    </button>
                </form>
            </div>
        @endif

        <!-- Tabel Kendaraan Parkir Aktif -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin: 0;">Kendaraan Parkir Aktif</h2>
                <p style="font-size: 0.875rem; color: #64748b; margin: 0.2rem 0 0 0;">Daftar seluruh kendaraan yang sedang berada di dalam area parkir saat ini</p>
            </div>
            <span style="background: #e2e8f0; color: #334155; padding: 0.4rem 0.9rem; border-radius: 9999px; font-weight: 700; font-size: 0.85rem;">
                <i class="fas fa-motorcycle" style="margin-right: 0.3rem; color: #475569;"></i> Total: {{ $activeTransactions->count() }} Kendaraan
            </span>
        </div>

        <div class="table-card" style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0,0,0,0.05); background: white;">
            <div class="table-responsive">
                <table class="table" style="margin-bottom: 0;">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 1rem 1.25rem; text-align: left; color: #475569; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Plat Nomor</th>
                            <th style="padding: 1rem 1.25rem; text-align: left; color: #475569; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Jenis Motor & Slot</th>
                            <th style="padding: 1rem 1.25rem; text-align: left; color: #475569; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Waktu Masuk & Durasi</th>
                            <th style="padding: 1rem 1.25rem; text-align: left; color: #475569; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Petugas Pencatat</th>
                            <th style="padding: 1rem 1.25rem; text-align: center; color: #475569; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            @if(auth()->user()->role === 'staff')
                            <th style="padding: 1rem 1.25rem; text-align: center; color: #475569; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeTransactions as $transaction)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='white'">
                            <td style="padding: 1.15rem 1.25rem;">
                                <div style="font-weight: 800; color: #1e293b; font-size: 1.1rem; letter-spacing: 1px;">
                                    {{ $transaction->formatted_plate }}
                                </div>
                            </td>
                            <td style="padding: 1.15rem 1.25rem;">
                                <div style="font-weight: 700; color: #334155; font-size: 0.95rem;">
                                    {{ $transaction->vehicle_type }}
                                </div>
                                <div style="margin-top: 0.35rem;">
                                    @php
                                        $slotCount = $transaction->slot_used ?? 1;
                                        $slotBg = $slotCount == 1 ? '#d1fae5' : ($slotCount == 2 ? '#fef3c7' : '#fee2e2');
                                        $slotColor = $slotCount == 1 ? '#065f46' : ($slotCount == 2 ? '#92400e' : '#991b1b');
                                    @endphp
                                    <span style="background: {{ $slotBg }}; color: {{ $slotColor }}; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 6px; display: inline-flex; align-items: center;">
                                        <i class="fas fa-layer-group" style="margin-right: 0.3rem; font-size: 0.7rem;"></i> {{ $slotCount }} Slot Digunakan
                                    </span>
                                </div>
                            </td>
                            <td style="padding: 1.15rem 1.25rem;">
                                <div style="font-weight: 700; color: #1e293b; font-size: 0.925rem;">
                                    <i class="far fa-clock" style="color: #64748b; margin-right: 0.35rem;"></i> {{ $transaction->entry_time->format('d/m/Y H:i') }} <span style="font-size: 0.75rem; color: #64748b;">WIB</span>
                                </div>
                                <div style="font-size: 0.8rem; font-weight: 600; color: #10b981; margin-top: 0.25rem; display: flex; align-items: center;">
                                    <span style="display: inline-block; width: 6px; height: 6px; background: #10b981; border-radius: 50%; margin-right: 0.4rem;"></span>
                                    {{ $transaction->formatted_duration }}
                                </div>
                            </td>
                            <td style="padding: 1.15rem 1.25rem;">
                                <div style="display: flex; align-items: center; gap: 0.6rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <span style="font-weight: 700; color: #334155; font-size: 0.9rem; display: block;">
                                            {{ $transaction->entryUser->name ?? 'System / Seeder' }}
                                        </span>
                                        <span style="font-size: 0.75rem; color: #64748b;">
                                            {{ ucfirst($transaction->entryUser->role ?? 'Petugas') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.15rem 1.25rem; text-align: center;">
                                <span style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center;">
                                    <span style="width: 6px; height: 6px; background: #10b981; border-radius: 50%; margin-right: 0.4rem; animation: pulse 2s infinite;"></span>
                                    Parkir Aktif
                                </span>
                            </td>
                            @if(auth()->user()->role === 'staff')
                            <td style="padding: 1.15rem 1.25rem; text-align: center;">
                                <a href="{{ route('kendaraan.karcis.pdf', $transaction->id) }}" target="_blank" class="btn btn-sm" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-weight: 700; padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center;">
                                    <i class="fas fa-print" style="margin-right: 0.35rem;"></i> Cetak Karcis
                                </a>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'staff' ? 6 : 5 }}" style="padding: 4rem 2rem; text-align: center; color: var(--text-muted);">
                                <div style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; font-size: 1.75rem;">
                                    <i class="fas fa-parking"></i>
                                </div>
                                <h4 style="font-size: 1.1rem; font-weight: 700; color: #475569; margin-bottom: 0.25rem;">Tidak Ada Kendaraan di Dalam Area</h4>
                                <p style="font-size: 0.875rem; margin: 0;">Semua area slot parkir saat ini dalam keadaan kosong.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script Interaktivitas Dynamic Profile Card Tarif -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectVehicle = document.getElementById('vehicle_type_id');
            const tariffCard = document.getElementById('tariffInfoCard');

            if (selectVehicle && tariffCard) {
                function updateTariffCard() {
                    const selectedOption = selectVehicle.options[selectVehicle.selectedIndex];
                    
                    if (selectedOption && selectedOption.value) {
                        document.getElementById('displayVehicleName').textContent = selectedOption.getAttribute('data-name') || '-';
                        document.getElementById('displayCategory').textContent = selectedOption.getAttribute('data-category') || '-';
                        document.getElementById('displaySlot').textContent = (selectedOption.getAttribute('data-slot') || '1') + ' Slot';
                        document.getElementById('displayBaseRate').textContent = 'Rp ' + (selectedOption.getAttribute('data-baserate') || '0');
                        document.getElementById('displayHourlyRate').textContent = 'Rp ' + (selectedOption.getAttribute('data-hourlyrate') || '0');
                        document.getElementById('displayOvernightRate').textContent = 'Rp ' + (selectedOption.getAttribute('data-overnightrate') || '0');
                        document.getElementById('displayFineRate').textContent = 'Rp ' + (selectedOption.getAttribute('data-finerate') || '0');
                        
                        tariffCard.style.display = 'block';
                    } else {
                        tariffCard.style.display = 'none';
                    }
                }

                selectVehicle.addEventListener('change', updateTariffCard);
                
                // Trigger on page load in case of old inputs / back button
                if (selectVehicle.value) {
                    updateTariffCard();
                }
            }
        });
    </script>

    <!-- Modal Cetak Tiket Sementara -->
    @if(isset($newTicket))
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }
                #printableTicket, #printableTicket * {
                    visibility: visible;
                }
                #printableTicket {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    max-width: 320px;
                    border: none !important;
                    padding: 0 !important;
                }
                #ticketModal {
                    position: absolute;
                    left: 0;
                    top: 0;
                    background: transparent !important;
                }
                .no-print {
                    display: none !important;
                }
            }
            @keyframes popIn {
                from { transform: scale(0.9); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
        </style>
        <div id="ticketModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.75); z-index: 1000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
            <div class="no-print" style="background-color: white; padding: 2.25rem 2rem; border-radius: 20px; width: 100%; max-width: 380px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35); animation: popIn 0.2s ease-out; border-top: 6px solid #10b981;">
                
                <div style="width: 54px; height: 54px; background: #ecfdf5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; font-size: 1.5rem;">
                    <i class="fas fa-check"></i>
                </div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin-bottom: 0.25rem;">Tiket Berhasil Terbit!</h3>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Kendaraan telah terdaftar ke sistem parkir aktif</p>

                <!-- Area Karcis yang Siap Cetak (Printable) -->
                <div id="printableTicket" style="border: 2px dashed #cbd5e1; padding: 1.5rem; margin-bottom: 1.75rem; border-radius: 12px; background-color: #f8fafc; text-align: center; position: relative;">
                    @if(isset($business) && $business->business_logo)
                        <img src="{{ asset('storage/' . $business->business_logo) }}" alt="Logo" style="height: 40px; margin-bottom: 0.5rem; object-fit: contain;">
                    @else
                        <div style="font-size: 1.75rem; color: #3b82f6; margin-bottom: 0.25rem;"><i class="fas fa-motorcycle"></i></div>
                    @endif
                    <h4 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 0.25rem; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px;">{{ $business->business_name ?? "Penitipan Parde's" }}</h4>
                    <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 1.25rem; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.75rem;">TIKET PARKIR KENDARAAN</p>
                    
                    <div style="font-size: 2.25rem; font-weight: 900; letter-spacing: 3px; margin-bottom: 1.25rem; color: #0f172a; line-height: 1;">
                        {{ $newTicket->formatted_plate }}
                    </div>
                    
                    <div style="text-align: left; font-size: 0.85rem; color: #334155; background: white; padding: 0.875rem; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #64748b;">Jenis Motor:</span>
                            <strong style="color: #1e293b;">{{ $newTicket->vehicle_type }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #64748b;">Kapasitas Slot:</span>
                            <strong style="color: #2563eb;">{{ $newTicket->slot_used ?? 1 }} Slot</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #64748b;">Jam Masuk:</span>
                            <strong style="color: #1e293b;">{{ $newTicket->entry_time->format('d/m/Y H:i') }} WIB</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Petugas:</span>
                            <strong style="color: #1e293b;">{{ $newTicket->entryUser->name ?? auth()->user()->name }}</strong>
                        </div>
                    </div>
                    
                    <p style="font-size: 0.7rem; color: #64748b; margin: 0; line-height: 1.4; font-style: italic;">
                        * Harap simpan karcis ini dengan baik.<br>
                        Kehilangan karcis akan dikenakan denda resmi.
                    </p>
                </div>
                
                <div style="display: flex; gap: 0.6rem; justify-content: center; flex-wrap: wrap;">
                    @if(auth()->user()->role === 'staff')
                    <a href="{{ route('kendaraan.karcis.pdf', $newTicket->id) }}" target="_blank" class="btn" style="flex: 1; padding: 0.75rem 1rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; font-weight: 700; border-radius: 10px; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-print" style="margin-right: 0.4rem;"></i> Cetak Karcis
                    </a>
                    @endif
                    <button type="button" onclick="document.getElementById('ticketModal').style.display='none'" class="btn btn-primary" style="flex: 1.2; padding: 0.75rem 1.25rem; font-weight: 700; border-radius: 10px; font-size: 0.95rem; cursor: pointer;">
                        <i class="fas fa-check" style="margin-right: 0.4rem;"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
@endsection
