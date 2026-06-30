<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Tampilkan antarmuka POS Terminal
     */
    public function index()
    {
        $categories = Category::orderBy('sort_order')->get();
        // Load menu with category to filter via JS easily
        $menus = Menu::with('category')->where('is_available', true)->get();
        $tables = Table::all();

        return view('cashier.pos.index', compact('categories', 'menus', 'tables'));
    }

    /**
     * Proses checkout pesanan dari POS Terminal
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:dine_in,takeaway',
            'customer_name' => 'required|string|max:255',
            'table_id' => 'nullable|exists:tables,id',
            'payment_method' => 'required|in:cash,qris',
            'cash_tendered' => 'nullable|numeric|min:0',
            'cart' => 'required|string', // JSON array of items
        ]);

        $cart = json_decode($request->cart, true);
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong!'], 400);
        }

        try {
            DB::beginTransaction();

            $orderTotal = 0;
            $orderItemsData = [];

            // 1. Calculate totals and prepare items
            foreach ($cart as $item) {
                $menu = Menu::findOrFail($item['id']);
                
                // Validate stock
                if ($menu->daily_stock < $item['qty']) {
                    throw new \Exception("Stok {$menu->name} tidak cukup. Sisa: {$menu->daily_stock}");
                }

                $subtotal = $menu->final_price * $item['qty'];
                $orderTotal += $subtotal;

                $orderItemsData[] = [
                    'menu_id' => $menu->id,
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                
                // Kurangi stok karena pesanan langsung diproses kasir (offline/direct)
                $menu->decrement('daily_stock', $item['qty']);
            }

            // 2. Validate Payment
            $cashTendered = $request->cash_tendered;
            $cashChange = null;
            $paymentRef = null;

            if ($request->payment_method === 'cash') {
                if ($cashTendered < $orderTotal) {
                    throw new \Exception("Uang tunai kurang! Tagihan: Rp " . number_format($orderTotal, 0, ',', '.'));
                }
                $cashChange = $cashTendered - $orderTotal;
            } else {
                // Mock QRIS reference for offline direct payment
                $paymentRef = 'QR/' . strtoupper(uniqid());
            }

            // 3. Create Order
            $order = Order::create([
                'order_type' => $request->order_type,
                'customer_name' => $request->customer_name,
                'table_id' => $request->order_type === 'dine_in' ? $request->table_id : null,
                'subtotal_amount' => $orderTotal,
                'total_amount' => $orderTotal, // Cashier terminal doesn't handle promo UI yet for simplicity, can be added later
                'delivery_fee' => 0,
                'discount_amount' => 0,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'order_status' => 'new', // will be picked up by Kitchen Display
                'cashier_id' => auth()->id(),
                'paid_at' => Carbon::now(),
                'cash_tendered' => $cashTendered,
                'cash_change' => $cashChange,
                'payment_reference' => $paymentRef,
            ]);

            // 4. Attach Items
            foreach ($orderItemsData as &$itemData) {
                $itemData['order_id'] = $order->id;
            }
            OrderItem::insert($orderItemsData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat dan lunas!',
                'redirect_url' => route('cashier.orders.receipt', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
