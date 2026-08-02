<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\ParkingTransaction;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'pendapatan'); // pendapatan, kendaraan, karcis_hilang, petugas
        $period = $request->query('period', 'harian'); // harian, bulanan, tahunan
        
        $dateFilter = $request->query('date_filter');
        
        if (!$dateFilter) {
            if ($period === 'harian') $dateFilter = now()->format('Y-m-d');
            elseif ($period === 'bulanan') $dateFilter = now()->format('Y-m');
            elseif ($period === 'tahunan') $dateFilter = now()->format('Y');
        }

        $startDate = now();
        $endDate = now();
        $periodeStr = '';

        if ($period === 'harian') {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $dateFilter)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $dateFilter)->endOfDay();
                $periodeStr = $startDate->translatedFormat('d F Y');
            } catch (\Exception $e) {
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                $periodeStr = $startDate->translatedFormat('d F Y');
                $dateFilter = now()->format('Y-m-d');
            }
        } elseif ($period === 'bulanan') {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $dateFilter)->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-m', $dateFilter)->endOfMonth();
                $periodeStr = $startDate->translatedFormat('F Y');
            } catch (\Exception $e) {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $periodeStr = $startDate->translatedFormat('F Y');
                $dateFilter = now()->format('Y-m');
            }
        } elseif ($period === 'tahunan') {
            try {
                $startDate = Carbon::createFromFormat('Y', $dateFilter)->startOfYear();
                $endDate = Carbon::createFromFormat('Y', $dateFilter)->endOfYear();
                $periodeStr = $startDate->translatedFormat('Y');
            } catch (\Exception $e) {
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                $periodeStr = $startDate->translatedFormat('Y');
                $dateFilter = now()->format('Y');
            }
        }

        $chartLabels = [];
        $chartData = [];

        // Fill labels
        if ($period === 'harian') {
            for ($i = 0; $i < 24; $i++) {
                $chartLabels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            }
        } elseif ($period === 'bulanan') {
            for ($i = 1; $i <= $startDate->daysInMonth; $i++) {
                $chartLabels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
            }
        } elseif ($period === 'tahunan') {
            for ($i = 1; $i <= 12; $i++) {
                $chartLabels[] = Carbon::create()->month($i)->translatedFormat('M');
            }
        }

        $metrics = [];
        $transactions = collect(); // For detail table
        $petugasData = collect(); // For petugas report

        if ($type === 'pendapatan') {
            $transactions = ParkingTransaction::with(['entryUser', 'exitUser'])
                            ->where('status', 'completed')
                            ->whereBetween('exit_time', [$startDate, $endDate])
                            ->orderBy('exit_time', 'desc')
                            ->get();

            $metrics['total_pendapatan'] = $transactions->sum('parking_fee');
            $metrics['jumlah_transaksi'] = $transactions->count();
            $metrics['rata_pendapatan'] = $metrics['jumlah_transaksi'] > 0 ? $metrics['total_pendapatan'] / $metrics['jumlah_transaksi'] : 0;
            
            // Build chart
            $grouped = $transactions->groupBy(function($item) use ($period) {
                if ($period === 'harian') return $item->exit_time->format('H:00');
                if ($period === 'bulanan') return $item->exit_time->format('d');
                if ($period === 'tahunan') return $item->exit_time->translatedFormat('M');
            });
            
            foreach ($chartLabels as $label) {
                $chartData[] = $grouped->get($label, collect())->sum('parking_fee');
            }
        } 
        elseif ($type === 'kendaraan') {
            $completedTransactions = ParkingTransaction::with(['entryUser', 'exitUser'])
                            ->where('status', 'completed')
                            ->whereBetween('exit_time', [$startDate, $endDate])
                            ->orderBy('exit_time', 'desc')
                            ->get();
            
            $masukTransactions = ParkingTransaction::whereBetween('entry_time', [$startDate, $endDate])->get();
            $aktifTransactions = ParkingTransaction::where('status', 'parked')
                            ->whereBetween('entry_time', [$startDate, $endDate])->count();
                            
            $metrics['total_keluar'] = $completedTransactions->count();
            $metrics['total_masuk'] = $masukTransactions->count();
            $metrics['total_aktif'] = $aktifTransactions;
            
            $avgDurationMinutes = $metrics['total_keluar'] > 0 ? $completedTransactions->avg('parking_duration') : 0;
            $metrics['rata_durasi'] = round($avgDurationMinutes / 60, 2);

            $transactions = $completedTransactions;

            // Build chart for Masuk
            $grouped = $masukTransactions->groupBy(function($item) use ($period) {
                if ($period === 'harian') return $item->entry_time->format('H:00');
                if ($period === 'bulanan') return $item->entry_time->format('d');
                if ($period === 'tahunan') return $item->entry_time->translatedFormat('M');
            });
            
            foreach ($chartLabels as $label) {
                $chartData[] = $grouped->get($label, collect())->count();
            }
        }
        elseif ($type === 'karcis_hilang') {
            $transactions = ParkingTransaction::with(['entryUser', 'exitUser'])
                            ->where('status', 'completed')
                            ->whereBetween('exit_time', [$startDate, $endDate])
                            ->orderBy('exit_time', 'desc')
                            ->get();
            
            $lostTransactions = $transactions->where('lost_ticket', true);

            $metrics['jumlah_hilang'] = $lostTransactions->count();
            $metrics['total_denda'] = $lostTransactions->sum('lost_ticket_fine_amount');
            $metrics['total_transaksi'] = $transactions->count();
            $metrics['persentase'] = $metrics['total_transaksi'] > 0 ? round(($metrics['jumlah_hilang'] / $metrics['total_transaksi']) * 100, 1) : 0;

            // Build chart
            $grouped = $lostTransactions->groupBy(function($item) use ($period) {
                if ($period === 'harian') return $item->exit_time->format('H:00');
                if ($period === 'bulanan') return $item->exit_time->format('d');
                if ($period === 'tahunan') return $item->exit_time->translatedFormat('M');
            });
            
            foreach ($chartLabels as $label) {
                $chartData[] = $grouped->get($label, collect())->count();
            }
        }
        elseif ($type === 'petugas') {
            // No chart for petugas
            $staffs = User::where('role', 'staff')->get();
            $allEntries = ParkingTransaction::whereBetween('entry_time', [$startDate, $endDate])->get();
            $allExits = ParkingTransaction::whereBetween('exit_time', [$startDate, $endDate])->where('status', 'completed')->get();

            foreach ($staffs as $staff) {
                $entriesCount = $allEntries->where('entry_user_id', $staff->id)->count();
                $exits = $allExits->where('exit_user_id', $staff->id);
                
                $petugasData->push((object)[
                    'name' => $staff->name,
                    'input_count' => $entriesCount,
                    'checkout_count' => $exits->count(),
                    'revenue' => $exits->sum('parking_fee'),
                    'lost_tickets' => $exits->where('lost_ticket', true)->count(),
                ]);
            }
        }

        $data = compact('type', 'period', 'dateFilter', 'periodeStr', 'metrics', 'chartLabels', 'chartData', 'transactions', 'petugasData');
        
        if ($request->routeIs('laporan.pdf')) {
            $pdf = Pdf::loadView('report.pdf', $data);
            return $pdf->download("Laporan_{$type}_{$periodeStr}.pdf");
        }
        
        if ($request->routeIs('laporan.excel')) {
            return $this->generateCsv($data);
        }

        return view('report.index', $data);
    }

    public function exportPdf(Request $request)
    {
        return $this->index($request);
    }

    public function exportExcel(Request $request)
    {
        return $this->index($request);
    }

    private function generateCsv($data)
    {
        $filename = "Laporan_{$data['type']}_{$data['periodeStr']}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($data) {
            $file = fopen('php://output', 'w');
            
            if ($data['type'] === 'petugas') {
                fputcsv($file, ['Nama Petugas', 'Kendaraan Diinput', 'Kendaraan Keluar', 'Pendapatan', 'Karcis Hilang']);
                foreach ($data['petugasData'] as $p) {
                    fputcsv($file, [
                        $p->name,
                        $p->input_count,
                        $p->checkout_count,
                        $p->revenue,
                        $p->lost_tickets
                    ]);
                }
            } else {
                fputcsv($file, ['Waktu Keluar', 'Plat Nomor', 'Jenis Motor', 'Durasi (Menit)', 'Biaya', 'Metode Bayar', 'Karcis Hilang', 'Petugas Masuk', 'Petugas Keluar']);
                foreach ($data['transactions'] as $t) {
                    fputcsv($file, [
                        $t->exit_time->format('d/m/Y H:i'),
                        $t->plate_number,
                        $t->vehicle_type,
                        $t->parking_duration,
                        $t->parking_fee,
                        $t->payment_method,
                        $t->lost_ticket ? 'Ya' : 'Tidak',
                        $t->entryUser->name ?? 'Sistem',
                        $t->exitUser->name ?? 'Sistem'
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
