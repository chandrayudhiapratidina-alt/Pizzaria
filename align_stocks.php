<?php

use App\Models\Menu;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

$ingredients = Ingredient::all();
$menus = Menu::with('ingredients')->get();

$ingredientTotals = [];

// 1. Calculate totals required by menus
foreach ($menus as $menu) {
    if ($menu->daily_stock > 0) {
        foreach ($menu->ingredients as $ingredient) {
            $qty_needed = $ingredient->pivot->qty_needed;
            $total_needed = $qty_needed * $menu->daily_stock;
            
            if (!isset($ingredientTotals[$ingredient->id])) {
                $ingredientTotals[$ingredient->id] = 0;
            }
            $ingredientTotals[$ingredient->id] += $total_needed;
        }
    }
}

// 2. Add topping buffers (Customizations)
$customizations = DB::table('customizations')->whereNotNull('deduct_ingredient_id')->get();
foreach ($customizations as $c) {
    // Assume an average of 100 orders per day for each topping
    $toppingBuffer = $c->deduct_qty * 100; 
    
    if (!isset($ingredientTotals[$c->deduct_ingredient_id])) {
        $ingredientTotals[$c->deduct_ingredient_id] = 0;
    }
    $ingredientTotals[$c->deduct_ingredient_id] += $toppingBuffer;
}

// 3. Update database
foreach ($ingredientTotals as $id => $total) {
    // Add 20% safety margin for daily ops
    $finalStock = ceil($total * 1.2); 
    
    $ing = Ingredient::find($id);
    if ($ing) {
        // We only increase it if it's currently lower than the calculated requirement
        if ($ing->stock_qty < $finalStock) {
            $ing->stock_qty = $finalStock;
            $ing->minimum_stock_alert = ceil($finalStock * 0.15); // 15% alert
            $ing->save();
            echo "Updated {$ing->name} to {$finalStock}\n";
        } else {
            echo "Kept {$ing->name} at {$ing->stock_qty} (required was {$finalStock})\n";
        }
    }
}
echo "Stock alignment completed.\n";
