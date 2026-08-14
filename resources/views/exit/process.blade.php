@extends('layouts.app')

@section('content')
<div style="max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem;">
    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title" style="margin-bottom: 0.25rem; font-size: 1.85rem;">Konfirmasi & Rincian Kendaraan Keluar</h1>
            <p class="page-subtitle" style="color: #64748b; margin: 0;">Verifikasi transparan perhitungan tarif parkir motor sebelum penyelesaian transaksi</p>
        </div>
        <a href="{{ route('kendaraan-keluar') }}" class="btn" style="background: white; border: 1px solid #cbd5e1; color: #475569; padding: 0.75rem 1.25rem; border-radius: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <i class="fas fa-arrow-left"></i> Kembali ke Cari Kendaraan
        </a>
    </div>

    @if(session('success'))
    <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 5px solid #16a34a; border-right: 1px solid #bbf7d0; border-top: 1px solid #bbf7d0; border-bottom: 1px solid #bbf7d0; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.08);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: #bbf7d0; color: #15803d; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <h4 style="margin: 0 0 0.25rem 0; font-weight: 800; font-size: 1.1rem; color: #166534;">Pembayaran Berhasil!</h4>
                <p style="margin: 0; font-size: 0.95rem; color: #15803d; font-weight: 600;">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div style="background: linear-gradient(135deg, #fef2f2 0%, #fff1f2 100%); border-left: 5px solid #ef4444; border-right: 1px solid #fecdd3; border-top: 1px solid #fecdd3; border-bottom: 1px solid #fecdd3; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.08);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: #fee2e2; color: #dc2626; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h4 style="margin: 0 0 0.25rem 0; font-weight: 800; font-size: 1.1rem; color: #991b1b;">Pembayaran Gagal / Ditolak</h4>
                <p style="margin: 0; font-size: 0.95rem; color: #7f1d1d; font-weight: 600;">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #f59e0b; border-right: 1px solid #fde68a; border-top: 1px solid #fde68a; border-bottom: 1px solid #fde68a; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.08);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: #fde68a; color: #b45309; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <h4 style="margin: 0 0 0.25rem 0; font-weight: 800; font-size: 1.1rem; color: #92400e;">Informasi Status Pembayaran</h4>
                <p style="margin: 0; font-size: 0.95rem; color: #b45309; font-weight: 600;">{{ session('warning') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(!empty($canCancel) && $canCancel)
    <!-- ALERT OPSI PEMBATALAN 15 MENIT -->
    <div style="background: linear-gradient(135deg, #fef2f2 0%, #fff1f2 100%); border-left: 5px solid #ef4444; border-right: 1px solid #fecdd3; border-top: 1px solid #fecdd3; border-bottom: 1px solid #fecdd3; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.08);">
        <div style="display: flex; align-items: flex-start; gap: 1rem; max-width: 650px;">
            <div style="background: #fee2e2; color: #dc2626; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <h4 style="margin: 0 0 0.35rem 0; font-weight: 800; font-size: 1.1rem; color: #991b1b;">Kendaraan Baru Masuk &le; 15 Menit yang Lalu!</h4>
                <p style="margin: 0; font-size: 0.925rem; color: #7f1d1d; line-height: 1.5;">
                    Usia parkir saat ini adalah <strong>{{ $formattedDuration }}</strong> (di bawah batas maksimal 15 menit). Apabila pengendara batal parkir atau salah masuk, Anda diizinkan membatalkan transaksi ini seharga <strong>Rp 0</strong> dan mengosongkan kembali slot garasi.
                </p>
            </div>
        </div>
        <form action="{{ route('kendaraan-keluar.cancel', $transaction->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin MEMBATALKAN transaksi kendaraan berplat {{ $transaction->formatted_plate }}? Biaya akan diset Rp 0 dan kapasitas slot garasi dipulihkan.')">
            @csrf
            <button type="submit" class="btn" style="background: #dc2626; color: white; border: none; padding: 0.85rem 1.5rem; border-radius: 10px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3); display: inline-flex; align-items: center; gap: 0.5rem; transition: background 0.2s;">
                <i class="fas fa-ban"></i> Batalkan Transaksi (Rp 0)
            </button>
        </form>
    </div>
    @endif

    <!-- GRID TRANSAKSI -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 2rem; align-items: start;">
        
        <!-- KOLOM KIRI: PROFIL & JEJAK WAKTU KENDARAAN -->
        <div>
            <!-- Profil Motor -->
            <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); padding: 1.75rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px dashed #cbd5e1;">
                    <div style="background: #eff6ff; color: #3b82f6; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: #1e293b;">Profil & Jejak Kendaraan</h3>
                        <span style="font-size: 0.8rem; color: #64748b;">Informasi registrasi saat motor memasuki area parkir</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                    <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Plat Nomor Kendaraan</span>
                    <span style="font-size: 1.35rem; font-weight: 900; color: #0f172a; letter-spacing: 1.5px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.25rem 0.75rem; border-radius: 8px;">
                        {{ $transaction->formatted_plate }}
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                    <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Jenis Motor</span>
                    <span style="font-size: 1.05rem; font-weight: 700; color: #1e293b;">{{ $transaction->vehicle_type }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                    <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Golongan Kendaraan</span>
                    <span style="background: #eff6ff; color: #1d4ed8; font-size: 0.85rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 20px; border: 1px solid #bfdbfe;">
                        <i class="fas fa-layer-group" style="margin-right: 0.3rem;"></i> {{ $tariff->category ?? 'Golongan I' }}
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                    <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Slot Parkir Digunakan</span>
                    <span style="background: #f5f3ff; color: #6d28d9; font-size: 0.85rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 20px; border: 1px solid #ddd6fe;">
                        <i class="fas fa-parking" style="margin-right: 0.3rem;"></i> {{ $transaction->slot_used }} Slot
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                    <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Metode Pencarian Keluar</span>
                    <span style="background: #f0fdf4; color: #15803d; font-size: 0.85rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 20px; border: 1px solid #bbf7d0;">
                        <i class="fas {{ str_contains(strtolower($searchMethod ?? ''), 'camera') ? 'fa-camera' : (str_contains(strtolower($searchMethod ?? ''), 'upload') ? 'fa-file-image' : 'fa-keyboard') }}" style="margin-right: 0.3rem;"></i>
                        {{ $searchMethod ?? 'Input Manual Plat' }}
                    </span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0;">
                    <span style="color: #64748b; font-size: 0.9rem; font-weight: 600;">Petugas Mencatat Masuk</span>
                    <span style="font-size: 0.95rem; font-weight: 700; color: #334155;">
                        <i class="fas fa-user-shield" style="margin-right: 0.3rem; color: #94a3b8;"></i> {{ $transaction->entryUser->name ?? 'System' }}
                    </span>
                </div>
            </div>

            <!-- Linimasa & Durasi -->
            <div style="background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border-radius: 16px; border: 1px solid #93c5fd; padding: 1.75rem; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.08);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div style="background: #3b82f6; color: white; width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #1e3a8a;">Linimasa & Durasi Aktual</h3>
                        <span style="font-size: 0.8rem; color: #2563eb; font-weight: 600;">Perhitungan waktu berjalan otomatis</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="background: white; padding: 1rem; border-radius: 12px; border: 1px solid #dbeafe;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Waktu Masuk</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #1e293b; margin-top: 0.3rem;">{{ $transaction->entry_time->format('d M Y') }}</div>
                        <div style="font-size: 0.85rem; color: #334155; font-weight: 600;">{{ $transaction->entry_time->format('H:i') }} <span style="font-size: 0.75rem; color: #64748b;">WIB</span></div>
                    </div>
                    <div style="background: white; padding: 1rem; border-radius: 12px; border: 1px solid #dbeafe;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Waktu Keluar</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #16a34a; margin-top: 0.3rem;">{{ now()->format('d M Y') }}</div>
                        <div style="font-size: 0.85rem; color: #15803d; font-weight: 700;">{{ now()->format('H:i') }} <span style="font-size: 0.75rem; color: #64748b;">WIB</span></div>
                    </div>
                </div>

                <div style="background: white; border: 2px solid #3b82f6; border-radius: 12px; padding: 1.25rem; text-align: center;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #3b82f6; text-transform: uppercase; margin-bottom: 0.25rem;">Durasi Parkir Aktual</div>
                    <div style="font-size: 1.75rem; font-weight: 900; color: #1e3a8a;">{{ $formattedDuration }}</div>
                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.2rem;">Total {{ $feeDetails['duration_minutes'] }} menit berlalu di dalam garasi</div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: RINCIAN BIAYA & CHECKOUT -->
        <div>
            <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.04); padding: 2rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 2px dashed #e2e8f0;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="background: #f0fdf4; color: #16a34a; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800; color: #1e293b;">Rincian Perhitungan Biaya</h3>
                            <span style="font-size: 0.85rem; color: #64748b;">Kalkulasi transparan sesuai aturan tarif golongan</span>
                        </div>
                    </div>
                </div>

                <!-- Tag Tarif yang Digunakan -->
                <div style="background: #f8fafc; border-left: 4px solid #3b82f6; padding: 0.85rem 1.15rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Tarif yang Digunakan</div>
                        <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-top: 0.15rem;">{{ $feeDetails['tarif_applied_name'] ?? 'Tarif Golongan Normal' }}</div>
                    </div>
                    <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #3b82f6;"></i>
                </div>

                <!-- Rincian Biaya -->
                <div style="margin-bottom: 1.5rem;">
                    @if($feeDetails['overnight_count'] > 0)
                        <!-- Kondisi Menginap: Tarif 6 jam & per jam TIDAK dihitung -->
                        <div style="display: flex; justify-content: space-between; padding: 0.65rem 0; font-size: 0.95rem;">
                            <span style="color: #475569; font-weight: 600;">Tarif Menginap ({{ $feeDetails['overnight_count'] }} Malam &times; Rp {{ number_format($tariff->overnight_rate, 0, ',', '.') }})</span>
                            <span style="font-weight: 700; color: #1e293b;">Rp {{ number_format($feeDetails['overnight_fee'], 0, ',', '.') }}</span>
                        </div>
                    @else
                        <!-- Kondisi Tidak Menginap: Base rate 6 jam + Tambahan Jam -->
                        <div style="display: flex; justify-content: space-between; padding: 0.65rem 0; font-size: 0.95rem;">
                            <span style="color: #475569; font-weight: 600;">Tarif 6 Jam Pertama (Base Rate)</span>
                            <span style="font-weight: 700; color: #1e293b;">Rp {{ number_format($feeDetails['base_fee'], 0, ',', '.') }}</span>
                        </div>

                        @if($feeDetails['extra_hours'] > 0)
                        <div style="display: flex; justify-content: space-between; padding: 0.65rem 0; font-size: 0.95rem;">
                            <span style="color: #475569; font-weight: 600;">Tambahan Waktu ({{ $feeDetails['extra_hours'] }} Jam &times; Rp {{ number_format($tariff->hourly_rate, 0, ',', '.') }})</span>
                            <span style="font-weight: 700; color: #1e293b;">Rp {{ number_format($feeDetails['hourly_fee'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                    @endif

                    <!-- Denda Hilang Karcis (Muncul Dinamis via JS) -->
                    <div id="fineRow" style="display: none; justify-content: space-between; padding: 0.65rem 0; font-size: 0.95rem; color: #dc2626; border-top: 1px dashed #fecdd3; margin-top: 0.5rem;">
                        <span style="font-weight: 700;"><i class="fas fa-exclamation-triangle" style="margin-right: 0.3rem;"></i> Denda Kehilangan Karcis</span>
                        <span style="font-weight: 800;">Rp {{ number_format($tariff->lost_ticket_fine, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- TOTAL BIAYA DISPLAY -->
                <div style="background: #ecfdf5; border: 2px solid #86efac; border-radius: 12px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <div>
                        <span style="font-size: 0.8rem; font-weight: 800; color: #047857; text-transform: uppercase; display: block;">Total Biaya Parkir</span>
                        <span style="font-size: 0.8rem; color: #065f46;">Siap dibayarkan & kembalikan slot</span>
                    </div>
                    <div id="totalDisplay" style="font-size: 1.85rem; font-weight: 900; color: #064e3b; letter-spacing: 0.5px;">
                        Rp {{ number_format($feeDetails['total_before_fine'], 0, ',', '.') }}
                    </div>
                </div>

                <!-- FORM CHECKOUT -->
                <form id="checkoutForm" action="{{ route('kendaraan-keluar.checkout', $transaction->id) }}" method="POST">
                    @csrf
                    
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; cursor: pointer; margin: 0; font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                            <input type="checkbox" name="lost_ticket" id="lost_ticket" value="1" style="width: 1.25rem; height: 1.25rem; margin-right: 0.85rem; accent-color: #ef4444; cursor: pointer;">
                            <div>
                                <span>Centang jika <strong>Karcis Parkir Hilang / Tidak Dipinjamkan</strong></span>
                                <span style="display: block; font-size: 0.8rem; color: #ef4444; font-weight: 700; margin-top: 0.1rem;">+ Denda Rp {{ number_format($tariff->lost_ticket_fine, 0, ',', '.') }} sesuai aturan golongan ini</span>
                            </div>
                        </label>
                    </div>

                    <div class="form-group mb-4">
                        <label for="payment_method" class="form-label" style="font-weight: 700; color: #334155; margin-bottom: 0.5rem; display: block;">
                            <i class="fas fa-wallet" style="color: #3b82f6; margin-right: 0.4rem;"></i> Pilih Metode Pembayaran
                        </label>
                        <select name="payment_method" id="payment_method" class="form-control" style="padding: 0.85rem 1rem; border-radius: 10px; font-size: 1rem; font-weight: 700; border: 2px solid #cbd5e1; color: #1e293b; background-color: white;" required>
                            <option value="Tunai">Tunai (Cash)</option>
                            <option value="QRIS">QRIS / Pembayaran Digital (Midtrans Sandbox)</option>
                        </select>
                        <span id="payment_helper_text" style="font-size: 0.8rem; color: #64748b; margin-top: 0.4rem; display: block;">
                            *Pilihan <strong>Tunai</strong> akan langsung memproses selesai transaksi & memulihkan slot garasi.
                        </span>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-primary" style="width: 100%; padding: 1.1rem; font-size: 1.15rem; font-weight: 800; border-radius: 12px; background: #16a34a; border: 1px solid #15803d; box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3); display: flex; align-items: center; justify-content: center; gap: 0.75rem; transition: all 0.2s;">
                        <i class="fas fa-check-circle" style="font-size: 1.3rem;"></i> <span>Konfirmasi Selesai & Keluar Motor</span>
                    </button>
                </form>

                <!-- PANEL SINKRONISASI STATUS & STRUK MIDTRANS (TAHAP 8D) -->
                <div id="midtransSyncPanel" style="display: {{ $transaction->payment_method === 'QRIS' || $transaction->midtrans_order_id ? 'block' : 'none' }}; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 14px; padding: 1.5rem; margin-top: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div>
                            <span style="font-size: 0.8rem; font-weight: 800; color: #475569; text-transform: uppercase; display: block;"><i class="fas fa-sync-alt" style="color: #3b82f6;"></i> Sinkronisasi Midtrans Status API</span>
                            <span style="font-size: 0.8rem; color: #64748b;">Pengganti Webhook untuk Lokal (Without Ngrok)</span>
                        </div>
                        <!-- Lencana Status -->
                        <span id="badgePaymentStatus" style="background: {{ $transaction->payment_status === 'paid' ? '#dcfce7' : '#fef3c7' }}; color: {{ $transaction->payment_status === 'paid' ? '#16a34a' : '#d97706' }}; border: 1px solid {{ $transaction->payment_status === 'paid' ? '#86efac' : '#fde68a' }}; padding: 0.4rem 0.85rem; border-radius: 50px; font-size: 0.85rem; font-weight: 800; text-transform: uppercase;">
                            {{ $transaction->payment_status ?? 'PENDING' }}
                        </span>
                    </div>

                    <div style="font-size: 0.85rem; color: #475569; background: white; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
                        <div><strong>Order ID:</strong> <span id="displayOrderId">{{ $transaction->midtrans_order_id ?? 'Belum terbit (Klik Bayar dengan QRIS)' }}</span></div>
                        <div style="margin-top: 0.3rem;"><strong>Tx ID:</strong> <span id="displayTxId">{{ $transaction->midtrans_transaction_id ?? '-' }}</span></div>
                    </div>

                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" id="btnCheckStatus" class="btn btn-info" style="flex: 1; padding: 0.75rem; font-weight: 800; font-size: 0.9rem; border-radius: 10px; background: #0284c7; border: 1px solid #0369a1; color: white; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" {{ empty($transaction->midtrans_order_id) ? 'disabled' : '' }}>
                            <i class="fas fa-sync-alt id-spin-icon"></i> <span>Cek Status Pembayaran</span>
                        </button>
                        <button type="button" id="btnPrintReceipt" class="btn btn-secondary" style="flex: 1; padding: 0.75rem; font-weight: 800; font-size: 0.9rem; border-radius: 10px; background: {{ $transaction->payment_status === 'paid' ? '#16a34a' : '#94a3b8' }}; border: 1px solid {{ $transaction->payment_status === 'paid' ? '#15803d' : '#64748b' }}; color: white; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: {{ $transaction->payment_status === 'paid' ? 'pointer' : 'not-allowed' }}; opacity: {{ $transaction->payment_status === 'paid' ? '1' : '0.65' }};" {{ $transaction->payment_status !== 'paid' ? 'disabled' : '' }}>
                            <i class="fas fa-print"></i> <span>Cetak Struk Keluar</span>
                        </button>
                    </div>

                    <div id="syncStatusHelperText" style="font-size: 0.8rem; color: #64748b; margin-top: 0.85rem; text-align: center;">
                        @if($transaction->payment_status === 'paid')
                            <i class="fas fa-check-circle" style="color: #16a34a;"></i> Status <strong>PAID</strong> terkonfirmasi! (Cetak Struk PDF diimplementasikan pada Tahap 9)
                        @else
                            *Tekan tombol <strong>Cek Status Pembayaran</strong> setelah pelanggan melunasi QRIS di aplikasi untuk memperbarui status secara otomatis.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL NOTIFIKASI CALLBACK & SINKRONISASI MIDTRANS SNAP (TAHAP 8C & 8D) -->
<div id="midtransCallbackModal" style="display: none; position: fixed; z-index: 99999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: white; width: 100%; max-width: 540px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); border: 1px solid #e2e8f0;">
        <div id="modalHeaderBg" style="background: linear-gradient(135deg, #1e293b, #0f172a); padding: 2.25rem 1.75rem 1.75rem; text-align: center; color: white; position: relative;">
            <div id="modalIconContainer" style="width: 80px; height: 80px; margin: 0 auto 1.25rem; border-radius: 50%; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);">
                <i id="modalIcon" class="fas fa-info-circle" style="font-size: 3rem; color: white;"></i>
            </div>
            <h3 id="modalTitle" style="margin: 0; font-size: 1.45rem; font-weight: 800; letter-spacing: -0.025em; color: white;">Notifikasi Pembayaran</h3>
            <span id="modalBadge" style="display: inline-block; margin-top: 0.6rem; background: rgba(255,255,255,0.25); padding: 0.3rem 0.85rem; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: white;">Midtrans Sandbox Callback</span>
        </div>
        
        <div style="padding: 1.75rem;">
            <div id="modalMessage" style="font-size: 1.05rem; line-height: 1.6; color: #334155; font-weight: 600; text-align: center; margin-bottom: 1.4rem;">
                <!-- Isi pesan callback dimuat melalui JavaScript -->
            </div>

            <div id="modalDetailsBox" style="display: none; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 1rem; font-size: 0.875rem; color: #475569; margin-bottom: 1.5rem; text-align: left;">
                <div style="font-weight: 800; margin-bottom: 0.5rem; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.4rem; display: flex; align-items: center; justify-content: space-between;">
                    <span><i class="fas fa-receipt text-primary" style="margin-right: 0.4rem;"></i> Rincian Data Callback Midtrans:</span>
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">JSON Object</span>
                </div>
                <div id="modalDetailsContent" style="font-family: 'Courier New', Courier, monospace; font-size: 0.825rem; white-space: pre-wrap; overflow-x: auto; max-height: 160px; color: #0f172a; background: white; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;"></div>
            </div>

            <button type="button" onclick="closeMidtransModal()" id="btnModalClose" class="btn" style="width: 100%; background: #334155; color: white; border: none; padding: 1rem; border-radius: 12px; font-size: 1.05rem; font-weight: 800; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 10px rgba(51, 65, 85, 0.3); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fas fa-check-circle"></i> Mengerti & Tutup Notifikasi
            </button>
        </div>
    </div>
</div>

<!-- INTEGRASI LIBRARY MIDTRANS SNAP -->
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

<!-- SCRIPT HITUNG TOTAL REAL-TIME & PENANGANAN LENGKAP MIDTRANS SNAP POPUP -->
<script>
    window.qrisSuccess = false;

    function showMidtransModal(type, title, message, result, customBadge = null) {
        const modal = document.getElementById('midtransCallbackModal');
        const headerBg = document.getElementById('modalHeaderBg');
        const iconContainer = document.getElementById('modalIconContainer');
        const icon = document.getElementById('modalIcon');
        const titleEl = document.getElementById('modalTitle');
        const badgeEl = document.getElementById('modalBadge');
        const msgEl = document.getElementById('modalMessage');
        const detailsBox = document.getElementById('modalDetailsBox');
        const detailsContent = document.getElementById('modalDetailsContent');
        const btnClose = document.getElementById('btnModalClose');
        const checkoutForm = document.getElementById('checkoutForm');

        titleEl.innerText = title;
        msgEl.innerHTML = message;

        // Reset default tombol close dan state
        btnClose.innerHTML = '<i class="fas fa-check-circle"></i> <span>Mengerti & Tutup Notifikasi</span>';
        btnClose.onclick = window.closeMidtransModal;
        btnClose.disabled = false;
        btnClose.style.cursor = 'pointer';

        if (type === 'success') {
            window.qrisSuccess = true;
            const pmSelect = document.getElementById('payment_method');
            if (pmSelect) pmSelect.value = 'QRIS';

            headerBg.style.background = 'linear-gradient(135deg, #16a34a, #15803d)';
            iconContainer.style.background = 'rgba(255, 255, 255, 0.2)';
            icon.className = 'fas fa-check-circle';
            badgeEl.innerText = customBadge || 'PEMBAYARAN DIGITAL LUNAS';
            
            btnClose.style.background = '#15803d';
            btnClose.style.boxShadow = '0 4px 12px rgba(21, 128, 61, 0.35)';
            btnClose.innerHTML = '<i class="fas fa-arrow-right"></i> <span>Menuju Halaman Kendaraan Keluar Sekarang</span>';
            btnClose.onclick = function() {
                if (checkoutForm) {
                    btnClose.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Mengarahkan...</span>';
                    btnClose.disabled = true;
                    HTMLFormElement.prototype.submit.call(checkoutForm);
                }
            };

            // Otomatis mengalihkan (submit form checkout) dalam 1.8 detik
            setTimeout(function() {
                if (window.qrisSuccess && checkoutForm) {
                    HTMLFormElement.prototype.submit.call(checkoutForm);
                }
            }, 1800);
        } else if (type === 'pending') {
            window.qrisSuccess = false;
            headerBg.style.background = 'linear-gradient(135deg, #d97706, #b45309)';
            iconContainer.style.background = 'rgba(255, 255, 255, 0.2)';
            icon.className = 'fas fa-clock';
            badgeEl.innerText = customBadge || 'Callback: onPending / PENDING';
            btnClose.style.background = '#d97706';
            btnClose.style.boxShadow = '0 4px 12px rgba(217, 119, 6, 0.3)';
        } else if (type === 'error') {
            window.qrisSuccess = false;
            headerBg.style.background = 'linear-gradient(135deg, #dc2626, #b91c1c)';
            iconContainer.style.background = 'rgba(255, 255, 255, 0.2)';
            icon.className = 'fas fa-exclamation-triangle';
            badgeEl.innerText = customBadge || 'Callback: onError / FAILED';
            btnClose.style.background = '#dc2626';
            btnClose.style.boxShadow = '0 4px 12px rgba(220, 38, 38, 0.3)';
        } else if (type === 'info') {
            window.qrisSuccess = false;
            headerBg.style.background = 'linear-gradient(135deg, #0284c7, #0369a1)';
            iconContainer.style.background = 'rgba(255, 255, 255, 0.2)';
            icon.className = 'fas fa-info-circle';
            badgeEl.innerText = customBadge || 'Informasi Status / Struk';
            btnClose.style.background = '#0284c7';
            btnClose.style.boxShadow = '0 4px 12px rgba(2, 132, 199, 0.3)';
        } else {
            window.qrisSuccess = false;
            headerBg.style.background = 'linear-gradient(135deg, #475569, #334155)';
            iconContainer.style.background = 'rgba(255, 255, 255, 0.15)';
            icon.className = 'fas fa-times-circle';
            badgeEl.innerText = customBadge || 'Callback: onClose';
            btnClose.style.background = '#475569';
            btnClose.style.boxShadow = '0 4px 12px rgba(71, 85, 105, 0.3)';
        }

        if (result && typeof result === 'object') {
            detailsBox.style.display = 'block';
            detailsContent.innerText = JSON.stringify(result, null, 2);
        } else {
            detailsBox.style.display = 'none';
        }

        modal.style.display = 'flex';
    }

    window.closeMidtransModal = function() {
        document.getElementById('midtransCallbackModal').style.display = 'none';
    };

    document.addEventListener('DOMContentLoaded', function() {
        const lostTicketCheckbox = document.getElementById('lost_ticket');
        const fineRow = document.getElementById('fineRow');
        const totalDisplay = document.getElementById('totalDisplay');
        const paymentMethodSelect = document.getElementById('payment_method');
        const btnSubmit = document.getElementById('btnSubmit');
        const paymentHelperText = document.getElementById('payment_helper_text');
        const checkoutForm = document.getElementById('checkoutForm');

        // Komponen Tahap 8D
        const midtransSyncPanel = document.getElementById('midtransSyncPanel');
        const btnCheckStatus = document.getElementById('btnCheckStatus');
        const btnPrintReceipt = document.getElementById('btnPrintReceipt');
        const badgePaymentStatus = document.getElementById('badgePaymentStatus');
        const displayOrderId = document.getElementById('displayOrderId');
        const displayTxId = document.getElementById('displayTxId');
        const syncStatusHelperText = document.getElementById('syncStatusHelperText');
        
        const baseTotal = {{ $feeDetails['total_before_fine'] }};
        const fineAmount = {{ $tariff->lost_ticket_fine }};

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        lostTicketCheckbox.addEventListener('change', function() {
            if (this.checked) {
                fineRow.style.display = 'flex';
                totalDisplay.innerText = 'Rp ' + formatRupiah(baseTotal + fineAmount);
            } else {
                fineRow.style.display = 'none';
                totalDisplay.innerText = 'Rp ' + formatRupiah(baseTotal);
            }
        });

        // Perubahan tampilan tombol & info berdasarkan metode pembayaran
        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'QRIS') {
                btnSubmit.style.background = '#2563eb';
                btnSubmit.style.borderColor = '#1d4ed8';
                btnSubmit.style.boxShadow = '0 4px 15px rgba(37, 99, 235, 0.35)';
                btnSubmit.innerHTML = '<i class="fas fa-qrcode" style="font-size: 1.3rem;"></i> <span>Bayar dengan QRIS</span>';
                paymentHelperText.innerHTML = '*Pilihan <strong>QRIS</strong> akan membuka popup pembayaran Midtrans Snap Sandbox dengan handling callback lengkap.';
                if (midtransSyncPanel) midtransSyncPanel.style.display = 'block';
            } else {
                btnSubmit.style.background = '#16a34a';
                btnSubmit.style.borderColor = '#15803d';
                btnSubmit.style.boxShadow = '0 4px 15px rgba(22, 163, 74, 0.3)';
                btnSubmit.innerHTML = '<i class="fas fa-check-circle" style="font-size: 1.3rem;"></i> <span>Konfirmasi Selesai & Keluar Motor</span>';
                paymentHelperText.innerHTML = '*Pilihan <strong>Tunai</strong> akan langsung memproses selesai transaksi & memulihkan slot garasi.';
                if (midtransSyncPanel && !('{{ $transaction->midtrans_order_id }}' !== '')) {
                    midtransSyncPanel.style.display = 'none';
                }
            }
        });

        // Penanganan pemanggilan Snap Popup & Callbacks saat form disubmit (jika QRIS)
        checkoutForm.addEventListener('submit', function(e) {
            if (paymentMethodSelect.value === 'QRIS' && !window.qrisSuccess) {
                e.preventDefault();

                // Ubah state tombol menjadi loading
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 1.3rem;"></i> <span>Memuat Midtrans Snap Popup...</span>';

                fetch('{{ route('kendaraan-keluar.snap-token', $transaction->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        lost_ticket: lostTicketCheckbox.checked ? 1 : 0
                    })
                })
                .then(response => response.json())
                .then(data => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-qrcode" style="font-size: 1.3rem;"></i> <span>Bayar dengan QRIS</span>';

                    if (data.status === 'success' && data.snap_token) {
                        console.log('Snap Token berhasil dimuat:', data.snap_token);
                        
                        // Update UI Panel Sinkronisasi
                        if (displayOrderId) displayOrderId.innerText = data.midtrans_order_id;
                        if (btnCheckStatus) btnCheckStatus.disabled = false;

                        // Membuka popup Snap Modal Midtrans
                        window.snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                console.log('Callback onSuccess:', result);
                                showMidtransModal(
                                    'success', 
                                    'Pembayaran QRIS Berhasil!', 
                                    `<div style="text-align: center; padding: 0.25rem 0;">
                                        <div style="font-size: 1.15rem; color: #15803d; font-weight: 800; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                            <i class="fas fa-check-circle" style="color: #16a34a; font-size: 1.35rem;"></i>
                                            <span>Transaksi Digital Lunas & Terverifikasi</span>
                                        </div>
                                        <p style="color: #475569; font-size: 0.95rem; margin-bottom: 1.35rem; line-height: 1.6;">
                                            Pembayaran digital via <strong>Midtrans (QRIS)</strong> telah berhasil dilunasi. Sistem sedang memproses penyelesaian pengeluaran kendaraan dan mengembalikan kuota slot garasi secara otomatis.
                                        </p>
                                        <div style="background: #ecfdf5; border: 1px dashed #34d399; border-radius: 12px; padding: 0.9rem 1.25rem; color: #065f46; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.12);">
                                            <i class="fas fa-sync-alt fa-spin" style="font-size: 1.25rem; color: #10b981;"></i>
                                            <span>Mengarahkan kembali ke Halaman Kendaraan Keluar...</span>
                                        </div>
                                    </div>`, 
                                    null,
                                    'QRIS / PAID (LUNAS)'
                                );
                            },
                            onPending: function(result) {
                                console.log('Callback onPending:', result);
                                showMidtransModal(
                                    'pending', 
                                    'Pembayaran Menunggu (onPending)', 
                                    'Callback <code>onPending</code> diterima. Setelah melunasi di aplikasi QRIS, tekan tombol <strong>Cek Status Pembayaran</strong> di bawah untuk memutakhirkan status.', 
                                    result
                                );
                            },
                            onError: function(result) {
                                console.error('Callback onError:', result);
                                showMidtransModal(
                                    'error', 
                                    'Pembayaran Gagal (onError)', 
                                    'Callback <code>onError</code> diterima dari Midtrans Sandbox. Terjadi kendala teknis atau kegagalan transaksi.', 
                                    result
                                );
                            },
                            onClose: function() {
                                console.log('Callback onClose: Popup ditutup tanpa notifikasi.');
                            }
                        });
                    } else {
                        showMidtransModal(
                            'error',
                            'Gagal Memuat Token',
                            'Server gagal menghasilkan Snap Token: <strong>' + (data.message || data.error || 'Terjadi kesalahan pada backend') + '</strong>',
                            null
                        );
                    }
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-qrcode" style="font-size: 1.3rem;"></i> <span>Bayar dengan QRIS</span>';
                    showMidtransModal(
                        'error',
                        'Gangguan Koneksi / Jaringan',
                        'Terjadi kendala koneksi saat memanggil layanan Snap Token Midtrans.',
                        null
                    );
                });
            } else {
                if (!confirm("Selesaikan transaksi dan konfirmasi pengeluaran motor ini? Kapasitas slot garasi akan otomatis ditambahkan.")) {
                    e.preventDefault();
                }
            }
        });

        // Event Listener Tombol Cek Status Pembayaran (Tahap 8D)
        if (btnCheckStatus) {
            btnCheckStatus.addEventListener('click', function() {
                const icon = this.querySelector('.id-spin-icon') || this.querySelector('i');
                if (icon) icon.classList.add('fa-spin');
                this.disabled = true;

                fetch('{{ route("kendaraan-keluar.sync-status", $transaction->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (icon) icon.classList.remove('fa-spin');
                    this.disabled = false;

                    if (data.status === 'success') {
                        // Perbarui UI lencana dan ID
                        badgePaymentStatus.innerText = data.payment_status.toUpperCase();
                        if (data.midtrans_transaction_id) {
                            displayTxId.innerText = data.midtrans_transaction_id;
                        }
                        if (data.midtrans_order_id) {
                            displayOrderId.innerText = data.midtrans_order_id;
                        }

                        if (data.is_paid) {
                            badgePaymentStatus.style.background = '#dcfce7';
                            badgePaymentStatus.style.color = '#16a34a';
                            badgePaymentStatus.style.borderColor = '#86efac';

                            btnPrintReceipt.disabled = false;
                            btnPrintReceipt.style.background = '#16a34a';
                            btnPrintReceipt.style.borderColor = '#15803d';
                            btnPrintReceipt.style.cursor = 'pointer';
                            btnPrintReceipt.style.opacity = '1';

                            syncStatusHelperText.innerHTML = '<i class="fas fa-check-circle" style="color: #16a34a;"></i> Status <strong>PAID</strong> terkonfirmasi! Tombol <strong>Cetak Struk Keluar</strong> siap digunakan untuk mencetak PDF.';

                            showMidtransModal(
                                'success',
                                'Pembayaran Terkonfirmasi Lunas!',
                                `<div style="text-align: center; padding: 0.25rem 0;">
                                    <div style="font-size: 1.15rem; color: #15803d; font-weight: 800; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                        <i class="fas fa-check-circle" style="color: #16a34a; font-size: 1.35rem;"></i>
                                        <span>Status PAID Terverifikasi Midtrans</span>
                                    </div>
                                    <p style="color: #475569; font-size: 0.95rem; margin-bottom: 1.35rem; line-height: 1.6;">
                                        Status dari Midtrans dikonfirmasi <strong>PAID (${data.payment_status.toUpperCase()})</strong>. Sistem sedang otomatis memverifikasi penyelesaian transaksi keluar dan memulihkan kapasitas slot garasi.
                                    </p>
                                    <div style="background: #ecfdf5; border: 1px dashed #34d399; border-radius: 12px; padding: 0.9rem 1.25rem; color: #065f46; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.12);">
                                        <i class="fas fa-sync-alt fa-spin" style="font-size: 1.25rem; color: #10b981;"></i>
                                        <span>Mengarahkan kembali ke Halaman Kendaraan Keluar...</span>
                                    </div>
                                </div>`,
                                null,
                                'SYNC: PAID (LUNAS)'
                            );
                        } else {
                            badgePaymentStatus.style.background = '#fef3c7';
                            badgePaymentStatus.style.color = '#d97706';
                            badgePaymentStatus.style.borderColor = '#fde68a';
                            
                            btnPrintReceipt.disabled = true;
                            btnPrintReceipt.style.background = '#94a3b8';
                            btnPrintReceipt.style.borderColor = '#64748b';
                            btnPrintReceipt.style.cursor = 'not-allowed';
                            btnPrintReceipt.style.opacity = '0.65';

                            showMidtransModal(
                                'pending',
                                'Hasil Sinkronisasi: ' + data.payment_status.toUpperCase(),
                                data.message || 'Status pembayaran saat ini adalah <strong>' + data.payment_status + '</strong>. Tombol Cetak Struk tetap dinonaktifkan hingga status berubah menjadi PAID.',
                                null,
                                'Sync: ' + data.payment_status.toUpperCase()
                            );
                        }
                    } else {
                        showMidtransModal(
                            'error',
                            'Gagal Sinkronisasi Status',
                            'Terjadi kesalahan saat mengeksekusi sinkronisasi: <strong>' + (data.message || 'Error tidak diketahui') + '</strong>',
                            null,
                            'Sync Error'
                        );
                    }
                })
                .catch(error => {
                    console.error("Sync Error:", error);
                    if (icon) icon.classList.remove('fa-spin');
                    this.disabled = false;
                    showMidtransModal(
                        'error',
                        'Gangguan Koneksi / Jaringan',
                        'Gagal menghubungi endpoint sinkronisasi Midtrans Status API lokal.',
                        null,
                        'Network Error'
                    );
                });
            });
        }

        // Event Listener Tombol Cetak Struk Keluar PDF (Tahap 9)
        if (btnPrintReceipt) {
            btnPrintReceipt.addEventListener('click', function() {
                window.open("{{ route('kendaraan-keluar.struk-pdf', $transaction->id) }}", '_blank');
            });
        }

        // Jika status transaksi QRIS sudah PAID saat halaman dibuka, langsung proses pengeluaran kendaraan
        @if($transaction->payment_method === 'QRIS' && $transaction->payment_status === 'paid' && $transaction->status === 'parked')
        showMidtransModal(
            'success', 
            'Pembayaran QRIS Telah Lunas!', 
            `<div style="text-align: center; padding: 0.25rem 0;">
                <div style="font-size: 1.15rem; color: #15803d; font-weight: 800; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-check-circle" style="color: #16a34a; font-size: 1.35rem;"></i>
                    <span>Transaksi QRIS Lunas & Terverifikasi</span>
                </div>
                <p style="color: #475569; font-size: 0.95rem; margin-bottom: 1.35rem; line-height: 1.6;">
                    Pembayaran digital via <strong>Midtrans (QRIS)</strong> tercatat telah terkonfirmasi lunas. Sistem sedang menyelesaikan transaksi dan mengembalikan kuota slot garasi.
                </p>
                <div style="background: #ecfdf5; border: 1px dashed #34d399; border-radius: 12px; padding: 0.9rem 1.25rem; color: #065f46; font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.12);">
                    <i class="fas fa-sync-alt fa-spin" style="font-size: 1.25rem; color: #10b981;"></i>
                    <span>Mengarahkan kembali ke Halaman Kendaraan Keluar...</span>
                </div>
            </div>`, 
            null,
            'QRIS / PAID (LUNAS)'
        );
        @endif
    });
</script>
@endsection
