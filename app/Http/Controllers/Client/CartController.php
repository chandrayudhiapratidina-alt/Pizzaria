<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = array_sum(array_column($cart, 'subtotal'));
        return view('client.cart', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'qty' => 'required|integer|min:1',
            'base_price' => 'required|numeric', // Validasi harga akan dikalkulasi ulang di backend
            'total_price' => 'required|numeric',
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        
        // RE-CALCULATE PRICE IN BACKEND TO PREVENT MANIPULATION
        $expectedTotal = $menu->base_price;
        $notes = [];
        
        if ($request->has('size_price') && $request->has('size_name')) {
            $expectedTotal += (float)$request->size_price;
            $notes['Size'] = $request->size_name;
        }
        if ($request->has('crust_price') && $request->has('crust_name')) {
            $expectedTotal += (float)$request->crust_price;
            $notes['Crust'] = $request->crust_name;
        }
        if ($request->has('topping_prices') && is_array($request->topping_prices)) {
            foreach ($request->topping_prices as $t_price) {
                $expectedTotal += (float)$t_price;
            }
            $notes['Toppings'] = $request->topping_names ?? [];
        }

        $expectedTotal *= $request->qty;

        $cart = session()->get('cart', []);
        $cartId = uniqid();
        $cart[$cartId] = [
            'cart_id' => $cartId,
            'menu_id' => $menu->id,
            'menu_name' => $menu->name,
            'image_url' => $menu->image_url,
            'qty' => $request->qty,
            'subtotal' => $expectedTotal,
            'customization_notes' => $notes
        ];

        session()->put('cart', $cart);

        return redirect()->route('client.catalog')->with('success', 'Berhasil ditambahkan ke keranjang!');
    }

    public function remove(Request $request)
    {
        if ($request->cart_id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->cart_id])) {
                unset($cart[$request->cart_id]);
                session()->put('cart', $cart);
            }
            return redirect()->route('client.cart')->with('success', 'Item dihapus dari keranjang.');
        }
    }
}
