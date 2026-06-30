<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Digunakan untuk checkout Dine-In (QR Code).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'nullable|string|max:255',
            'customer_whatsapp' => 'nullable|string|max:20',
            'table_id' => 'nullable|exists:tables,id',
            'order_type' => 'required|in:dine_in',
            // Payment method tidak diperlukan untuk dine-in (bayar nanti di kasir)
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [];
    }
}
