@extends('layouts.app')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 class="page-title" style="margin-bottom: 0.25rem;">Proses Pembayaran</h1>
                <p class="page-subtitle">Selesaikan transaksi kendaraan keluar</p>
            </div>
            <a href="{{ route('kendaraan-keluar') }}" style="color: var(--text-muted); font-size: 0.875rem; text-decoration: none;">
                <i class="fas fa-arrow-left" style="margin-right: 0.25rem;"></i> Kembali
            </a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            
            <!-- Kolom Kiri: Informasi Kendaraan -->
            <div>
                <div class="table-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-main); border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">Informasi Kendaraan</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="color: var(--text-muted); font-size: 0.875rem;">Plat Nomor</span>
                        <span style="font-weight: 700; color: var(--primary-color);">{{ $transaction->formatted_plate }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="color: var(--text-muted); font-size: 0.875rem;">Tipe Motor</span>
                        <span style="font-weight: 500; color: var(--text-main);">{{ $transaction->vehicle_type }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="color: var(--text-muted); font-size: 0.875rem;">Waktu Masuk</span>
                        <span style="font-weight: 500; color: var(--text-main); text-align: right;">
                            {{ $transaction->entry_time->format('d M Y') }}<br>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $transaction->entry_time->format('H:i') }} WIB</span>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="color: var(--text-muted); font-size: 0.875rem;">Petugas Masuk</span>
                        <span style="font-weight: 500; color: var(--text-main);">{{ $transaction->entryUser->name ?? 'Sistem' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; background-color: #eff6ff; padding: 0.75rem; border-radius: var(--radius-sm);">
                        <span style="color: var(--primary-dark); font-size: 0.875rem; font-weight: 600;">Durasi Parkir</span>
                        <span style="font-weight: 700; color: var(--primary-color);">{{ $formattedDuration }}</span>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Biaya & Form -->
            <div>
                <div class="table-card" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-main); border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">Rincian Biaya</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: var(--text-muted); font-size: 0.875rem;">Tarif 6 Jam Pertama</span>
                        <span style="font-weight: 500; color: var(--text-main);">Rp {{ number_format($feeDetails['base_fee'], 0, ',', '.') }}</span>
                    </div>
                    
                    @if($feeDetails['extra_hours'] > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: var(--text-muted); font-size: 0.875rem;">Jam Tambahan ({{ $feeDetails['extra_hours'] }} Jam)</span>
                        <span style="font-weight: 500; color: var(--text-main);">Rp {{ number_format($feeDetails['hourly_fee'], 0, ',', '.') }}</span>
                    </div>
                    @endif

                    @if($feeDetails['overnight_count'] > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: var(--text-muted); font-size: 0.875rem;">Tarif Menginap ({{ $feeDetails['overnight_count'] }} Malam)</span>
                        <span style="font-weight: 500; color: var(--text-main);">Rp {{ number_format($feeDetails['overnight_fee'], 0, ',', '.') }}</span>
                    </div>
                    @endif

                    <!-- Hidden by default, shown by JS if checkbox checked -->
                    <div id="fineRow" style="display: none; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: #ef4444; font-size: 0.875rem;">Denda Karcis Hilang</span>
                        <span style="font-weight: 500; color: #ef4444;">Rp {{ number_format($tariff->lost_ticket_fine, 0, ',', '.') }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 2px dashed var(--border-color);">
                        <span style="color: var(--text-main); font-size: 1.125rem; font-weight: 700;">Total Biaya</span>
                        <span id="totalDisplay" style="font-weight: 800; color: var(--primary-color); font-size: 1.25rem;">
                            Rp {{ number_format($feeDetails['total_before_fine'], 0, ',', '.') }}
                        </span>
                    </div>

                    <form action="{{ route('kendaraan-keluar.checkout', $transaction->id) }}" method="POST" style="margin-top: 2rem;">
                        @csrf
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" name="lost_ticket" id="lost_ticket" value="1" style="width: 1rem; height: 1rem; margin-right: 0.75rem;">
                                <span style="font-size: 0.875rem; color: var(--text-main);">Karcis Hilang</span>
                            </label>
                        </div>

                        <div class="form-group mb-6">
                            <label for="payment_method" class="form-label">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="Tunai">Tunai (Cash)</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1rem;">
                            <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to calculate UI Total instantly -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lostTicketCheckbox = document.getElementById('lost_ticket');
            const fineRow = document.getElementById('fineRow');
            const totalDisplay = document.getElementById('totalDisplay');
            
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
        });
    </script>
@endsection
