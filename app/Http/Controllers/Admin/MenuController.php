<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Menu;
use App\Models\Category;
use App\Models\Ingredient;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        return view('admin.menus.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'base_price' => 'required|numeric',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|lt:base_price',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'category_id' => 'required|exists:categories,id',
            'daily_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data['discount_type'] = $request->input('discount_type', 'fixed');
        $data['discount_value'] = $request->input('discount_value') ?: 0;
        
        if ($data['discount_type'] === 'percentage' && $data['discount_value'] > 0) {
            $data['discount_price'] = max(0, $data['base_price'] - ($data['base_price'] * ($data['discount_value'] / 100)));
        } elseif ($data['discount_type'] === 'fixed' && $data['discount_value'] > 0) {
            $data['discount_price'] = $data['discount_value'];
        } else {
            $data['discount_price'] = null;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $data['is_available'] = $request->has('is_available');

        $menu = Menu::create($data);

        // Sync BOM (Bill of Materials / Resep)
        $this->syncIngredients($request, $menu);
        
        // Auto adjust inventory
        $menu->load('ingredients');
        $dailyStock = (int) $menu->daily_stock;
        if ($dailyStock > 0) {
            foreach ($menu->ingredients as $ing) {
                $req = $dailyStock * $ing->pivot->qty_needed;
                if ($req > 0) {
                    $ingredient = \App\Models\Ingredient::find($ing->id);
                    if ($ingredient) {
                        $ingredient->stock_qty += $req;
                        $ingredient->save();
                    }
                }
            }
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu created successfully!');
    }

    public function show(string $id) { }

    public function edit(Menu $menu)
    {
        $categories = Category::orderBy('sort_order')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        $menu->load('ingredients');
        return view('admin.menus.edit', compact('menu', 'categories', 'ingredients'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'base_price' => 'required|numeric',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|lt:base_price',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'category_id' => 'required|exists:categories,id',
            'daily_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data['discount_type'] = $request->input('discount_type', 'fixed');
        $data['discount_value'] = $request->input('discount_value') ?: 0;
        
        if ($data['discount_type'] === 'percentage' && $data['discount_value'] > 0) {
            $data['discount_price'] = max(0, $data['base_price'] - ($data['base_price'] * ($data['discount_value'] / 100)));
        } elseif ($data['discount_type'] === 'fixed' && $data['discount_value'] > 0) {
            $data['discount_price'] = $data['discount_value'];
        } else {
            $data['discount_price'] = null;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $data['is_available'] = $request->has('is_available');

        // Capture old requirements
        $menu->load('ingredients');
        $oldDailyStock = (int) $menu->daily_stock;
        $oldReqs = [];
        foreach ($menu->ingredients as $ing) {
            $oldReqs[$ing->id] = $oldDailyStock * $ing->pivot->qty_needed;
        }

        $menu->update($data);

        // Sync BOM (Bill of Materials / Resep)
        $this->syncIngredients($request, $menu);

        // Capture new requirements
        $menu->load('ingredients'); // reload after sync
        $newDailyStock = (int) $menu->daily_stock;
        $newReqs = [];
        foreach ($menu->ingredients as $ing) {
            $newReqs[$ing->id] = $newDailyStock * $ing->pivot->qty_needed;
        }

        // Apply inventory adjustments automatically
        $allIngredientIds = array_unique(array_merge(array_keys($oldReqs), array_keys($newReqs)));
        foreach ($allIngredientIds as $ingId) {
            $old = $oldReqs[$ingId] ?? 0;
            $new = $newReqs[$ingId] ?? 0;
            $diff = $new - $old;
            
            if ($diff != 0) {
                $ingredient = \App\Models\Ingredient::find($ingId);
                if ($ingredient) {
                    $ingredient->stock_qty += $diff;
                    if ($ingredient->stock_qty < 0) $ingredient->stock_qty = 0;
                    $ingredient->save();
                }
            }
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully!');
    }

    /**
     * Sync ingredients (BOM / resep) untuk menu.
     * Menerima array ingredient_ids dan qty_needed dari form.
     */
    private function syncIngredients(Request $request, Menu $menu)
    {
        $ingredientIds = $request->input('ingredient_ids', []);
        $qtyNeeded = $request->input('qty_needed', []);

        $syncData = [];
        foreach ($ingredientIds as $index => $ingredientId) {
            if ($ingredientId && isset($qtyNeeded[$index]) && $qtyNeeded[$index] > 0) {
                $syncData[$ingredientId] = ['qty_needed' => $qtyNeeded[$index]];
            }
        }

        $menu->ingredients()->sync($syncData);
    }
}
