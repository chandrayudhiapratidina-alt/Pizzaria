<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::orderBy('created_at', 'desc')->get();
        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code',
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'banner_title' => 'nullable|string|max:255',
            'banner_subtitle' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'theme_color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->has('is_active');
        $data['is_first_order_only'] = $request->has('is_first_order_only');
        $data['min_order_amount'] = $data['min_order_amount'] ?? 0;

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('promotions', 'public');
            $data['background_image'] = '/storage/' . $path;
        }

        Promotion::create($data);
        return redirect()->route('admin.promotions.index')->with('success', 'Promo berhasil ditambahkan!');
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code,' . $promotion->id,
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'banner_title' => 'nullable|string|max:255',
            'banner_subtitle' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'theme_color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['is_active'] = $request->has('is_active');
        $data['is_first_order_only'] = $request->has('is_first_order_only');
        $data['min_order_amount'] = $data['min_order_amount'] ?? 0;

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('promotions', 'public');
            $data['background_image'] = '/storage/' . $path;
        }

        $promotion->update($data);
        return redirect()->route('admin.promotions.index')->with('success', 'Promo berhasil diperbarui!');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('admin.promotions.index')->with('success', 'Promo berhasil dihapus!');
    }
}
