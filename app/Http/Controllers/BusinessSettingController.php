<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    public function index()
    {
        return view('settings.business');
    }

    public function update(Request $request)
    {
        if ($request->has('business_name')) {
            $request->merge(['business_name' => trim($request->business_name)]);
        }

        $request->validate([
            'business_name' => 'required|string|min:3|max:18',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'business_name.min' => 'Nama usaha minimal terdiri dari 3 karakter.',
            'business_logo.mimes' => 'Format logo harus berupa JPG, JPEG, atau PNG.',
            'business_logo.image' => 'File yang diunggah harus berupa gambar.',
            'business_logo.max' => 'Ukuran logo maksimal 2 MB.',
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
