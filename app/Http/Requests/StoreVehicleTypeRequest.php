<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:vehicle_types,name'],
            'category' => ['required', 'string', 'in:Golongan I,Golongan II,Golongan III'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama jenis motor wajib diisi.',
            'name.unique' => 'Nama jenis motor sudah terdaftar di sistem.',
            'category.required' => 'Golongan kendaraan wajib dipilih.',
            'category.in' => 'Pilihan golongan tidak valid.',
            'status.required' => 'Status aktivasi wajib ditentukan.',
            'status.in' => 'Pilihan status tidak valid.',
        ];
    }
}
