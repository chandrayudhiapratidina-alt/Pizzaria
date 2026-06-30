<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreReview;
use Illuminate\Http\Request;

class StoreReviewController extends Controller
{
    public function index()
    {
        $reviews = StoreReview::with('user')->latest()->get();
        return view('admin.store_reviews.index', compact('reviews'));
    }

    public function toggleApproval(Request $request, $id)
    {
        $review = StoreReview::findOrFail($id);
        $review->is_approved = !$review->is_approved;
        $review->save();

        return redirect()->back()->with('success', 'Status ulasan berhasil diubah.');
    }

    public function create()
    {
        return view('admin.store_reviews.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review_type' => 'required|string',
            'comment' => 'nullable|string',
            'is_approved' => 'boolean'
        ]);

        StoreReview::create([
            'guest_name' => $request->guest_name,
            'rating' => $request->rating,
            'review_type' => $request->review_type,
            'comment' => $request->comment,
            'is_approved' => $request->has('is_approved') ? 1 : 0
        ]);

        return redirect()->route('admin.store_reviews.index')->with('success', 'Ulasan baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $review = StoreReview::findOrFail($id);
        return view('admin.store_reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $review = StoreReview::findOrFail($id);
        
        $request->validate([
            'guest_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review_type' => 'required|string',
            'comment' => 'nullable|string',
            'is_approved' => 'boolean'
        ]);

        $review->update([
            'guest_name' => $request->guest_name,
            'rating' => $request->rating,
            'review_type' => $request->review_type,
            'comment' => $request->comment,
            'is_approved' => $request->has('is_approved') ? 1 : 0
        ]);

        return redirect()->route('admin.store_reviews.index')->with('success', 'Ulasan berhasil diperbarui.');
    }
    
    public function destroy($id)
    {
        $review = StoreReview::findOrFail($id);
        $review->delete();
        
        return redirect()->back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
