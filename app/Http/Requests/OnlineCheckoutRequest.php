<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OnlineCheckoutRequest extends FormRequest
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
     * Digunakan untuk checkout Online (Delivery / Pickup).
     * Pembayaran WAJIB di muka — tidak ada opsi COD.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_whatsapp' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'order_type' => 'required|in:delivery,pickup',
            'address_id' => 'required_if:order_type,delivery',
            'new_address' => 'required_if:address_id,new|nullable|string|max:500',
            'latitude' => 'required_if:address_id,new|nullable|numeric',
            'longitude' => 'required_if:address_id,new|nullable|numeric',
            'payment_method' => 'required|in:bank_transfer,ewallet,qris_online',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pemesan wajib diisi.',
            'customer_whatsapp.required' => 'No. WhatsApp wajib diisi.',
            'address_id.required_if' => 'Silakan pilih alamat pengiriman.',
            'new_address.required_if' => 'Alamat lengkap wajib diisi.',
            'latitude.required_if' => 'Anda wajib menandai lokasi rumah Anda di Peta.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Pesanan online hanya bisa dibayar via Bank Transfer, e-Wallet, atau QRIS.',
        ];
    }
}
