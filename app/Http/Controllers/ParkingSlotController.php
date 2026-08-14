<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ParkingSlot;
use App\Models\ParkingTransaction;
use App\Http\Requests\UpdateParkingSlotRequest;

class ParkingSlotController extends Controller
{
    /**
     * Tampilkan form Pengaturan Slot Parkir Global.
     */
    public function index()
    {
        $slot = ParkingSlot::first();
        if (!$slot) {
            $slot = ParkingSlot::create([
                'name' => 'Kapasitas Parkir Global',
                'total_slots' => 200,
                'status' => 'active',
            ]);
        }

        // Menghitung slot terpakai saat ini dari transaksi aktif
        $usedSlots = ParkingTransaction::where('status', 'parked')->sum('slot_used');
        $availableSlots = max(0, $slot->total_slots - $usedSlots);
        $percentage = $slot->total_slots > 0 ? round(($usedSlots / $slot->total_slots) * 100, 1) : 0;

        return view('parking-slots.index', compact('slot', 'usedSlots', 'availableSlots', 'percentage'));
    }

    /**
     * Perbarui nilai kapasitas total slot parkir global.
     */
    public function update(UpdateParkingSlotRequest $request)
    {
        $slot = ParkingSlot::first();
        if ($slot) {
            $slot->update([
                'total_slots' => $request->total_slots,
            ]);
        } else {
            ParkingSlot::create([
                'name' => 'Kapasitas Parkir Global',
                'total_slots' => $request->total_slots,
                'status' => 'active',
            ]);
        }

        return redirect()->route('pengaturan-slot')->with('success', 'Kapasitas total slot parkir global berhasil diperbarui.');
    }
}
