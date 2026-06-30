<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ingredient;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredientsByCategory = Ingredient::all()->groupBy('category');
        return view('admin.ingredients.index', compact('ingredientsByCategory'));
    }

    public function create()
    {
        return view('admin.ingredients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'category' => 'required|in:menu_base,topping,beverage,other',
            'stock_qty' => 'required|numeric',
            'unit' => 'required|in:g,ml,pcs',
            'minimum_stock_alert' => 'required|numeric'
        ]);

        Ingredient::create($data);
        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient added successfully!');
    }

    public function show(string $id) { }

    public function edit(Ingredient $ingredient)
    {
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'name' => 'required',
            'category' => 'required|in:menu_base,topping,beverage,other',
            'stock_qty' => 'required|numeric',
            'unit' => 'required|in:g,ml,pcs',
            'minimum_stock_alert' => 'required|numeric'
        ]);

        $ingredient->update($data);
        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient updated successfully!');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient deleted successfully!');
    }
}
