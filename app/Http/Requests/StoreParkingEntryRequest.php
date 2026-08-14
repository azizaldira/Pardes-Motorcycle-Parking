<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreParkingEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'staff';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plate_number' => [
                'required',
                'string',
                'regex:/^[A-Za-z]{1,2}\s*[0-9]{3,4}\s*[A-Za-z]{2,3}$/',
            ],
            'vehicle_type_id' => [
                'required',
                Rule::exists('vehicle_types', 'id')->where(function ($query) {
                    $query->where('status', 'active');
                }),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'plate_number.required' => 'Plat nomor kendaraan wajib diisi.',
            'plate_number.regex' => 'Format plat nomor tidak valid. Gunakan format standar Indonesia (contoh: B 1234 KNJ, 1–2 huruf depan, 3–4 angka, dan 2–3 huruf belakang).',
            'vehicle_type_id.required' => 'Silakan pilih jenis motor yang masuk.',
            'vehicle_type_id.exists' => 'Jenis motor yang dipilih tidak ditemukan atau sedang tidak aktif dalam sistem.',
        ];
    }
}
