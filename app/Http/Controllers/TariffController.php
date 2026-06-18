<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\Tariff;

class TariffController extends Controller
{
    public function index()
    {
        $business = BusinessSetting::first();
        $tariff = Tariff::first();
        
        return view('tariff.index', compact('business', 'tariff'));
    }

    public function update(Request $request)
    {
        // Remove formatting (dots) before validation
        $request->merge([
            'base_rate' => str_replace('.', '', $request->base_rate),
            'hourly_rate' => str_replace('.', '', $request->hourly_rate),
            'overnight_rate' => str_replace('.', '', $request->overnight_rate),
            'lost_ticket_fine' => str_replace('.', '', $request->lost_ticket_fine),
        ]);

        $validated = $request->validate([
            'base_rate' => ['required', 'numeric', 'min:0'],
            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'overnight_rate' => ['required', 'numeric', 'min:0'],
            'lost_ticket_fine' => ['required', 'numeric', 'min:0'],
        ]);

        $tariff = Tariff::first();
        if ($tariff) {
            $tariff->update($validated);
        } else {
            Tariff::create($validated);
        }

        return redirect()->route('pengaturan-tarif')->with('success', 'Pengaturan tarif berhasil diperbarui.');
    }
}
