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
                           placeholder="Contoh: B1234KKN" 
                           pattern="^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$"
                           title="Format plat nomor tidak valid. Gunakan format seperti B 1234 KNJ (1–2 huruf, 3–4 angka, 2–3 huruf)."
                           value="{{ old('plate_number') }}" required style="text-transform: uppercase;">
                    @error('plate_number')
                        <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipe Motor -->
                <div class="form-group mb-6">
                    <label for="vehicle_type" class="form-label" style="font-weight: 600;">Tipe Motor</label>
                    <select id="vehicle_type" name="vehicle_type" class="form-control" required>
                        <option value="Matic" {{ old('vehicle_type') == 'Matic' ? 'selected' : '' }}>Matic</option>
                        <option value="Bebek" {{ old('vehicle_type') == 'Bebek' ? 'selected' : '' }}>Bebek</option>
                        <option value="Sport" {{ old('vehicle_type') == 'Sport' ? 'selected' : '' }}>Sport</option>
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
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Plat Nomor</th>
                            <th>Jenis Motor</th>
                            <th>Waktu Masuk</th>
                            <th>Durasi Saat Ini</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeTransactions as $transaction)
                        <tr>
                            <td style="font-weight: 600;">{{ $transaction->formatted_plate }}</td>
                            <td>{{ $transaction->vehicle_type }}</td>
                            <td>{{ $transaction->entry_time->format('H:i') }} <span style="font-size: 0.75rem;">WIB</span></td>
                            <td style="font-weight: 500;">{{ $transaction->formatted_duration }}</td>
                            <td>
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
        </style>
        <div id="ticketModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
            <div class="no-print" style="background-color: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 350px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                <div id="printableTicket" style="border: 2px dashed #d1d5db; padding: 1.5rem; margin-bottom: 1.5rem; border-radius: 8px; background-color: white;">
                    @if(isset($business) && $business->business_logo)
                        <img src="{{ asset('storage/' . $business->business_logo) }}" alt="Logo" style="height: 40px; margin-bottom: 0.5rem; object-fit: contain;">
                    @else
                        <i class="fas fa-motorcycle" style="font-size: 2rem; color: #9ca3af; margin-bottom: 0.5rem;"></i>
                    @endif
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase;">{{ $business->business_name ?? "Penitipan Parde's" }}</h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem; border-bottom: 1px dashed #d1d5db; padding-bottom: 1rem;">Tiket Parkir Motor</p>
                    
                    <div style="font-size: 2rem; font-weight: 800; letter-spacing: 2px; margin-bottom: 1rem; color: #1f2937;">
                        {{ $newTicket->formatted_plate }}
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #4b5563; margin-bottom: 0.5rem;">
                        <span>Tipe Motor:</span>
                        <span style="font-weight: 600;">{{ $newTicket->vehicle_type }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #4b5563; margin-bottom: 0.5rem;">
                        <span>Waktu Masuk:</span>
                        <span style="font-weight: 600;">{{ $newTicket->entry_time->format('d/m/Y H:i') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #4b5563;">
                        <span>Petugas:</span>
                        <span style="font-weight: 600;">{{ auth()->user()->name }}</span>
                    </div>
                    
                    <p style="font-size: 0.75rem; color: #9ca3af; margin-top: 1rem; border-top: 1px dashed #d1d5db; padding-top: 1rem; font-style: italic;">
                        Harap simpan karcis ini.<br>Kehilangan karcis akan dikenakan denda.
                    </p>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" onclick="document.getElementById('ticketModal').style.display='none'" class="btn btn-primary" style="padding: 0.5rem 2rem;">Tutup</button>
                </div>
            </div>
        </div>
    @endif
@endsection
