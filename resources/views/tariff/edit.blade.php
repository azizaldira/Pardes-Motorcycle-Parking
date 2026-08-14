@extends('layouts.app')

@section('content')
    <div class="table-card" style="max-width: 750px; margin: 0 auto; padding: 2.5rem; background: white; border-radius: var(--radius-lg); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1.25rem;">
            <div>
                <h1 class="page-title" style="color: #1e293b; margin: 0; font-size: 1.5rem;">Ubah Tarif: {{ $tariff->category }}</h1>
                <p class="page-subtitle" style="margin: 0; margin-top: 0.25rem; font-size: 0.875rem;">Sesuaikan nominal tarif parkir untuk seluruh motor pada golongan ini</p>
            </div>
            <a href="{{ route('pengaturan-tarif') }}" class="btn" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; border-radius: var(--radius-sm); display: inline-flex; align-items: center;">
                <i class="fas fa-arrow-left" style="margin-right: 0.4rem;"></i> Kembali
            </a>
        </div>

        <!-- Kartu Informasi Golongan -->
        <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; display: block;">Golongan Kendaraan</span>
                <span style="display: inline-flex; align-items: center; margin-top: 0.35rem; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 1rem; font-weight: 800; 
                    {{ $tariff->category === 'Golongan I' ? 'background-color: #e0f2fe; color: #0369a1;' : ($tariff->category === 'Golongan II' ? 'background-color: #fef3c7; color: #b45309;' : 'background-color: #f3e8ff; color: #6b21a8;') }}">
                    <i class="fas fa-tag" style="margin-right: 0.35rem; font-size: 0.85rem;"></i> {{ $tariff->category }}
                </span>
            </div>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <span style="background-color: white; border: 1px solid #cbd5e1; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.95rem; font-weight: 800; color: #334155;">
                    <i class="fas fa-parking text-primary" style="margin-right: 0.35rem;"></i> {{ $tariff->slot_required }} Slot per Motor
                </span>
            </div>
        </div>

        @if($errors->any())
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; font-size: 0.875rem; border-left: 4px solid #ef4444;">
                <div style="font-weight: 700; margin-bottom: 0.25rem;"><i class="fas fa-exclamation-circle" style="margin-right: 0.35rem;"></i> Terdapat kesalahan pada pengisian form:</div>
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pengaturan-tarif.update', $tariff) }}" method="POST" id="tariffEditForm">
            @csrf
            @method('PUT')

            <!-- Tarif 6 Jam Pertama -->
            <div class="form-group mb-6" style="margin-bottom: 1.75rem;">
                <label for="base_rate" class="form-label" style="font-weight: 600; color: #334155; display: block; margin-bottom: 0.5rem; font-size: 1rem;">
                    Tarif 6 Jam Pertama <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fas fa-money-bill-wave" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <span style="position: absolute; left: 3.25rem; top: 50%; transform: translateY(-50%); color: #1e293b; font-weight: 700; font-size: 1rem;">Rp</span>
                    <input type="text" id="base_rate" name="base_rate" class="form-control currency-input @error('base_rate') is-invalid @enderror" 
                           style="padding-left: 5.25rem; width: 100%; font-size: 1.05rem; font-weight: 700; color: #0f172a;" 
                           value="{{ old('base_rate', $tariff->base_rate) }}" required autocomplete="off">
                </div>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Tarif flat yang dikenakan kepada pelanggan untuk penitipan maksimal 6 jam pertama.</p>
                @error('base_rate')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tarif Per Jam Berikutnya -->
            <div class="form-group mb-6" style="margin-bottom: 1.75rem;">
                <label for="hourly_rate" class="form-label" style="font-weight: 600; color: #334155; display: block; margin-bottom: 0.5rem; font-size: 1rem;">
                    Tarif Per Jam Berikutnya <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fas fa-clock" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <span style="position: absolute; left: 3.25rem; top: 50%; transform: translateY(-50%); color: #1e293b; font-weight: 700; font-size: 1rem;">Rp</span>
                    <input type="text" id="hourly_rate" name="hourly_rate" class="form-control currency-input @error('hourly_rate') is-invalid @enderror" 
                           style="padding-left: 5.25rem; width: 100%; font-size: 1.05rem; font-weight: 700; color: #0f172a;" 
                           value="{{ old('hourly_rate', $tariff->hourly_rate) }}" required autocomplete="off">
                </div>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Tarif tambahan per jam setelah melewati batas 6 jam pertama (sebelum melewati tengah malam).</p>
                @error('hourly_rate')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tarif Menginap Per Malam -->
            <div class="form-group mb-6" style="margin-bottom: 1.75rem;">
                <label for="overnight_rate" class="form-label" style="font-weight: 600; color: #334155; display: block; margin-bottom: 0.5rem; font-size: 1rem;">
                    Tarif Menginap Per Malam <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fas fa-moon" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <span style="position: absolute; left: 3.25rem; top: 50%; transform: translateY(-50%); color: #1e293b; font-weight: 700; font-size: 1rem;">Rp</span>
                    <input type="text" id="overnight_rate" name="overnight_rate" class="form-control currency-input @error('overnight_rate') is-invalid @enderror" 
                           style="padding-left: 5.25rem; width: 100%; font-size: 1.05rem; font-weight: 700; color: #0f172a;" 
                           value="{{ old('overnight_rate', $tariff->overnight_rate) }}" required autocomplete="off">
                </div>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Tarif inap per malam jika kendaraan diparkir melewati pergantian hari (tengah malam).</p>
                @error('overnight_rate')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Denda Kehilangan Karcis -->
            <div class="form-group mb-6" style="margin-bottom: 2rem;">
                <label for="lost_ticket_fine" class="form-label" style="font-weight: 600; color: #ef4444; display: block; margin-bottom: 0.5rem; font-size: 1rem;">
                    Denda Kehilangan Karcis <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fas fa-ticket-alt" style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: #ef4444;"></i>
                    <span style="position: absolute; left: 3.25rem; top: 50%; transform: translateY(-50%); color: #ef4444; font-weight: 700; font-size: 1rem;">Rp</span>
                    <input type="text" id="lost_ticket_fine" name="lost_ticket_fine" class="form-control currency-input @error('lost_ticket_fine') is-invalid @enderror" 
                           style="padding-left: 5.25rem; width: 100%; font-size: 1.05rem; font-weight: 700; color: #ef4444;" 
                           value="{{ old('lost_ticket_fine', $tariff->lost_ticket_fine) }}" required autocomplete="off">
                </div>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">Biaya denda administratif yang dikenakan apabila pelanggan menghilangkan karcis parkir resmi.</p>
                @error('lost_ticket_fine')
                    <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 1.5rem; gap: 1rem;">
                <a href="{{ route('pengaturan-tarif') }}" class="btn" style="background: #e2e8f0; color: #475569; text-decoration: none; padding: 0.75rem 1.5rem; font-weight: 600; border-radius: var(--radius-md);">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.25rem; font-size: 1rem; font-weight: 600; border-radius: var(--radius-md);">
                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Simpan Tarif
                </button>
            </div>
        </form>
    </div>

    <!-- Vanilla JS Script for Realtime Rupiah Formatting -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currencyInputs = document.querySelectorAll('.currency-input');

            function formatRupiah(value) {
                let numberString = value.toString().replace(/[^,\d]/g, '');
                let split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah;
            }

            currencyInputs.forEach(function(input) {
                // Format initial value on load
                input.value = formatRupiah(input.value);

                input.addEventListener('input', function(e) {
                    this.value = formatRupiah(this.value);
                });
            });
        });
    </script>
@endsection
