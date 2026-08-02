@extends('layouts.app')

@section('content')
    <div style="padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div style="flex: 1; min-width: 250px;">
                <h1 class="page-title" style="margin-bottom: 0.25rem; font-size: 1.5rem; font-weight: 600; color: var(--text-main);">Laporan Analitik</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Pantau kinerja operasional, pendapatan, dan staf secara real-time</p>
            </div>

            <!-- Tombol Ekspor Dinamis -->
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('laporan.pdf', ['type' => $type, 'period' => $period, 'date_filter' => $dateFilter]) }}" class="btn" style="background-color: #dc2626; color: white; border: none; padding: 0.6rem 1.25rem; border-radius: var(--radius-md); font-weight: 500; font-size: 0.875rem;" target="_blank">
                    <i class="fas fa-file-pdf" style="margin-right: 0.5rem;"></i> Export PDF
                </a>
                <a href="{{ route('laporan.excel', ['type' => $type, 'period' => $period, 'date_filter' => $dateFilter]) }}" class="btn" style="background-color: #16a34a; color: white; border: none; padding: 0.6rem 1.25rem; border-radius: var(--radius-md); font-weight: 500; font-size: 0.875rem;">
                    <i class="fas fa-file-excel" style="margin-right: 0.5rem;"></i> Export Excel
                </a>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="table-card" style="padding: 1.5rem; margin-bottom: 2rem; background-color: white;">
            <form action="{{ route('laporan') }}" method="GET" style="display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-main); margin-bottom: 0.5rem;">Jenis Laporan</label>
                    <select name="type" class="form-input" style="width: 100%;">
                        <option value="pendapatan" {{ $type === 'pendapatan' ? 'selected' : '' }}>💰 Pendapatan</option>
                        <option value="kendaraan" {{ $type === 'kendaraan' ? 'selected' : '' }}>🏍️ Kendaraan Masuk & Keluar</option>
                        <option value="karcis_hilang" {{ $type === 'karcis_hilang' ? 'selected' : '' }}>🎫 Kasus Karcis Hilang</option>
                        <option value="petugas" {{ $type === 'petugas' ? 'selected' : '' }}>👥 Kinerja Petugas</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 150px;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-main); margin-bottom: 0.5rem;">Periode</label>
                    <select name="period" id="periodSelect" class="form-input" style="width: 100%;" onchange="updateDateFilterInput()">
                        <option value="harian" {{ $period === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="bulanan" {{ $period === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="tahunan" {{ $period === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 200px;" id="dateFilterContainer">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-main); margin-bottom: 0.5rem;">Pilih Waktu</label>
                    <!-- Input will be dynamically replaced by JS based on Period -->
                    <input type="text" name="date_filter" id="dateFilterInput" value="{{ $dateFilter }}" class="form-input" style="width: 100%;">
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem;">
                        <i class="fas fa-filter" style="margin-right: 0.5rem;"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-main); margin-bottom: 1.5rem;">Ringkasan Periode: {{ $periodeStr }}</h2>

        <!-- DYNAMIC METRICS CARDS -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 3rem;">
            @if($type === 'pendapatan')
                <!-- Pendapatan Metrics -->
                <div style="background-color: #f0fdf4; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #16a34a; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-wallet" style="margin-right: 0.5rem;"></i> Total Pendapatan</div>
                    <div style="font-size: 1.75rem; font-weight: 600; color: #14532d;">Rp {{ number_format($metrics['total_pendapatan'], 0, ',', '.') }}</div>
                </div>
                <div style="background-color: #eff6ff; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #3b82f6; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-receipt" style="margin-right: 0.5rem;"></i> Jumlah Transaksi</div>
                    <div style="font-size: 2rem; font-weight: 600; color: #1e3a8a;">{{ number_format($metrics['jumlah_transaksi'], 0, ',', '.') }}</div>
                </div>
                <div style="background-color: #faf5ff; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #9333ea; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-chart-pie" style="margin-right: 0.5rem;"></i> Rata-rata per Transaksi</div>
                    <div style="font-size: 1.75rem; font-weight: 600; color: #581c87;">Rp {{ number_format($metrics['rata_pendapatan'], 0, ',', '.') }}</div>
                </div>
                <div style="background-color: #fff7ed; padding: 1.5rem; border-radius: var(--radius-lg); opacity: 0.5;">
                    <div style="color: #ea580c; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-minus" style="margin-right: 0.5rem;"></i> -</div>
                    <div style="font-size: 1.75rem; font-weight: 600; color: #9a3412;">-</div>
                </div>

            @elseif($type === 'kendaraan')
                <!-- Kendaraan Metrics -->
                <div style="background-color: #eff6ff; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #3b82f6; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i> Total Kendaraan Masuk</div>
                    <div style="font-size: 2rem; font-weight: 600; color: #1e3a8a;">{{ number_format($metrics['total_masuk'], 0, ',', '.') }}</div>
                </div>
                <div style="background-color: #f0fdf4; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #16a34a; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-sign-out-alt" style="margin-right: 0.5rem;"></i> Total Kendaraan Keluar</div>
                    <div style="font-size: 2rem; font-weight: 600; color: #14532d;">{{ number_format($metrics['total_keluar'], 0, ',', '.') }}</div>
                </div>
                <div style="background-color: #fff7ed; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #ea580c; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-motorcycle" style="margin-right: 0.5rem;"></i> Total Kendaraan Aktif</div>
                    <div style="font-size: 2rem; font-weight: 600; color: #9a3412;">{{ number_format($metrics['total_aktif'], 0, ',', '.') }}</div>
                </div>
                <div style="background-color: #faf5ff; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #9333ea; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="far fa-clock" style="margin-right: 0.5rem;"></i> Rata-rata Durasi</div>
                    <div style="font-size: 1.75rem; font-weight: 600; color: #581c87;">{{ $metrics['rata_durasi'] }} jam</div>
                </div>

            @elseif($type === 'karcis_hilang')
                <!-- Karcis Hilang Metrics -->
                <div style="background-color: #fef2f2; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #dc2626; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i> Jumlah Karcis Hilang</div>
                    <div style="font-size: 2rem; font-weight: 600; color: #7f1d1d;">{{ number_format($metrics['jumlah_hilang'], 0, ',', '.') }}</div>
                </div>
                <div style="background-color: #fff7ed; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #ea580c; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-money-bill-wave" style="margin-right: 0.5rem;"></i> Total Denda Hilang</div>
                    <div style="font-size: 1.75rem; font-weight: 600; color: #9a3412;">Rp {{ number_format($metrics['total_denda'], 0, ',', '.') }}</div>
                </div>
                <div style="background-color: #faf5ff; padding: 1.5rem; border-radius: var(--radius-lg);">
                    <div style="color: #9333ea; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-percent" style="margin-right: 0.5rem;"></i> Persentase Kasus</div>
                    <div style="font-size: 2rem; font-weight: 600; color: #581c87;">{{ $metrics['persentase'] }}%</div>
                </div>
                <div style="background-color: #f3f4f6; padding: 1.5rem; border-radius: var(--radius-lg); opacity: 0.5;">
                    <div style="color: #4b5563; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.75rem;"><i class="fas fa-minus" style="margin-right: 0.5rem;"></i> -</div>
                    <div style="font-size: 1.75rem; font-weight: 600; color: #1f2937;">-</div>
                </div>
            @endif
        </div>

        <!-- DYNAMIC CHART -->
        @if($type !== 'petugas')
        <div class="table-card" style="padding: 2rem; margin-bottom: 3rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-main); margin-bottom: 1.5rem;">
                Grafik {{ ucfirst(str_replace('_', ' ', $type)) }}
            </h3>
            <div style="height: 350px; width: 100%;">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
        @endif

        <!-- DYNAMIC TABLE -->
        <div class="table-card">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-main);">
                    Detail {{ $type === 'petugas' ? 'Kinerja Petugas' : 'Transaksi' }}
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            @if($type === 'petugas')
                                <th>Nama Petugas</th>
                                <th>Kendaraan Diinput</th>
                                <th>Kendaraan Diproses Keluar</th>
                                <th>Pendapatan Diproses</th>
                                <th>Karcis Hilang Ditangani</th>
                            @else
                                <th>Waktu</th>
                                <th>Plat Nomor</th>
                                <th>Durasi</th>
                                <th>Biaya</th>
                                <th>Metode</th>
                                <th>Petugas Masuk</th>
                                <th>Petugas Keluar</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($type === 'petugas')
                            @forelse($petugasData as $p)
                            <tr>
                                <td>{{ $p->name }}</td>
                                <td>{{ number_format($p->input_count, 0, ',', '.') }}</td>
                                <td>{{ number_format($p->checkout_count, 0, ',', '.') }}</td>
                                <td style="color: #16a34a; font-weight: 500;">Rp {{ number_format($p->revenue, 0, ',', '.') }}</td>
                                <td style="color: #dc2626;">{{ number_format($p->lost_tickets, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="padding: 3rem 2rem; text-align: center; color: var(--text-muted);">
                                    <i class="fas fa-users-slash" style="font-size: 2.5rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
                                    <p style="margin: 0; font-weight: 500;">Tidak ada data staf.</p>
                                </td>
                            </tr>
                            @endforelse
                        @else
                            @forelse($transactions as $t)
                            <tr>
                                <td>
                                    {{ $t->entry_time->format('H:i') }} - {{ $t->exit_time->format('H:i') }}<br>
                                    <small style="color: #9ca3af;">{{ $t->exit_time->format('d/m/Y') }}</small>
                                </td>
                                <td>{{ $t->formatted_plate }}</td>
                                <td>{{ round($t->parking_duration / 60, 2) }} jam</td>
                                <td>Rp {{ number_format($t->parking_fee, 0, ',', '.') }}</td>
                                <td>
                                    <span style="background-color: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px;">{{ $t->payment_method == 'cash' ? 'Tunai' : 'QRIS' }}</span>
                                    @if($t->lost_ticket)
                                    <span style="background-color: #fef2f2; color: #dc2626; padding: 0.25rem 0.5rem; border-radius: 4px; margin-left: 0.5rem;"><i class="fas fa-exclamation-triangle"></i></span>
                                    @endif
                                </td>
                                <td>{{ $t->entryUser->name ?? '-' }}</td>
                                <td>{{ $t->exitUser->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="padding: 3rem 2rem; text-align: center; color: var(--text-muted);">
                                    <i class="fas fa-folder-open" style="font-size: 2rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
                                    Tidak ada data transaksi pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Form filter script
        function updateDateFilterInput() {
            const period = document.getElementById('periodSelect').value;
            const container = document.getElementById('dateFilterContainer');
            
            let html = '';
            // Get current value to persist if possible, or provide default
            const currentValue = '{{ $dateFilter }}';

            if (period === 'harian') {
                html = `<input type="date" name="date_filter" id="dateFilterInput" value="${currentValue.length === 10 ? currentValue : '{{ now()->format("Y-m-d") }}'}" class="form-input" style="width: 100%;">`;
            } else if (period === 'bulanan') {
                html = `<input type="month" name="date_filter" id="dateFilterInput" value="${currentValue.length === 7 ? currentValue : '{{ now()->format("Y-m") }}'}" class="form-input" style="width: 100%;">`;
            } else if (period === 'tahunan') {
                // Year input using number type
                html = `<input type="number" name="date_filter" id="dateFilterInput" value="${currentValue.length === 4 ? currentValue : '{{ now()->format("Y") }}'}" min="2020" max="2100" class="form-input" style="width: 100%;">`;
            }
            
            container.innerHTML = `<label style="display: block; font-size: 0.875rem; font-weight: 500; color: var(--text-main); margin-bottom: 0.5rem;">Pilih Waktu</label>` + html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Trigger once to set correct input type based on selected period on page load
            updateDateFilterInput();

            @if($type !== 'petugas')
            const labels = {!! json_encode($chartLabels) !!};
            const data = {!! json_encode($chartData) !!};
            const type = '{{ $type }}';
            
            const ctx = document.getElementById('mainChart').getContext('2d');
            
            let chartType = 'bar';
            let bgColor = '#3b82f6';
            let borderColor = '#2563eb';
            let fill = false;
            let labelName = 'Data';

            if (type === 'pendapatan') {
                chartType = 'line';
                borderColor = '#16a34a';
                bgColor = 'rgba(22, 163, 74, 0.1)';
                fill = true;
                labelName = 'Pendapatan (Rp)';
            } else if (type === 'kendaraan') {
                chartType = 'bar';
                bgColor = '#4285F4';
                labelName = 'Kendaraan Masuk';
            } else if (type === 'karcis_hilang') {
                chartType = 'bar';
                bgColor = '#dc2626';
                labelName = 'Kasus Karcis Hilang';
            }

            new Chart(ctx, {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: [{
                        label: labelName,
                        data: data,
                        backgroundColor: bgColor,
                        borderColor: borderColor,
                        borderWidth: 2,
                        borderRadius: chartType === 'bar' ? 4 : 0,
                        fill: fill,
                        pointBackgroundColor: borderColor,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#e5e7eb' },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: true }
                        }
                    }
                }
            });
            @endif
        });
    </script>
@endsection
