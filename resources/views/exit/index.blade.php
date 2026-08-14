@extends('layouts.app')

@section('content')
<style>
    .tab-btn {
        padding: 0.85rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 700;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        cursor: pointer;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 3px solid transparent;
    }
    .tab-btn:hover {
        background: #e2e8f0;
        color: #334155;
    }
    .tab-btn.active-tab {
        background: white;
        color: var(--primary);
        border-bottom: 3px solid var(--primary);
        box-shadow: 0 -4px 10px rgba(0,0,0,0.03);
    }
    .scanner-box {
        background: white;
        border-radius: 0 var(--radius-lg) var(--radius-lg) var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        padding: 2rem;
        margin-bottom: 2.5rem;
    }
    .drop-zone {
        border: 2px dashed #93c5fd;
        border-radius: var(--radius-lg);
        background: #eff6ff;
        padding: 3rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .drop-zone:hover {
        background: #dbeafe;
        border-color: #3b82f6;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95); opacity: 0.8; }
        50% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.8; }
    }
</style>

<div style="max-width: 1150px; margin: 0 auto; padding: 1.5rem;">
    <div style="margin-bottom: 2rem;">
        <h1 class="page-title" style="margin-bottom: 0.25rem; font-size: 1.85rem;">Pencarian & Scan Kendaraan Keluar</h1>
        <p class="page-subtitle">Pindai QR Code pada karcis atau input plat nomor secara manual untuk memproses pengeluaran kendaraan</p>
    </div>

    @if(session('error') || isset($searchError))
        <div style="background-color: #fef2f2; border-left: 5px solid #ef4444; color: #991b1b; padding: 1.25rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 2rem; font-size: 0.95rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1);">
            <div style="background: #fee2e2; color: #dc2626; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h4 style="margin: 0 0 0.25rem 0; font-weight: 700; font-size: 1.05rem; color: #991b1b;">Pencarian Tidak Memenuhi Syarat Keluar</h4>
                <p style="margin: 0; line-height: 1.5; font-size: 0.95rem;">{{ session('error') ?? $searchError }}</p>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div style="background-color: #ecfdf5; border-left: 5px solid #10b981; color: #065f46; padding: 1.25rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 2rem; font-size: 0.95rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);">
            <div style="display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 280px;">
                <div style="background: #d1fae5; color: #059669; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div style="font-weight: 600; line-height: 1.4;">{{ session('success') }}</div>
            </div>
            @if(session('completed_tx_id'))
                <a href="{{ route('kendaraan-keluar.struk-pdf', session('completed_tx_id')) }}" target="_blank" class="btn" style="background: #059669; color: white; padding: 0.7rem 1.25rem; border-radius: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2); transition: all 0.2s; white-space: nowrap;">
                    <i class="fas fa-print"></i> <span>Cetak Struk Keluar (PDF)</span>
                </a>
            @endif
        </div>
    @endif

    <!-- CARD HASIL PENCARIAN KENDARAAN (Jika Ditemukan) -->
    @if(!empty($searchedTransaction))
    <div style="background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border: 2px solid #3b82f6; border-radius: 16px; padding: 2rem; margin-bottom: 2.5rem; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.15);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px dashed #bfdbfe;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="background: #3b82f6; color: white; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin: 0;">Profil Kendaraan Ditemukan</h2>
                    <span style="font-size: 0.85rem; color: #64748b;">Siap untuk dilanjutkan ke tahap pemesanan & checkout keluar</span>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                <span style="background: #eff6ff; border: 1px solid #93c5fd; color: #1d4ed8; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="fas {{ $searchMethod === 'Scan Kamera QR' ? 'fa-camera' : ($searchMethod === 'Upload Gambar QR' ? 'fa-file-image' : 'fa-keyboard') }}"></i>
                    Metode: {{ $searchMethod ?? 'Pencarian' }}
                </span>
                <span style="background: #ecfdf5; border: 1px solid #86efac; color: #047857; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block; animation: pulse-green 1.5s infinite;"></span>
                    Status: Parkir Aktif
                </span>
            </div>
        </div>

        <!-- Detail Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div style="background: white; padding: 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.35rem;">Plat Nomor Kendaraan</div>
                <div style="font-size: 1.75rem; font-weight: 900; color: #0f172a; letter-spacing: 2px;">{{ $searchedTransaction->formatted_plate }}</div>
            </div>
            <div style="background: white; padding: 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.35rem;">Jenis Motor & Golongan</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #334155;">{{ $searchedTransaction->vehicle_type }}</div>
                <div style="font-size: 0.85rem; color: #3b82f6; font-weight: 700; margin-top: 0.15rem;">
                    {{ $searchedTransaction->vehicleType->category ?? 'Golongan Standar' }} 
                    <span style="color: #94a3b8;">&bull;</span> 
                    {{ $searchedTransaction->slot_used }} Slot
                </div>
            </div>
            <div style="background: white; padding: 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.35rem;">Waktu Masuk Parkir</div>
                <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b;">{{ $searchedTransaction->entry_time->format('d/m/Y, H:i') }} <span style="font-size: 0.8rem; color: #64748b;">WIB</span></div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;"><i class="fas fa-user-shield" style="margin-right: 0.3rem; color: #94a3b8;"></i> Petugas Masuk: <strong>{{ $searchedTransaction->entryUser->name ?? '-' }}</strong></div>
            </div>
            <div style="background: #eff6ff; padding: 1.25rem; border-radius: 12px; border: 1px solid #bfdbfe; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 0.8rem; font-weight: 700; color: #1d4ed8; text-transform: uppercase; margin-bottom: 0.35rem;"><i class="far fa-clock" style="margin-right: 0.3rem;"></i> Durasi Parkir Sementara</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #1e3a8a;">{{ $searchedTransaction->durasi_sementara }}</div>
                <div style="font-size: 0.8rem; color: #2563eb; margin-top: 0.25rem; font-weight: 600;"><i class="fas fa-history" style="margin-right:0.25rem;"></i> Dihitung secara real-time</div>
            </div>
            <div style="background: #ecfdf5; padding: 1.25rem; border-radius: 12px; border: 1px solid #a7f3d0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 0.8rem; font-weight: 700; color: #047857; text-transform: uppercase; margin-bottom: 0.35rem;"><i class="fas fa-coins" style="margin-right: 0.3rem;"></i> Estimasi Biaya Sementara</div>
                <div style="font-size: 1.35rem; font-weight: 900; color: #064e3b;">Rp {{ number_format($searchedTransaction->estimasi_biaya, 0, ',', '.') }}</div>
                <div style="font-size: 0.8rem; color: #059669; margin-top: 0.25rem; font-weight: 600;"><i class="fas fa-tag" style="margin-right:0.25rem;"></i> Sesuai tarif {{ $searchedTransaction->vehicle_type }}</div>
            </div>
            <div style="background: #f5f3ff; padding: 1.25rem; border-radius: 12px; border: 1px solid #ddd6fe; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 0.8rem; font-weight: 700; color: #5b21b6; text-transform: uppercase; margin-bottom: 0.35rem;"><i class="fas fa-parking" style="margin-right: 0.3rem;"></i> Status Pengosongan Slot</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #3b0764;">Mengembalikan {{ $searchedTransaction->slot_used }} Slot</div>
                <div style="font-size: 0.8rem; color: #6d28d9; margin-top: 0.25rem; font-weight: 600;"><i class="fas fa-check-circle" style="margin-right:0.25rem;"></i> Kapasitas garasi siap bertambah</div>
            </div>
        </div>

        <!-- Transparansi Tarif Berlaku -->
        @if(isset($searchedTransaction->tarif_berlaku))
        <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 1.75rem;">
            <div style="font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-coins" style="color: #f59e0b;"></i> Transparansi Tarif yang Berlaku (Sesuai Tipe: {{ $searchedTransaction->vehicle_type }})
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; text-align: center;">
                <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px;">
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Tarif 6 Jam Pertama</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-top: 0.2rem;">Rp {{ number_format($searchedTransaction->tarif_berlaku->base_rate, 0, ',', '.') }}</div>
                </div>
                <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px;">
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Tarif Per Jam Berikutnya</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-top: 0.2rem;">Rp {{ number_format($searchedTransaction->tarif_berlaku->hourly_rate, 0, ',', '.') }} / Jam</div>
                </div>
                <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px;">
                    <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Tarif Menginap / Malam</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-top: 0.2rem;">Rp {{ number_format($searchedTransaction->tarif_berlaku->overnight_rate, 0, ',', '.') }}</div>
                </div>
                <div style="background: #fef2f2; padding: 0.75rem; border-radius: 8px; border: 1px dashed #fca5a5;">
                    <div style="font-size: 0.75rem; color: #991b1b; font-weight: 600;">Denda Hilang Karcis</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #dc2626; margin-top: 0.2rem;">Rp {{ number_format($searchedTransaction->tarif_berlaku->lost_ticket_fine, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Aksi Tombol -->
        <div style="display: flex; justify-content: flex-end; gap: 1rem; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('kendaraan-keluar') }}" class="btn" style="padding: 0.85rem 1.5rem; background: white; color: #475569; border: 1px solid #cbd5e1; border-radius: var(--radius-md); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-undo"></i> Cari Kendaraan Lain
            </a>
            @if($searchedTransaction->can_cancel)
            <form action="{{ route('kendaraan-keluar.cancel', $searchedTransaction->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN transaksi kendaraan berplat {{ $searchedTransaction->formatted_plate }}? (Baru parkir {{ $searchedTransaction->durasi_sementara }} <= 15 Menit). Biaya akan menjadi Rp 0 dan kapasitas slot kembali ke kuota global.')">
                @csrf
                <button type="submit" class="btn" style="padding: 0.85rem 1.5rem; background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; border-radius: var(--radius-md); font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;" title="Batalkan karena kendaraan baru masuk <= 15 menit">
                    <i class="fas fa-times-circle"></i> Batalkan Transaksi (< 15 Mnt - Rp 0)
                </button>
            </form>
            @endif
            <a href="{{ route('kendaraan-keluar.process', ['id' => $searchedTransaction->id, 'method' => $searchMethod ?? 'Pencarian Manual']) }}" class="btn btn-primary" style="padding: 0.85rem 2rem; font-size: 1.05rem; font-weight: 700; border-radius: var(--radius-md); background: #16a34a; border: 1px solid #15803d; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.3); text-decoration: none; display: inline-flex; align-items: center; gap: 0.6rem;">
                <i class="fas fa-cash-register"></i> Proses Kendaraan Keluar & Pembayaran
            </a>
        </div>
    </div>
    @endif

    <!-- PANEL PENCARIAN 3-IN-1 (TABS) -->
    <div>
        <div style="display: flex; border-bottom: 1px solid var(--border-color); gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" id="btn-camera" class="tab-btn active-tab" onclick="showTab('camera')">
                <i class="fas fa-camera"></i> Scan Kamera QR
            </button>
            <button type="button" id="btn-upload" class="tab-btn" onclick="showTab('upload')">
                <i class="fas fa-file-image"></i> Upload Gambar QR
            </button>
            <button type="button" id="btn-manual" class="tab-btn" onclick="showTab('manual')">
                <i class="fas fa-keyboard"></i> Input Manual Plat
            </button>
        </div>

        <div class="scanner-box">
            <!-- TAB 1: CAMERA SCANNER -->
            <div id="pane-camera" class="tab-pane" style="display: block;">
                <div style="text-align: center; max-width: 550px; margin: 0 auto;">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Pemindai QR Code via Kamera</h3>
                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.5rem;">Arahkan lensa kamera peramban ke QR Code pada karcis parkir motor untuk mendeteksi plat nomor secara otomatis.</p>
                    
                    <div id="reader" style="width: 100%; max-width: 450px; margin: 0 auto 1rem auto; border-radius: 12px; overflow: hidden; border: 2px solid #e2e8f0; background: #f8fafc; min-height: 280px; position: relative; display: flex; align-items: center; justify-content: center;">
                        <div id="cameraPlaceholder" style="color: #94a3b8; padding: 2rem; width: 100%;">
                            <i class="fas fa-camera" style="font-size: 3.5rem; margin-bottom: 1rem; color: #cbd5e1; display: block;"></i>
                            Kamera belum diaktifkan. Tekan tombol di bawah untuk memulai pencarian scanner.
                        </div>
                        <video id="cameraVideo" autoplay playsinline style="width: 100%; max-height: 340px; object-fit: cover; display: none; background: #000;"></video>
                        <canvas id="cameraCanvas" style="display: none;"></canvas>
                    </div>

                    <div id="cameraStatus" style="font-size: 0.9rem; font-weight: 600; color: #64748b; margin-bottom: 1.25rem;">
                        <i class="fas fa-info-circle"></i> Siap memindai karcis parkir
                    </div>

                    <div style="display: flex; justify-content: center; gap: 1rem;">
                        <button type="button" id="startCameraBtn" onclick="startCamera()" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-weight: 700; border-radius: var(--radius-md); background: #3b82f6; border: none; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-play"></i> Aktifkan Kamera Scanner
                        </button>
                        <button type="button" id="stopCameraBtn" onclick="stopCamera()" class="btn" style="padding: 0.75rem 1.5rem; font-weight: 700; border-radius: var(--radius-md); background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; display: none; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-stop"></i> Matikan Kamera
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: UPLOAD GAMBAR QR -->
            <div id="pane-upload" class="tab-pane" style="display: none;">
                <div style="max-width: 600px; margin: 0 auto; text-align: center;">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Unggah File Gambar QR Code</h3>
                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.5rem;">Pilih foto atau tangkap layar (screenshot) karcis PDF yang menampilkan QR Code. Sistem akan membaca gambar dalam hitungan detik.</p>

                    <label class="drop-zone" style="display: block; margin-bottom: 1.5rem;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 3.5rem; color: #3b82f6; margin-bottom: 1rem;"></i>
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.5rem;">Klik untuk Memilih Gambar QR dari Perangkat</h4>
                        <p style="font-size: 0.85rem; color: #64748b; margin: 0;">Mendukung format JPG, JPEG, PNG, dan GIF</p>
                        <input type="file" id="qrFileInput" accept="image/*" style="display: none;" onchange="handleFileUpload(this)">
                    </label>

                    <!-- Hidden container for dummy scanner -->
                    <div id="file-reader-dummy" style="display: none;"></div>

                    <div id="uploadStatus" style="min-height: 50px;"></div>
                </div>
            </div>

            <!-- TAB 3: INPUT MANUAL PLAT NOMOR -->
            <div id="pane-manual" class="tab-pane" style="display: none;">
                <div style="max-width: 550px; margin: 0 auto;">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; text-align: center;">Input Manual Plat Nomor Kendaraan</h3>
                    <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1.75rem; text-align: center;">Ketik plat nomor kendaraan secara lengkap apabila QR Code rusak atau karcis tidak terbaca.</p>

                    <form action="{{ route('kendaraan-keluar') }}" method="GET" style="background: #f8fafc; padding: 2rem; border-radius: 14px; border: 1px solid #e2e8f0;">
                        <input type="hidden" name="search_method" value="Input Manual Plat">
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label for="search_plate" class="form-label" style="font-weight: 700; font-size: 0.95rem; color: #334155; display: block; margin-bottom: 0.5rem;">Plat Nomor Kendaraan</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1.25rem; color: #64748b;">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" name="search_plate" id="search_plate" class="form-control" 
                                       placeholder="Contoh: B 1234 KKN atau B1234KKN" 
                                       value="{{ old('search_plate', request('search_plate')) }}" 
                                       style="width: 100%; padding: 0.85rem 1rem 0.85rem 3rem; font-size: 1.1rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; border-radius: var(--radius-md); border: 2px solid #cbd5e1;"
                                       required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.95rem; font-size: 1.05rem; font-weight: 700; border-radius: var(--radius-md); box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);">
                            <i class="fas fa-search" style="margin-right: 0.5rem;"></i> Cari Data Kendaraan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- HIDDEN FORM FOR QR JS SCAN SUBMISSIONS -->
    <form id="hiddenSearchForm" action="{{ route('kendaraan-keluar') }}" method="GET" style="display: none;">
        <input type="hidden" name="search_plate" id="hidden_search_plate" value="">
        <input type="hidden" name="search_method" id="hidden_search_method" value="">
    </form>

    <!-- TABEL KENDARAAN PARKIR AKTIF -->
    <div class="table-card" style="margin-bottom: 2rem;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.25rem;"><i class="fas fa-list-ul" style="margin-right: 0.5rem; color: #3b82f6;"></i> Daftar Seluruh Kendaraan Parkir Aktif</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Menampilkan {{ $activeTransactions->count() }} unit motor yang sedang berada di dalam garasi</p>
            </div>
            
            <!-- Filter Kecil -->
            <form action="{{ route('kendaraan-keluar') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                @if(request('search_plate'))
                    <input type="hidden" name="search_plate" value="{{ request('search_plate') }}">
                    <input type="hidden" name="search_method" value="{{ request('search_method') }}">
                @endif
                
                <select name="vehicle_type" class="form-control" style="padding: 0.5rem 0.85rem; font-size: 0.85rem; border-radius: 8px; border: 1px solid #cbd5e1;" onchange="this.form.submit()">
                    <option value="Semua Tipe">Semua Tipe Motor</option>
                    @foreach($vehicleTypes ?? [] as $vt)
                        <option value="{{ $vt->name }}" {{ request('vehicle_type') == $vt->name ? 'selected' : '' }}>{{ $vt->name }} ({{ $vt->category }})</option>
                    @endforeach
                </select>

                <select name="duration" class="form-control" style="padding: 0.5rem 0.85rem; font-size: 0.85rem; border-radius: 8px; border: 1px solid #cbd5e1;" onchange="this.form.submit()">
                    <option value="Semua Durasi" {{ request('duration') == 'Semua Durasi' ? 'selected' : '' }}>Semua Durasi</option>
                    <option value="Kurang dari 6 Jam" {{ request('duration') == 'Kurang dari 6 Jam' ? 'selected' : '' }}>Kurang dari 6 Jam</option>
                    <option value="Lebih dari 6 Jam" {{ request('duration') == 'Lebih dari 6 Jam' ? 'selected' : '' }}>Lebih dari 6 Jam</option>
                </select>

                @if(request('vehicle_type') || request('duration') || request('search_plate'))
                <a href="{{ route('kendaraan-keluar') }}" class="btn btn-sm" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 0.5rem 0.85rem; font-size: 0.8rem; border-radius: 8px; text-decoration: none;">
                    <i class="fas fa-times" style="margin-right: 0.2rem;"></i> Reset Filter
                </a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table" style="margin: 0; width: 100%;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 1rem 1.25rem; font-weight: 700; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Plat Nomor</th>
                        <th style="padding: 1rem 1.25rem; font-weight: 700; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Tipe Motor & Slot</th>
                        <th style="padding: 1rem 1.25rem; font-weight: 700; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Waktu Masuk</th>
                        <th style="padding: 1rem 1.25rem; font-weight: 700; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Durasi & Petugas</th>
                        <th style="padding: 1rem 1.25rem; font-weight: 700; font-size: 0.85rem; color: #475569; text-transform: uppercase;">Est. Biaya</th>
                        <th style="padding: 1rem 1.25rem; font-weight: 700; font-size: 0.85rem; color: #475569; text-transform: uppercase; text-align: center;">Aksi</th>
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
                            <div style="font-size: 0.75rem; color: #10b981; font-weight: 700; margin-top: 0.2rem;">
                                <i class="fas fa-layer-group"></i> {{ $transaction->slot_used ?? 1 }} Slot Terpakai
                            </div>
                        </td>
                        <td style="padding: 1.15rem 1.25rem;">
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.925rem;">
                                {{ $transaction->entry_time->format('d/m/Y, H:i') }} <span style="font-size: 0.75rem; color: #64748b;">WIB</span>
                            </div>
                        </td>
                        <td style="padding: 1.15rem 1.25rem;">
                            <div style="font-weight: 700; color: #2563eb; font-size: 0.9rem;">
                                <i class="far fa-clock" style="margin-right: 0.2rem;"></i> {{ $transaction->formatted_duration }}
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.15rem;">
                                Petugas: {{ $transaction->entryUser->name ?? '-' }}
                            </div>
                        </td>
                        <td style="padding: 1.15rem 1.25rem;">
                            <span style="font-weight: 800; color: #1e293b; font-size: 1rem;">
                                Rp {{ number_format($transaction->estimasi_biaya, 0, ',', '.') }}
                            </span>
                        </td>
                        <td style="padding: 1.15rem 1.25rem; text-align: center;">
                            <div style="display: inline-flex; gap: 0.4rem; justify-content: center;">
                                @if($transaction->can_cancel)
                                <form action="{{ route('kendaraan-keluar.cancel', $transaction->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Batalkan transaksi plat {{ $transaction->formatted_plate }}? (Usia parkir {{ $transaction->formatted_duration }} <= 15 menit). Biaya menjadi Rp 0 dan slot dikembalikan.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm" style="padding: 0.5rem 0.85rem; border-radius: 8px; font-weight: 700; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; box-shadow: 0 2px 4px rgba(0,0,0,0.02);" title="Batalkan (< 15 menit)">
                                        <i class="fas fa-times"></i> Batal (Rp 0)
                                    </button>
                                </form>
                                @endif
                                <!-- Tombol tunggal Proses Keluar tanpa Edit & Hapus ( Sesuai Aturan Bisnis Tahap 4 & 5 ) -->
                                <a href="{{ route('kendaraan-keluar.process', ['id' => $transaction->id, 'method' => 'Daftar Parkir Aktif']) }}" class="btn btn-primary btn-sm" title="Proses Pengeluaran Kendaraan" style="padding: 0.5rem 1rem; border-radius: 8px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; background: #3b82f6; border: none; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);">
                                    <i class="fas fa-sign-out-alt"></i> Proses Keluar
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 3.5rem 2rem; text-align: center; color: #94a3b8;">
                            <i class="fas fa-motorcycle" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 1rem; display: block;"></i>
                            <p style="font-size: 1rem; font-weight: 600; margin: 0;">Tidak ada data kendaraan parkir aktif yang sesuai pesanan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- LOAD JSQR (ULTRA-FAST PIXEL ENGINE) AND HTML5-QRCODE AS SECONDARY BACKUP -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" integrity="sha512-r6rDA7W6ZeQhvl8S7yGXG1gBO12eP2GFNGCYOh97OehzVbEKA3qNWJyId99AjuDMLIr00GI1lRbiJASxGrmgcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // Tab switching controller
    function showTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active-tab'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.style.display = 'none');
        document.getElementById('btn-' + tabName).classList.add('active-tab');
        document.getElementById('pane-' + tabName).style.display = 'block';

        // Matikan kamera secara otomatis jika beralih ke tab lain demi menghemat baterai/resource
        if (tabName !== 'camera' && isCameraRunning) {
            stopCamera();
        }
    }

    // Camera Scanner logic with HTML5 Video + jsQR (Super Cepat & Mendukung Laptop 100%)
    let currentStream = null;
    let scanInterval = null;
    let isCameraRunning = false;

    function startCamera() {
        const video = document.getElementById("cameraVideo");
        const placeholder = document.getElementById("cameraPlaceholder");
        const startBtn = document.getElementById("startCameraBtn");
        const stopBtn = document.getElementById("stopCameraBtn");
        const statusMsg = document.getElementById("cameraStatus");
        const canvas = document.getElementById("cameraCanvas");
        const context = canvas.getContext("2d");

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            statusMsg.innerHTML = '<div style="color: #991b1b; background: #fef2f2; padding: 1rem; border-radius: 10px; border: 1px solid #fca5a5; font-weight: 600; text-align: left; max-width: 520px; margin: 0.5rem auto; line-height: 1.5;"><i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1.2rem; float: left; margin-right: 0.6rem;"></i> <b>Akses Kamera Dibatasi Browser</b><br>Browser melarang WebRTC/Webcam jika web diakses melalui HTTP pada domain custom (<code>' + window.location.hostname + '</code>).<br><br><b>Solusi:</b> Silakan buka aplikasi menggunakan <b>http://localhost:8000/kendaraan-keluar</b> di browser Anda agar kamera aktif seketika.</div>';
            return;
        }

        statusMsg.innerHTML = '<span style="color: #0284c7;"><i class="fas fa-spinner fa-spin" style="margin-right: 0.3rem;"></i> Mengaktifkan kamera laptop / perangkat...</span>';
        startBtn.style.display = "none";

        // Pertama coba request kamera belakang (untuk HP). Jika gagal/ditolak (karena di laptop cuma ada webcam depan), otomatis fallback ke { video: true } (sembarang webcam yang tersedia).
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .catch(() => navigator.mediaDevices.getUserMedia({ video: true }))
            .then(stream => {
                currentStream = stream;
                video.srcObject = stream;
                video.setAttribute("playsinline", true); // Diperlukan untuk iPhone/iOS
                video.play();
                
                placeholder.style.display = "none";
                video.style.display = "block";
                isCameraRunning = true;
                stopBtn.style.display = "inline-flex";
                statusMsg.innerHTML = '<span style="color: #16a34a; font-weight: 600;"><span style="display:inline-block; width:8px; height:8px; background:#10b981; border-radius:50%; margin-right:0.4rem; animation:pulse-green 1.5s infinite;"></span> Kamera aktif & siap scan. Arahkan QR Code Karcis ke layar kamera.</span>';

                // Loop pemindaian super cepat setiap 80 milidetik menggunakan jsQR
                scanInterval = setInterval(() => {
                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);
                        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                        
                        // Eksekusi jsQR
                        if (typeof jsQR !== "undefined") {
                            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: "dontInvert"
                            });

                            if (code && code.data) {
                                // QR Terdeteksi dalam hitungan milidetik!
                                clearInterval(scanInterval);
                                stopCamera();
                                statusMsg.innerHTML = '<span style="color: #16a34a; font-weight: 700;"><i class="fas fa-check-circle" style="margin-right: 0.3rem;"></i> QR Berhasil Dibaca: <strong style="text-decoration:underline;">' + code.data + '</strong>. Memproses pencarian...</span>';
                                submitSearch(code.data, 'Scan Kamera QR');
                            }
                        }
                    }
                }, 80);
            })
            .catch(err => {
                isCameraRunning = false;
                startBtn.style.display = "inline-flex";
                stopBtn.style.display = "none";
                
                let errmsg = "Gagal mengakses kamera: Pastikan izin kamera telah diaktifkan pada browser Anda.";
                if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    errmsg = "<b>Kamera Diblokir Oleh Keamanan Browser</b><br>Browser modern (Chrome/Edge) melarang akses kamera jika website dibuka via jaringan HTTP dengan domain lokal seperti <code>http://" + window.location.hostname + "</code>.<br><br><b>Solusi cepat:</b> Buka aplikasi di peramban menggunakan alamat <b>http://localhost:8000/kendaraan-keluar</b> atau gunakan tab <b>Upload Gambar QR</b>.";
                } else if (err && (err.name === 'NotAllowedError' || err.toString().includes('Permission'))) {
                    errmsg = "<b>Izin Akses Kamera Ditolak</b><br>Peramban Anda melarang akses webcam untuk halaman ini. Klik ikon gembok/setelan di sebelah kiri kolom alamat (address bar) browser Anda, lalu ubah pengaturan Kamera menjadi <b>Izinkan (Allow)</b>, kemudian refresh halaman.";
                }
                statusMsg.innerHTML = '<div style="color: #991b1b; background: #fef2f2; padding: 1rem; border-radius: 10px; border: 1px solid #fca5a5; font-weight: 600; text-align: left; max-width: 520px; margin: 0.5rem auto; box-shadow: 0 4px 6px -1px rgba(239,68,68,0.1); line-height: 1.5;"><i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1.2rem; float: left; margin-right: 0.6rem; margin-top: 0.2rem;"></i> ' + errmsg + '</div>';
                console.error("Gagal getUserMedia:", err);
            });
    }

    function stopCamera() {
        if (scanInterval) {
            clearInterval(scanInterval);
            scanInterval = null;
        }
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        const video = document.getElementById("cameraVideo");
        const placeholder = document.getElementById("cameraPlaceholder");
        if (video) {
            video.pause();
            video.srcObject = null;
            video.style.display = "none";
        }
        if (placeholder) {
            placeholder.style.display = "block";
        }
        isCameraRunning = false;
        document.getElementById("startCameraBtn").style.display = "inline-flex";
        document.getElementById("stopCameraBtn").style.display = "none";
        document.getElementById("cameraStatus").innerHTML = '<span style="color: #64748b;"><i class="fas fa-info-circle"></i> Kamera dimatikan. Klik tombol di atas jika ingin memindai kembali.</span>';
    }

    // Upload File QR Scanner logic - Hybrid Dual-Engine (jsQR Instant + Html5Qrcode Backup)
    function handleFileUpload(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const statusDiv = document.getElementById("uploadStatus");
        statusDiv.innerHTML = '<div style="color: #0284c7; font-weight: 600; padding: 0.75rem; background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe; display:inline-block;"><i class="fas fa-spinner fa-spin" style="margin-right: 0.4rem;"></i> Menganalisis dan mende-code QR dari gambar dalam hitungan milidetik...</div>';

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                // Engine 1: Scan instan dengan jsQR
                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                canvas.width = img.width;
                canvas.height = img.height;
                context.drawImage(img, 0, 0, img.width, img.height);
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

                let code = null;
                if (typeof jsQR !== "undefined") {
                    code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "attemptBoth" });
                }

                if (code && code.data) {
                    statusDiv.innerHTML = '<div style="color: #065f46; font-weight: 700; background: #d1fae5; padding: 0.85rem 1.25rem; border-radius: 8px; border: 1px solid #a7f3d0; display:inline-flex; align-items:center; gap:0.5rem;"><i class="fas fa-check-circle" style="font-size: 1.2rem;"></i> <span>QR Code Terbaca Instan: <strong style="text-decoration:underline;">' + code.data + '</strong>. Menampilkan rincian...</span></div>';
                    submitSearch(code.data, 'Upload Gambar QR');
                    return;
                }

                // Engine 2 (Backup): Jika gambar terlalu miring atau memerlukan preprocessing khusus, panggil Html5Qrcode
                const fileReaderQr = new Html5Qrcode("file-reader-dummy");
                fileReaderQr.scanFile(file, true)
                .then(decodedText => {
                    statusDiv.innerHTML = '<div style="color: #065f46; font-weight: 700; background: #d1fae5; padding: 0.85rem 1.25rem; border-radius: 8px; border: 1px solid #a7f3d0; display:inline-flex; align-items:center; gap:0.5rem;"><i class="fas fa-check-circle" style="font-size: 1.2rem;"></i> <span>QR Code Terbaca: <strong style="text-decoration:underline;">' + decodedText + '</strong>. Menampilkan rincian...</span></div>';
                    submitSearch(decodedText, 'Upload Gambar QR');
                })
                .catch(err => {
                    statusDiv.innerHTML = '<div style="color: #991b1b; background: #fef2f2; padding: 1rem; border-radius: 10px; border: 1px solid #fca5a5; font-weight: 600; text-align: left; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(239,68,68,0.1);"><div style="display:flex; gap:0.75rem; align-items:center;"><i class="fas fa-exclamation-triangle" style="color: #dc2626; font-size: 1.35rem; flex-shrink:0;"></i><div><strong>Gambar Tidak Valid / QR Tidak Terbaca</strong><br><span style="font-size: 0.85rem; font-weight: 500; color: #b91c1c;">Gambar yang diunggah tidak mengandung QR Code plat nomor yang sah atau kualitas foto kurang jelas. Silakan coba gambar lain atau gunakan input manual.</span></div></div></div>';
                    input.value = '';
                });
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function submitSearch(plateNumber, method) {
        document.getElementById("hidden_search_plate").value = plateNumber;
        document.getElementById("hidden_search_method").value = method;
        setTimeout(() => {
            document.getElementById("hiddenSearchForm").submit();
        }, 350);
    }
</script>
@endsection
