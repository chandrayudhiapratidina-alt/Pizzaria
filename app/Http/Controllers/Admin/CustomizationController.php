<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customization;
use App\Models\Menu;

class CustomizationController extends Controller
{
    public function index()
    {
        $globalCustomizations = Customization::whereNull('menu_id')->whereNull('category_id')->get();
        $categories = \App\Models\Category::with('customizations')->has('customizations')->get();
        $menus = Menu::with('customizations')->has('customizations')->get();
        
        return view('admin.customizations.index', compact('globalCustomizations', 'categories', 'menus'));
    }

    public function create()
    {
        $menus = Menu::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $ingredients = \App\Models\Ingredient::orderBy('name')->get();
        return view('admin.customizations.create', compact('menus', 'categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'target_type' => 'required|in:global,category,menu',
            'menu_id' => 'required_if:target_type,menu|nullable|exists:menus,id',
            'category_id' => 'required_if:target_type,category|nullable|exists:categories,id',
            'type' => 'required|in:size,crust,topping',
            'name' => 'required|string|max:255',
            'additional_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'deduct_ingredient_id' => 'nullable|exists:ingredients,id',
            'deduct_qty' => 'nullable|numeric|min:0'
        ]);

        if ($data['target_type'] === 'global') {
            $data['menu_id'] = null;
            $data['category_id'] = null;
        } elseif ($data['target_type'] === 'category') {
            $data['menu_id'] = null;
        } elseif ($data['target_type'] === 'menu') {
            $data['category_id'] = null;
        }
        
        unset($data['target_type']);

        $customization = Customization::create($data);

        // Auto adjust inventory
        $stock = (int) $customization->stock;
        $deductQty = (float) $customization->deduct_qty;
        if ($stock > 0 && $deductQty > 0 && $customization->deduct_ingredient_id) {
            $ingredient = \App\Models\Ingredient::find($customization->deduct_ingredient_id);
            if ($ingredient) {
                $req = $stock * $deductQty;
                $ingredient->stock_qty += $req;
                $ingredient->save();
            }
        }
        return redirect()->route('admin.customizations.index')->with('success', 'Kustomisasi berhasil ditambahkan!');
    }

    public function edit(Customization $customization)
    {
        $menus = Menu::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $ingredients = \App\Models\Ingredient::orderBy('name')->get();
        
        $target_type = 'global';
        if ($customization->category_id) $target_type = 'category';
        if ($customization->menu_id) $target_type = 'menu';
        
        return view('admin.customizations.edit', compact('customization', 'menus', 'categories', 'ingredients', 'target_type'));
    }

    public function update(Request $request, Customization $customization)
    {
        $data = $request->validate([
            'target_type' => 'required|in:global,category,menu',
            'menu_id' => 'required_if:target_type,menu|nullable|exists:menus,id',
            'category_id' => 'required_if:target_type,category|nullable|exists:categories,id',
            'type' => 'required|in:size,crust,topping',
            'name' => 'required|string|max:255',
            'additional_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'deduct_ingredient_id' => 'nullable|exists:ingredients,id',
            'deduct_qty' => 'nullable|numeric|min:0'
        ]);

        if ($data['target_type'] === 'global') {
            $data['menu_id'] = null;
            $data['category_id'] = null;
        } elseif ($data['target_type'] === 'category') {
            $data['menu_id'] = null;
        } elseif ($data['target_type'] === 'menu') {
            $data['category_id'] = null;
        }
        
        unset($data['target_type']);

        // Capture old requirements
        $oldIngId = $customization->deduct_ingredient_id;
        $oldReq = ((int) $customization->stock) * ((float) $customization->deduct_qty);

        $customization->update($data);

        // Capture new requirements
        $newIngId = $customization->deduct_ingredient_id;
        $newReq = ((int) $customization->stock) * ((float) $customization->deduct_qty);

        // Auto adjust inventory
        if ($oldIngId == $newIngId) {
            // Same ingredient, just adjust difference
            $diff = $newReq - $oldReq;
            if ($diff != 0 && $newIngId) {
                $ingredient = \App\Models\Ingredient::find($newIngId);
                if ($ingredient) {
                    $ingredient->stock_qty += $diff;
                    if ($ingredient->stock_qty < 0) $ingredient->stock_qty = 0;
                    $ingredient->save();
                }
            }
        } else {
            // Changed ingredient
            if ($oldIngId && $oldReq > 0) {
                $oldIngredient = \App\Models\Ingredient::find($oldIngId);
                if ($oldIngredient) {
                    $oldIngredient->stock_qty -= $oldReq;
                    if ($oldIngredient->stock_qty < 0) $oldIngredient->stock_qty = 0;
                    $oldIngredient->save();
                }
            }
            if ($newIngId && $newReq > 0) {
                $newIngredient = \App\Models\Ingredient::find($newIngId);
                if ($newIngredient) {
                    $newIngredient->stock_qty += $newReq;
                    $newIngredient->save();
                }
            }
        }
        return redirect()->route('admin.customizations.index')->with('success', 'Kustomisasi berhasil diperbarui!');
    }

    public function destroy(Customization $customization)
    {
        $customization->delete();
        return redirect()->route('admin.customizations.index')->with('success', 'Kustomisasi berhasil dihapus!');
    }
}
