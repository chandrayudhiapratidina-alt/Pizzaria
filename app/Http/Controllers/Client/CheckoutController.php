<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function process()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('client.cart')->with('error', 'Keranjang belanja kosong.');
        }

        $totalAmount = array_sum(array_column($cart, 'subtotal'));

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'table_id' => null,
                'total_amount' => $totalAmount,
                'status' => 'new',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['qty'],
                    'price' => $item['subtotal'] / $item['qty'],
                    'customization_notes' => $item['customization_notes'],
                ]);

                $recipe = DB::table('menu_ingredients')->where('menu_id', $item['menu_id'])->get();
                foreach ($recipe as $ing) {
                    $totalDeduction = $ing->required_qty * $item['qty'];
                    $ingredientModel = Ingredient::find($ing->ingredient_id);
                    if ($ingredientModel) {
                        $ingredientModel->stock_qty -= $totalDeduction;
                        $ingredientModel->save();
                    }
                }
            }

            session()->forget('cart');
            DB::commit();

            return redirect()->route('client.catalog')->with('success', 'Pesanan berhasil dibuat! Silakan bayar di kasir.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('client.cart')->with('error', 'Terjadi kesalahan saat memproses checkout.');
        }
    }
}
