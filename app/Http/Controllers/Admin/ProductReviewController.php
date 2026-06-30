<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * Menampilkan semua ulasan untuk dimoderasi
     */
    public function index(Request $request)
    {
        $query = ProductReview::with(['user', 'menu', 'order'])->latest();

        if ($request->filled('status')) {
            if ($request->status === 'hidden') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        $reviews = $query->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Menyembunyikan / Menampilkan ulasan
     */
    public function toggleApproval($id)
    {
        $review = ProductReview::findOrFail($id);
        
        $review->is_approved = !$review->is_approved;
        $review->save(); // Akan men-trigger event saved untuk recalculate rating

        $status = $review->is_approved ? 'ditampilkan' : 'disembunyikan';
        return back()->with('success', "Ulasan berhasil {$status}.");
    }
}
