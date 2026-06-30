<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreReview;
use Illuminate\Support\Facades\Auth;

class StoreReviewController extends Controller
{
    /**
     * Menyimpan ulasan ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_type' => 'required|in:service,ambiance,general',
            'comment' => 'nullable|string|max:1000',
            'guest_name' => 'nullable|string|max:255',
        ]);

        $data = [
            'rating' => $request->rating,
            'review_type' => $request->review_type,
            'comment' => $request->comment,
            'is_approved' => false, // Harus disetujui admin
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        } else {
            if (empty($request->guest_name)) {
                return response()->json(['success' => false, 'message' => 'Nama wajib diisi untuk pengguna publik.'], 422);
            }
            $data['guest_name'] = $request->guest_name;
        }

        StoreReview::create($data);

        if ($request->has('redirect_back')) {
            return back()->with('success', 'Terima kasih! Ulasan restoran Anda telah dikirim dan menunggu persetujuan admin.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas ulasan Anda! Ulasan akan diproses oleh tim kami.'
        ]);
    }
}
