<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\MidtransWebhookController;

// Return a list of menus for Android/iOS apps
Route::get('/menus', [MenuController::class, 'index']);

// Return a specific menu by ID
Route::get('/menus/{id}', [MenuController::class, 'show']);

// ═══════════════════════════════════════════════════════════════
// MIDTRANS WEBHOOK — Server-to-Server notification
// Endpoint ini dipanggil oleh server Midtrans, bukan browser.
// CSRF protection di-bypass karena ini bukan form submission.
// ═══════════════════════════════════════════════════════════════
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handle']);

// ═══════════════════════════════════════════════════════════════
// ORDER STATUS POLLING — Safe read-only endpoint
// Digunakan oleh frontend untuk polling status order (pengganti
// client-side payment callback yang tidak aman).
// ═══════════════════════════════════════════════════════════════
Route::get('/orders/{id}/status', function ($id) {
    $order = \App\Models\Order::findOrFail($id);
    return response()->json([
        'order_status' => $order->order_status,
        'payment_status' => $order->payment_status,
        'order_status_label' => $order->order_status_label,
        'payment_status_label' => $order->payment_status_label,
        'remaining_seconds' => $order->remaining_payment_seconds,
    ]);
});

