<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\Tariff;
use App\Models\VehicleType;
use App\Http\Requests\UpdateTariffRequest;

class TariffController extends Controller
{
    /**
     * Tampilkan daftar seluruh golongan kendaraan beserta konfigurasi tarifnya.
     */
    public function index()
    {
        // Safety fallback: pastikan Golongan I, II, dan III ada di tabel tarif
        $categories = ['Golongan I', 'Golongan II', 'Golongan III'];
        foreach ($categories as $cat) {
            if (!Tariff::where('category', $cat)->exists()) {
                $baseRates = match($cat) {
                    'Golongan I' => ['base' => 3000, 'hourly' => 500, 'overnight' => 15000, 'fine' => 15000],
                    'Golongan II' => ['base' => 6000, 'hourly' => 1000, 'overnight' => 30000, 'fine' => 30000],
                    'Golongan III' => ['base' => 9000, 'hourly' => 1500, 'overnight' => 45000, 'fine' => 45000],
                    default => ['base' => 3000, 'hourly' => 500, 'overnight' => 15000, 'fine' => 15000],
                };
                Tariff::create([
                    'category' => $cat,
                    'base_rate' => $baseRates['base'],
                    'hourly_rate' => $baseRates['hourly'],
                    'overnight_rate' => $baseRates['overnight'],
                    'lost_ticket_fine' => $baseRates['fine'],
                ]);
            }
        }

        $tariffs = Tariff::orderBy('category')->get();

        return view('tariff.index', compact('tariffs'));
    }

    /**
     * Tampilkan form edit tarif untuk golongan tertentu.
     */
    public function edit(Tariff $tariff)
    {
        return view('tariff.edit', compact('tariff'));
    }

    /**
     * Perbarui data tarif untuk golongan menggunakan nilai numerik murni dari Form Request Validation.
     */
    public function update(UpdateTariffRequest $request, Tariff $tariff)
    {
        $tariff->update($request->validated());

        return redirect()->route('pengaturan-tarif')->with('success', "Pengaturan tarif untuk '{$tariff->category}' berhasil diperbarui.");
    }
}
