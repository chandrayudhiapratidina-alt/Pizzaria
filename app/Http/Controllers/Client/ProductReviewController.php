<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * Menampilkan formulir ulasan untuk sebuah pesanan
     */
    public function create(Order $order)
    {
        // Pastikan order milik user yang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Pastikan order sudah selesai
        if (!in_array($order->order_status, ['completed', 'served'])) {
            return redirect()->route('client.online.profile')->with('error', 'Pesanan belum selesai, ulasan belum dapat diberikan.');
        }

        // Ambil item unik dari pesanan ini
        $items = $order->items()->with('menu')->get()->unique('menu_id');

        // Cek mana saja yang sudah di-review di order ini
        $reviewedMenuIds = ProductReview::where('order_id', $order->id)
                                        ->where('user_id', Auth::id())
                                        ->pluck('menu_id')
                                        ->toArray();

        // Jika semua sudah di-review
        if (count($reviewedMenuIds) >= $items->count()) {
            return redirect()->route('client.online.profile')->with('success', 'Anda sudah memberikan ulasan untuk semua menu pada pesanan ini. Terima kasih!');
        }

        return view('client.reviews.create', compact('order', 'items', 'reviewedMenuIds'));
    }

    /**
     * Menyimpan ulasan ke database
     */
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->order_status, ['completed', 'served'])) {
            return redirect()->route('client.online.profile')->with('error', 'Pesanan belum selesai.');
        }

        $request->validate([
            'reviews' => 'required|array',
            'reviews.*.menu_id' => 'required|exists:menus,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'nullable|string|max:1000',
        ]);

        $savedCount = 0;

        foreach ($request->reviews as $reviewData) {
            // Cek apakah user sudah mereview menu ini di pesanan ini
            $exists = ProductReview::where('order_id', $order->id)
                        ->where('menu_id', $reviewData['menu_id'])
                        ->where('user_id', Auth::id())
                        ->exists();

            if (!$exists && isset($reviewData['rating'])) {
                ProductReview::create([
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'menu_id' => $reviewData['menu_id'],
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'] ?? null,
                    'is_approved' => true, // Post-moderation: default true
                ]);
                $savedCount++;
            }
        }

        if ($savedCount > 0) {
            return redirect()->route('client.online.profile')->with('success', 'Terima kasih! Ulasan Anda telah berhasil disimpan.');
        }

        return redirect()->route('client.online.profile')->with('error', 'Tidak ada ulasan baru yang disimpan.');
    }
}
