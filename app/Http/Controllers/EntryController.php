<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreParkingEntryRequest;
use App\Models\BusinessSetting;
use App\Models\ParkingTransaction;
use App\Models\VehicleType;
use App\Models\ParkingSlot;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EntryController extends Controller
{
    /**
     * Menampilkan halaman pemantauan (Admin) & input kendaraan (Staff)
     * serta daftar kendaraan parkir aktif.
     */
    public function index()
    {
        // Eager load relasi entryUser dan vehicleType untuk optimalisasi performa database
        $activeTransactions = ParkingTransaction::with(['entryUser', 'vehicleType'])
                                ->where('status', 'parked')
                                ->orderBy('entry_time', 'desc')
                                ->get()
                                ->map(function ($item) {
                                    $diff = $item->entry_time->diff(now());
                                    $hours = ($diff->days * 24) + $diff->h;
                                    $minutes = $diff->i;
                                    $item->formatted_duration = "{$hours} Jam {$minutes} Menit";
                                    return $item;
                                });

        $newTicket = null;
        if (session('new_ticket_id')) {
            $newTicket = ParkingTransaction::with(['entryUser', 'vehicleType'])->find(session('new_ticket_id'));
        }

        // Ambil jenis motor yang aktif beserta relasi tarif untuk di-cache / diolah di view
        $vehicleTypes = VehicleType::with('tariff')
                            ->where('status', 'active')
                            ->orderBy('category')
                            ->orderBy('name')
                            ->get();

        // Hitung kapasitas slot global
        $slot = ParkingSlot::first();
        $total_slots = $slot ? $slot->total_slots : 0;
        $used_slots = ParkingTransaction::where('status', 'parked')->sum('slot_used');
        $available_slots = max(0, $total_slots - $used_slots);
        $percentage = $total_slots > 0 ? round(($used_slots / $total_slots) * 100, 1) : 0;

        $business = BusinessSetting::first();

        return view('entry.index', compact(
            'activeTransactions', 
            'newTicket', 
            'vehicleTypes', 
            'total_slots', 
            'used_slots', 
            'available_slots', 
            'percentage',
            'business'
        ));
    }

    /**
     * Menyimpan transaksi kendaraan masuk baru ke dalam database (Khusus Staff/Petugas).
     */
    public function store(StoreParkingEntryRequest $request)
    {
        $normalizedPlate = strtoupper(preg_replace('/\s+/', '', $request->plate_number));

        // 1. Cek apakah ada kendaraan dengan plat sama yang masih berstatus parkir aktif
        $existing = ParkingTransaction::where('plate_number', $normalizedPlate)
                        ->where('status', 'parked')
                        ->first();

        if ($existing) {
            return back()->withInput()->withErrors([
                'plate_number' => 'Kendaraan dengan pelat nomor ini masih berstatus parkir aktif di dalam area.'
            ]);
        }

        // 2. Ambil data tipe motor aktif beserta spesifikasi kebutuhan slot
        $vt = VehicleType::where('id', $request->vehicle_type_id)
                ->where('status', 'active')
                ->firstOrFail();

        $slotRequired = $vt->slot_required;

        // 3. Validasi ketersediaan kapasitas slot global
        $slot = ParkingSlot::first();
        $totalSlots = $slot ? $slot->total_slots : 0;
        $usedSlots = ParkingTransaction::where('status', 'parked')->sum('slot_used');
        $availableSlots = max(0, $totalSlots - $usedSlots);

        if ($availableSlots < $slotRequired) {
            return back()->withInput()->withErrors([
                'vehicle_type_id' => "Kapasitas slot parkir global tidak mencukupi! Motor '{$vt->name}' membutuhkan {$slotRequired} slot, sedangkan sisa ruang parkir tersedia tinggal {$availableSlots} slot."
            ]);
        }

        // 4. Buat Nomor Tiket (ticket_number) yang dijamin unik di database
        do {
            $ticketNumber = 'TIX-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (ParkingTransaction::where('ticket_number', $ticketNumber)->exists());

        // 5. Simpan transaksi secara permanen (tanpa fitur edit / hapus)
        $transaction = ParkingTransaction::create([
            'ticket_number' => $ticketNumber,
            'qr_code' => $normalizedPlate,
            'plate_number' => $normalizedPlate,
            'entry_user_id' => auth()->id(),
            'vehicle_type' => $vt->name,
            'vehicle_type_id' => $vt->id,
            'slot_used' => $slotRequired,
            'entry_time' => now(),
            'status' => 'parked',
            'payment_status' => 'pending',
        ]);

        return redirect()->route('kendaraan-masuk')
            ->with('success', "Kendaraan '{$normalizedPlate}' ({$vt->name} - {$slotRequired} Slot) berhasil dicatat.")
            ->with('new_ticket_id', $transaction->id);
    }

    /**
     * Mencetak karcis parkir dalam format PDF dengan QR Code (Khusus Staff/Petugas).
     */
    public function printTicketPdf($id)
    {
        // Pastikan hanya role staff yang berwenang mengeksekusi ini
        if (auth()->user()->role !== 'staff') {
            abort(403, 'Akses Ditolak: Cetak karcis hanya dapat dilakukan oleh Staff (Petugas).');
        }

        $transaction = ParkingTransaction::with(['entryUser', 'vehicleType.tariff'])->findOrFail($id);
        $business = BusinessSetting::first();

        // Generate QR Code berisi murni nomor plat kendaraan dengan margin (quiet zone) agar pemindaian kamera sangat cepat dan akurat
        $qrContent = $transaction->qr_code ?: $transaction->plate_number;
        $qrSvg = QrCode::format('svg')->size(145)->margin(2)->generate($qrContent);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $pdf = Pdf::loadView('entry.ticket-pdf', compact('transaction', 'business', 'qrBase64'));
        
        // Ukuran struk thermal printer (sekitar lebar 80mm ekivalen 226.77pt x 560pt)
        $pdf->setPaper([0, 0, 226.77, 560], 'portrait');

        return $pdf->stream("Karcis_Parkir_{$transaction->plate_number}.pdf");
    }
}
