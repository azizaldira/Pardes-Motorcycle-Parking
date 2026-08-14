<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTariffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'base_rate' => $this->has('base_rate') ? str_replace(['.', ','], '', $this->base_rate) : null,
            'hourly_rate' => $this->has('hourly_rate') ? str_replace(['.', ','], '', $this->hourly_rate) : null,
            'overnight_rate' => $this->has('overnight_rate') ? str_replace(['.', ','], '', $this->overnight_rate) : null,
            'lost_ticket_fine' => $this->has('lost_ticket_fine') ? str_replace(['.', ','], '', $this->lost_ticket_fine) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'base_rate' => ['required', 'numeric', 'min:500'],
            'hourly_rate' => ['required', 'numeric', 'min:500'],
            'overnight_rate' => ['required', 'numeric', 'min:500'],
            'lost_ticket_fine' => ['required', 'numeric', 'min:500'],
        ];
    }

    /**
     * Custom error messages for validator.
     */
    public function messages(): array
    {
        return [
            'base_rate.required' => 'Tarif 6 jam pertama wajib diisi.',
            'base_rate.numeric' => 'Tarif 6 jam pertama harus berupa angka.',
            'base_rate.min' => 'Tarif minimal adalah Rp 500.',
            'hourly_rate.required' => 'Tarif per jam berikutnya wajib diisi.',
            'hourly_rate.numeric' => 'Tarif per jam berikutnya harus berupa angka.',
            'hourly_rate.min' => 'Tarif minimal adalah Rp 500.',
            'overnight_rate.required' => 'Tarif menginap per malam wajib diisi.',
            'overnight_rate.numeric' => 'Tarif menginap per malam harus berupa angka.',
            'overnight_rate.min' => 'Tarif minimal adalah Rp 500.',
            'lost_ticket_fine.required' => 'Denda kehilangan karcis wajib diisi.',
            'lost_ticket_fine.numeric' => 'Denda kehilangan karcis harus berupa angka.',
            'lost_ticket_fine.min' => 'Denda minimal adalah Rp 500.',
        ];
    }
}
