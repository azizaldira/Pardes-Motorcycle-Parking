@extends('layouts.app')

@section('content')
    <div class="table-card" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
        <h1 class="page-title">Pengaturan Tarif</h1>
        <p class="page-subtitle">Kelola tarif dasar, tarif menginap, dan denda kehilangan karcis.</p>

        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('pengaturan-tarif.update') }}" method="POST" id="tariffForm">
            @csrf
            @method('PUT')

            <!-- Tarif 6 Jam Pertama -->
            <div class="form-group mb-6">
                <label for="base_rate" class="form-label" style="font-size: 1rem;">Tarif 6 Jam Pertama</label>
                <div class="input-wrapper">
                    <i class="fas fa-money-bill-wave" style="left: 1.5rem;"></i>
                    <span style="position: absolute; left: 3rem; color: var(--text-main); font-weight: 500;">Rp</span>
                    <input type="text" id="base_rate" name="base_rate" class="form-control currency-input" 
                           style="padding-left: 4.5rem;" 
                           value="{{ old('base_rate', $tariff->base_rate ?? '') }}" required>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Tarif flat yang dikenakan untuk penitipan motor selama maksimal 6 jam pertama.</p>
                @error('base_rate')
                    <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tarif Per Jam Berikutnya -->
            <div class="form-group mb-6">
                <label for="hourly_rate" class="form-label" style="font-size: 1rem;">Tarif Per Jam Berikutnya</label>
                <div class="input-wrapper">
                    <i class="fas fa-clock" style="left: 1.5rem;"></i>
                    <span style="position: absolute; left: 3rem; color: var(--text-main); font-weight: 500;">Rp</span>
                    <input type="text" id="hourly_rate" name="hourly_rate" class="form-control currency-input" 
                           style="padding-left: 4.5rem;" 
                           value="{{ old('hourly_rate', $tariff->hourly_rate ?? '') }}" required>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Tarif tambahan per jam setelah melewati batas 6 jam pertama (sebelum melewati tengah malam).</p>
                @error('hourly_rate')
                    <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tarif Menginap Per Malam -->
            <div class="form-group mb-6">
                <label for="overnight_rate" class="form-label" style="font-size: 1rem;">Tarif Menginap Per Malam</label>
                <div class="input-wrapper">
                    <i class="fas fa-moon" style="left: 1.5rem;"></i>
                    <span style="position: absolute; left: 3rem; color: var(--text-main); font-weight: 500;">Rp</span>
                    <input type="text" id="overnight_rate" name="overnight_rate" class="form-control currency-input" 
                           style="padding-left: 4.5rem;" 
                           value="{{ old('overnight_rate', $tariff->overnight_rate ?? '') }}" required>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Tarif tetap per malam jika kendaraan diinapkan melewati batas hari operasional.</p>
                @error('overnight_rate')
                    <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Denda Kehilangan Karcis -->
            <div class="form-group mb-6">
                <label for="lost_ticket_fine" class="form-label" style="font-size: 1rem;">Denda Kehilangan Karcis</label>
                <div class="input-wrapper">
                    <i class="fas fa-ticket-alt" style="left: 1.5rem;"></i>
                    <span style="position: absolute; left: 3rem; color: var(--text-main); font-weight: 500;">Rp</span>
                    <input type="text" id="lost_ticket_fine" name="lost_ticket_fine" class="form-control currency-input" 
                           style="padding-left: 4.5rem;" 
                           value="{{ old('lost_ticket_fine', $tariff->lost_ticket_fine ?? '') }}" required>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Biaya denda yang dikenakan apabila pelanggan menghilangkan karcis parkir.</p>
                @error('lost_ticket_fine')
                    <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Vanilla JS Script for Currency Formatting -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currencyInputs = document.querySelectorAll('.currency-input');

            // Function to format number to Rupiah string (e.g. "3000" -> "3.000")
            function formatRupiah(value) {
                let numberString = value.replace(/[^,\d]/g, '').toString();
                let split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return rupiah;
            }

            // Apply formatting on load
            currencyInputs.forEach(input => {
                input.value = formatRupiah(input.value);

                // Add event listener for input changes
                input.addEventListener('input', function(e) {
                    this.value = formatRupiah(this.value);
                });
            });

            // Clean formatting on submit (send raw numbers to backend)
            const form = document.getElementById('tariffForm');
            form.addEventListener('submit', function() {
                currencyInputs.forEach(input => {
                    // Remove all dots before submitting
                    input.value = input.value.replace(/\./g, '');
                });
            });
        });
    </script>
@endsection
