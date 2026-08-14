<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleType;
use App\Http\Requests\StoreVehicleTypeRequest;
use App\Http\Requests\UpdateVehicleTypeRequest;

class VehicleTypeController extends Controller
{
    /**
     * Tampilkan daftar jenis motor beserta fitur pencarian dan filter.
     */
    public function index(Request $request)
    {
        $query = VehicleType::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $vehicleTypes = $query->orderBy('category')->orderBy('name')->get();

        return view('vehicle-types.index', compact('vehicleTypes'));
    }

    /**
     * Tampilkan form pembuatan jenis motor baru.
     */
    public function create()
    {
        return view('vehicle-types.create');
    }

    /**
     * Simpan jenis motor baru dengan aturan mutlak slot_required.
     */
    public function store(StoreVehicleTypeRequest $request)
    {
        // Aturan bisnis mutlak: Golongan I = 1 slot, II = 2 slot, III = 3 slot
        $slotRequired = match($request->category) {
            'Golongan I' => 1,
            'Golongan II' => 2,
            'Golongan III' => 3,
            default => 1,
        };

        $vt = VehicleType::create([
            'name' => $request->name,
            'category' => $request->category,
            'slot_required' => $slotRequired,
            'status' => $request->status,
        ]);

        return redirect()->route('vehicle-types.index')->with('success', "Jenis motor '{$vt->name}' berhasil ditambahkan (Otomatis: {$slotRequired} Slot, menggunakan tarif {$request->category}).");
    }

    /**
     * Tampilkan form edit jenis motor.
     */
    public function edit(VehicleType $vehicleType)
    {
        return view('vehicle-types.edit', compact('vehicleType'));
    }

    /**
     * Perbarui data jenis motor dengan aturan mutlak slot_required.
     */
    public function update(UpdateVehicleTypeRequest $request, VehicleType $vehicleType)
    {
        // Aturan bisnis mutlak untuk slot_required
        $slotRequired = match($request->category) {
            'Golongan I' => 1,
            'Golongan II' => 2,
            'Golongan III' => 3,
            default => 1,
        };

        $vehicleType->update([
            'name' => $request->name,
            'category' => $request->category,
            'slot_required' => $slotRequired,
            'status' => $request->status,
        ]);

        return redirect()->route('vehicle-types.index')->with('success', "Data jenis motor '{$vehicleType->name}' berhasil diperbarui.");
    }

    /**
     * Ubah status aktif/nonaktif jenis motor tanpa menghapus permanen.
     */
    public function toggleStatus(VehicleType $vehicleType)
    {
        $newStatus = $vehicleType->status === 'active' ? 'inactive' : 'active';
        $vehicleType->update(['status' => $newStatus]);

        $msg = $newStatus === 'active' ? "Jenis motor '{$vehicleType->name}' berhasil diaktifkan kembali." : "Jenis motor '{$vehicleType->name}' telah dinonaktifkan dan tidak akan muncul di opsi kendaraan masuk.";

        return redirect()->route('vehicle-types.index')->with('success', $msg);
    }
}
