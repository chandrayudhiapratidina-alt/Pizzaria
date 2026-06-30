<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class StockDeductionService
{
    /**
     * Deduct stock for an entire order.
     * Handles:
     * 1. Daily menu stock (daily_stock)
     * 2. Raw ingredients (BOM - menu_ingredients)
     * 3. Customization items
     * 
     * @param Order $order
     * @return bool
     * @throws \Exception
     */
    public function deductOrderStock(Order $order): bool
    {
        // Guard: prevent double deduction
        if ($order->stock_deducted) {
            return true;
        }

        DB::beginTransaction();

        try {
            foreach ($order->items as $item) {
                // 1. Kurangi stok harian menu jika diset
                if ($item->menu && $item->menu->daily_stock !== null) {
                    if ($item->menu->daily_stock < $item->qty) {
                        throw new \Exception("Stok harian untuk menu {$item->menu->name} tidak mencukupi.");
                    }
                    $item->menu->decrement('daily_stock', $item->qty);
                }

                // 2. Kurangi bahan baku (BOM) berdasarkan resep menu
                if ($item->menu && $item->menu->ingredients->count() > 0) {
                    foreach ($item->menu->ingredients as $ingredient) {
                        $qtyToDeduct = $ingredient->pivot->qty_needed * $item->qty;
                        if ($ingredient->stock_qty < $qtyToDeduct) {
                            throw new \Exception("Stok bahan baku {$ingredient->name} tidak mencukupi untuk menu {$item->menu->name}.");
                        }
                        $ingredient->decrement('stock_qty', $qtyToDeduct);
                    }
                }

                // 3. Kurangi stok bahan baku dari kustomisasi (extra topping dsb)
                $customizationIds = is_string($item->customization_ids) 
                                        ? json_decode($item->customization_ids, true) 
                                        : $item->customization_ids;

                if (is_array($customizationIds) && !empty($customizationIds)) {
                    $customizations = \App\Models\Customization::whereIn('id', $customizationIds)->get();

                    foreach ($customizations as $cust) {
                        if ($cust->deduct_ingredient_id) {
                            $ingredient = \App\Models\Ingredient::find($cust->deduct_ingredient_id);
                            if ($ingredient && $cust->deduct_qty > 0) {
                                $qtyToDeduct = $cust->deduct_qty * $item->qty;
                                if ($ingredient->stock_qty < $qtyToDeduct) {
                                    throw new \Exception("Stok bahan baku {$ingredient->name} (kustomisasi) tidak mencukupi.");
                                }
                                $ingredient->decrement('stock_qty', $qtyToDeduct);
                            }
                        }
                        
                        // Also decrement customization direct stock
                        if ($cust->stock !== null && $cust->stock > 0) {
                            $cust->decrement('stock', $item->qty);
                        }
                    }
                }
            }

            // Mark as deducted to prevent future double-deductions
            $order->update(['stock_deducted' => true]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore stock for an entire order if it is cancelled or expired.
     * 
     * @param Order $order
     * @return bool
     * @throws \Exception
     */
    public function restoreOrderStock(Order $order): bool
    {
        // Guard: only restore if it was previously deducted
        if (!$order->stock_deducted) {
            return true;
        }

        DB::beginTransaction();

        try {
            foreach ($order->items as $item) {
                // 1. Kembalikan stok harian menu
                if ($item->menu && $item->menu->daily_stock !== null) {
                    $item->menu->increment('daily_stock', $item->qty);
                }

                // 2. Kembalikan bahan baku (BOM)
                if ($item->menu && $item->menu->ingredients->count() > 0) {
                    foreach ($item->menu->ingredients as $ingredient) {
                        $qtyToRestore = $ingredient->pivot->qty_needed * $item->qty;
                        $ingredient->increment('stock_qty', $qtyToRestore);
                    }
                }

                // 3. Kembalikan stok kustomisasi
                $customizationIds = is_string($item->customization_ids) 
                                        ? json_decode($item->customization_ids, true) 
                                        : $item->customization_ids;

                if (is_array($customizationIds) && !empty($customizationIds)) {
                    $customizations = \App\Models\Customization::whereIn('id', $customizationIds)->get();

                    foreach ($customizations as $cust) {
                        if ($cust->deduct_ingredient_id) {
                            $ingredient = \App\Models\Ingredient::find($cust->deduct_ingredient_id);
                            if ($ingredient && $cust->deduct_qty > 0) {
                                $qtyToRestore = $cust->deduct_qty * $item->qty;
                                $ingredient->increment('stock_qty', $qtyToRestore);
                            }
                        }
                        
                        if ($cust->stock !== null) {
                            $cust->increment('stock', $item->qty);
                        }
                    }
                }
            }

            // Mark as not deducted
            $order->update(['stock_deducted' => false]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
