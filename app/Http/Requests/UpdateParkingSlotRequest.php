<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParkingSlotRequest extends FormRequest
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
            'total_slots' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'total_slots.required' => 'Kapasitas total slot parkir wajib diisi.',
            'total_slots.integer' => 'Kapasitas harus berupa angka bulat.',
            'total_slots.min' => 'Kapasitas minimal adalah 1 slot.',
        ];
    }
}
