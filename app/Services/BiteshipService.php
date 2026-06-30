<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.biteship.com/v1';

    public function __construct()
    {
        $this->apiKey = Setting::get('biteship_api_key', '') ?: env('BITESHIP_API_KEY', '');
    }

    /**
     * Get delivery rates from Biteship.
     */
    public function getRates($originLat, $originLng, $destLat, $destLng, $weight = 0.5)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/rates/couriers", [
                'origin_latitude' => $originLat,
                'origin_longitude' => $originLng,
                'destination_latitude' => $destLat,
                'destination_longitude' => $destLng,
                'couriers' => 'gojek',
                'items' => [
                    ['name' => 'Pesanan Pizzaria', 'quantity' => 1, 'value' => 50000, 'weight' => $weight],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $pricing = $data['pricing']['gojek']['instant'] ?? null;
                if ($pricing) {
                    return [
                        'success' => true,
                        'fee' => $pricing['total_fee'] ?? 0,
                        'courier_company' => 'gojek',
                        'courier_type' => 'instant',
                        'message' => 'Berhasil mendapatkan tarif.',
                    ];
                }
                Log::error('Biteship: No Gojek Instant pricing found', $data);
                return ['success' => false, 'message' => 'Tidak ada tarif Gojek Instant tersedia.'];
            }

            Log::error('Biteship Check Rates Error: ' . $response->body());
            return ['success' => false, 'message' => 'Gagal mendapatkan tarif pengiriman.'];

        } catch (\Exception $e) {
            Log::error('Biteship Check Rates Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat menghubungi server pengiriman.'];
        }
    }

    /**
     * Create delivery order in Biteship.
     */
    public function createOrder($order)
    {
        try {
            $originLat = Setting::get('store_latitude', '-7.4116');
            $originLng = Setting::get('store_longitude', '109.2638');

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/orders", [
                'shipper_contact_name' => 'Pizzaria',
                'shipper_contact_phone' => '081234567890',
                'shipper_organization' => 'Pizzaria',
                'origin_contact_name' => 'Pizzaria',
                'origin_contact_phone' => '081234567890',
                'origin_address' => 'Jl. Jenderal Sudirman No. 10, Purwokerto',
                'origin_coordinate' => [
                    'latitude' => (float) $originLat,
                    'longitude' => (float) $originLng,
                ],
                'destination_contact_name' => $order->customer_name,
                'destination_contact_phone' => $order->customer_whatsapp,
                'destination_address' => $order->customer_address,
                'destination_coordinate' => [
                    'latitude' => (float) ($order->latitude ?? $originLat),
                    'longitude' => (float) ($order->longitude ?? $originLng),
                ],
                'courier_company' => 'gojek',
                'courier_type' => 'instant',
                'delivery_type' => 'now',
                'items' => [
                    ['name' => 'Pesanan Pizzaria #' . $order->order_number, 'quantity' => 1, 'value' => (int) $order->total_amount, 'weight' => 500],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $order->biteshipDelivery()->create([
                    'biteship_order_id' => $data['id'] ?? null,
                    'biteship_tracking_id' => $data['tracking_id'] ?? null,
                    'waybill_id' => $data['waybill_id'] ?? null,
                    'courier_company' => 'gojek',
                    'courier_type' => 'instant',
                    'driver_name' => $data['driver']['name'] ?? null,
                    'driver_phone' => $data['driver']['phone'] ?? null,
                    'driver_photo_url' => $data['driver']['photo_url'] ?? null,
                    'live_tracking_url' => $data['live_tracking']['url'] ?? null,
                    'status' => $data['status'] ?? 'created',
                    'raw_response' => json_encode($data),
                ]);
                return ['success' => true, 'message' => 'Pesanan Biteship berhasil dibuat.'];
            }

            Log::error('Biteship Create Order Error: ' . $response->body());
            return ['success' => false, 'message' => 'Gagal membuat pesanan Biteship.'];

        } catch (\Exception $e) {
            Log::error('Biteship Create Order Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Get tracking info from Biteship.
     */
    public function getTracking($trackingId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/trackings/{$trackingId}");

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'message' => 'Gagal mendapatkan tracking.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
