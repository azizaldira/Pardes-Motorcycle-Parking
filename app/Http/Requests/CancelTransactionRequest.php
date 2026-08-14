<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelTransactionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [];
    }
}
