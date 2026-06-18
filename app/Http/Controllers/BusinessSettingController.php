<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    public function index()
    {
        $setting = BusinessSetting::first();
        return view('settings.business', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:100',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $setting = BusinessSetting::first();
        
        if (!$setting) {
            $setting = new BusinessSetting();
        }

        $setting->business_name = $request->business_name;

        if ($request->hasFile('business_logo')) {
            // Hapus logo lama jika ada
            if ($setting->business_logo && Storage::disk('public')->exists($setting->business_logo)) {
                Storage::disk('public')->delete($setting->business_logo);
            }

            // Simpan logo baru
            $path = $request->file('business_logo')->store('logos', 'public');
            $setting->business_logo = $path;
        }

        $setting->save();

        return back()->with('success', 'Pengaturan bisnis berhasil diperbarui.');
    }

    public function removeLogo()
    {
        $setting = BusinessSetting::first();
        
        if ($setting && $setting->business_logo) {
            if (Storage::disk('public')->exists($setting->business_logo)) {
                Storage::disk('public')->delete($setting->business_logo);
            }
            $setting->business_logo = null;
            $setting->save();
        }

        return back()->with('success', 'Logo bisnis berhasil dihapus.');
    }
}
