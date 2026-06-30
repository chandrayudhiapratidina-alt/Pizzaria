<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ClientMenuController extends Controller
{
    /**
     * Menampilkan katalog menu untuk pelanggan guest (is_available = true)
     * Jika ada parameter ?table=X → set session meja (Dine-In via QR Code)
     */
    public function index(Request $request)
    {
        $showWelcomeAd = false;
        if ($request->has('table')) {
            $table = \App\Models\Table::find($request->table);
            if ($table) {
                Session::put('table_id', $table->id);
                Session::put('table_number', $table->table_number);
                Session::put('order_mode', 'dine_in');
                
                // Selalu tampilkan iklan untuk testing
                $showWelcomeAd = true;
            }
        }

        $categories = \App\Models\Category::whereHas('menus', function($query) {
            $query->where('is_available', true);
        })->orderBy('sort_order')->get();
        $menus = Menu::with('category')->where('is_available', true);

        if ($request->filled('category')) {
            $menus->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $menus->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $menus->where('base_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $menus->where('base_price', '<=', $request->max_price);
        }

        if ($request->filled('sort')) {
            if ($request->sort == 'price_asc') {
                $menus->orderBy('base_price', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $menus->orderBy('base_price', 'desc');
            } elseif ($request->sort == 'newest') {
                $menus->orderBy('created_at', 'desc');
            }
        } else {
            $menus->orderBy('created_at', 'desc');
        }

        $menus = $menus->get();

        // Cek apakah ada order aktif di meja ini (untuk menampilkan tombol "Tambah Pesanan")
        $activeOrder = null;
        if (Session::has('table_id')) {
            $activeOrder = Order::where('table_id', Session::get('table_id'))
                ->where('order_type', 'dine_in')
                ->whereNotIn('order_status', ['served'])
                ->where('payment_status', 'pending')
                ->latest()
                ->first();
        }

        // Ambil review terbaru yang sudah di-approve untuk testimonials
        $latestReviews = \App\Models\StoreReview::where('is_approved', true)
            ->where('rating', '>=', 4)
            ->with(['user'])
            ->latest()
            ->take(6)
            ->get();
            
        // Ambil promo aktif berdasarkan Pengaturan Banner Promo Utama
        $welcomePromoId = \App\Models\Setting::get('welcome_promo_id');
        $activePromo = null;
        if ($welcomePromoId) {
            $activePromo = \App\Models\Promotion::valid()->find($welcomePromoId);
        }

        // Calculate dynamic stats
        $totalMenuItems = Menu::count();
        $averageRating = \App\Models\ProductReview::where('is_approved', true)->avg('rating');
        $averageRating = $averageRating ? round($averageRating, 1) : 4.9; // Fallback if no reviews
        $yearsOfExcellence = max(10, date('Y') - 2014); // Assuming started around 2014

        return view('client.catalog', compact(
            'menus', 'categories', 'showWelcomeAd', 'activeOrder', 
            'latestReviews', 'activePromo', 'totalMenuItems', 'averageRating', 'yearsOfExcellence'
        ));
    }

    /**
     * Menampilkan detail menu untuk dikustomisasi
     */
    public function show($id)
    {
        $menu = Menu::findOrFail($id);
        if (!$menu->is_available) {
            return redirect()->route('client.guest.catalog')->with('error', 'Menu tidak tersedia.');
        }

        $customizations = \App\Models\Customization::where('menu_id', $id)
            ->orWhere('category_id', $menu->category_id)
            ->orWhere(function($query) {
                $query->whereNull('menu_id')->whereNull('category_id');
            })
            ->get();

        $sizes = $customizations->where('type', 'size');
        $crusts = $customizations->where('type', 'crust');
        $toppings = $customizations->where('type', 'topping');
        $temperatures = $customizations->where('type', 'temperature');

        $images = \App\Models\MenuImage::where('menu_id', $id)->orderBy('is_primary', 'desc')->get();
        $reviews = \App\Models\ProductReview::with('user')->where('menu_id', $id)->where('is_approved', true)->latest()->get();

        return view('client.menu_show', compact('menu', 'sizes', 'crusts', 'toppings', 'temperatures', 'images', 'reviews'));
    }

    /**
     * Menampilkan halaman keranjang dari session
    /**
     * Menambah menu ke dalam keranjang (Session)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'qty' => 'required|integer|min:1',
            'size' => 'nullable|exists:customizations,id',
            'crust' => 'nullable|exists:customizations,id',
            'temperature' => 'nullable|exists:customizations,id',
            'toppings' => 'nullable|array',
            'toppings.*' => 'exists:customizations,id',
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        
        // Verifikasi Ketersediaan & Stok
        if (!$menu->checkAvailability()) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, menu ini sedang tidak tersedia atau stok habis.'
            ], 400);
        }

        $price = $menu->final_price;
        $notes = [];
        $customizationIds = [];

        if ($request->filled('size')) {
            $size = \App\Models\Customization::find($request->size);
            if ($size) {
                $price += $size->additional_price;
                $notes['Ukuran'] = $size->name . ' (+Rp ' . number_format($size->additional_price, 0, ',', '.') . ')';
                $customizationIds[] = $size->id;
            }
        }

        if ($request->filled('crust')) {
            $crust = \App\Models\Customization::find($request->crust);
            if ($crust) {
                $price += $crust->additional_price;
                $notes['Crust'] = $crust->name . ' (+Rp ' . number_format($crust->additional_price, 0, ',', '.') . ')';
                $customizationIds[] = $crust->id;
            }
        }

        if ($request->filled('temperature')) {
            $temp = \App\Models\Customization::find($request->temperature);
            if ($temp) {
                $price += $temp->additional_price;
                $notes['Suhu'] = $temp->name . ' (+Rp ' . number_format($temp->additional_price, 0, ',', '.') . ')';
                $customizationIds[] = $temp->id;
            }
        }

        if ($request->filled('toppings')) {
            $toppingNotes = [];
            foreach ($request->toppings as $toppingId) {
                $topping = \App\Models\Customization::find($toppingId);
                if ($topping) {
                    $price += $topping->additional_price;
                    $toppingNotes[] = $topping->name . ' (+Rp ' . number_format($topping->additional_price, 0, ',', '.') . ')';
                    $customizationIds[] = $topping->id;
                }
            }
            if (count($toppingNotes) > 0) {
                $notes['Topping'] = $toppingNotes;
            }
        }

        $subtotal = $price * $request->qty;

        $item = [
            'cart_id' => uniqid(),
            'menu_id' => $menu->id,
            'menu_name' => $menu->name,
            'image_url' => $menu->image_url,
            'price' => $price,
            'qty' => $request->qty,
            'subtotal' => $subtotal,
            'customization_notes' => $notes,
            'customization_ids' => $customizationIds
        ];

        Session::push('cart', $item);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil ditambahkan ke keranjang!',
                'cart_count' => count(Session::get('cart', []))
            ]);
        }

        if ($request->action == 'checkout') {
            if (Session::has('table_id')) {
                return redirect()->route('client.guest.dinein.checkout')->with('success', 'Berhasil menambahkan ke keranjang!');
            } else {
                return redirect()->route('client.online.checkout')->with('success', 'Berhasil menambahkan ke keranjang!');
            }
        } else {
            return redirect()->route('client.guest.catalog')->with('success', 'Berhasil ditambahkan ke keranjang! Silakan pilih menu lainnya.');
        }
    }

    /**
     * Menghapus item dari keranjang
     */
    public function removeFromCart(Request $request)
    {
        $cart = Session::get('cart', []);
        
        $cart = array_filter($cart, function($item) use ($request) {
            return $item['cart_id'] !== $request->cart_id;
        });
        
        Session::put('cart', array_values($cart));
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus dari keranjang!',
                'cart_count' => count(Session::get('cart', []))
            ]);
        }

        return redirect()->route('client.guest.cart')->with('success', 'Berhasil menghapus dari keranjang!');
    }

    /**
     * Terapkan kode promo/voucher ke keranjang dine-in.
     * 
     * KONSOLIDASI: Menggunakan model Promotion (bukan Voucher).
     * Semua kode diskon sekarang dikelola di satu tabel 'promotions'.
     */
    public function applyVoucher(Request $request)
    {
        $request->validate(['voucher_code' => 'required|string']);

        $promo = \App\Models\Promotion::active()
            ->where('code', strtoupper($request->voucher_code))
            ->first();

        if (!$promo) {
            return back()->with('voucher_error', 'Kode promo tidak ditemukan atau tidak aktif.');
        }

        $user = auth()->user();
        $isValid = $user ? $promo->isValidForUser($user->id) : $promo->isValid();

        if (!$isValid) {
            return back()->with('voucher_error', 'Kode promo kadaluarsa, kuota habis, atau Anda telah melewati batas penggunaan promo ini.');
        }

        $cart = Session::get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['subtotal'];
        }

        if ($total < $promo->min_order_amount) {
            return back()->with('voucher_error', 'Total pesanan belum memenuhi syarat minimal pembelian promo ini (Rp ' . number_format($promo->min_order_amount, 0, ',', '.') . ').');
        }

        $discount = $promo->calculateDiscount($total);

        Session::put('voucher_code', $promo->code);
        Session::put('voucher_discount', $discount);
        Session::put('voucher_promo_id', $promo->id);

        return back()->with('voucher_success', 'Promo berhasil diterapkan! Diskon: Rp ' . number_format($discount, 0, ',', '.'));
    }

    /**
     * Menghapus promo dari keranjang dine-in.
     */
    public function removeVoucher()
    {
        Session::forget(['voucher_code', 'voucher_discount', 'voucher_promo_id']);
        return back()->with('voucher_success', 'Promo berhasil dihapus.');
    }

    /**
     * Menampilkan halaman khusus checkout Dine-In
     */
    public function dineInCheckoutForm(Request $request)
    {
        if (!Session::has('table_id')) {
            return redirect()->route('client.guest.catalog')->with('error', 'Sesi meja tidak ditemukan. Silakan scan ulang QR Code di meja Anda.');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('client.guest.catalog')->with('error', 'Keranjang Anda kosong.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['subtotal'];
        }

        $discountAmt = 0;
        $promoCode = null;
        $promoId = Session::get('voucher_promo_id');
        if ($promoId) {
            $promo = \App\Models\Promotion::find($promoId);
            if ($promo && $promo->isValid() && $total >= $promo->min_order_amount) {
                $discountAmt = $promo->calculateDiscount($total);
                $promoCode = $promo->code;
            } else {
                Session::forget(['voucher_code', 'voucher_promo_id', 'voucher_discount']);
                session()->now('voucher_error', 'Kode promo dilepas karena total pesanan kurang dari minimum belanja.');
            }
        }

        return view('client.dinein_checkout', compact('cart', 'total', 'discountAmt', 'promoCode'));
    }

    /**
     * Memproses pesanan DINE-IN dari keranjang ke database.
     * 
     * Logika Dine-In:
     * - Jika ada order aktif di meja yang sama → gabungkan (Add-on)
     * - Jika tidak ada → buat order baru
     * - Status langsung "cooking" (tanpa menunggu bayar)
     * - Pembayaran dilakukan di akhir via kasir
     */
    public function checkout(CheckoutRequest $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('client.guest.catalog')->with('error', 'Keranjang Anda kosong.');
        }

        // Hitung total_amount secara real-time dari database dan verifikasi stok
        $subtotalAmount = 0;
        foreach ($cart as &$item) {
            $menu = \App\Models\Menu::with('ingredients')->find($item['menu_id']);
            if (!$menu || !$menu->checkAvailability()) {
                return redirect()->route('client.guest.catalog')->with('error', "Maaf, menu {$item['menu_name']} sudah tidak tersedia.");
            }

            if ($menu->daily_stock !== null && $menu->daily_stock < $item['qty']) {
                return redirect()->route('client.guest.catalog')->with('error', "Maaf, stok harian untuk menu {$menu->name} tidak mencukupi.");
            }

            // Real-time price calculation
            $realPrice = $menu->final_price;
            
            // Verifikasi stok bahan baku tambahan (BOM) jika ada
            foreach ($menu->ingredients as $ingredient) {
                $qtyNeeded = $ingredient->pivot->qty_needed * $item['qty'];
                if ($ingredient->stock_qty < $qtyNeeded) {
                    return redirect()->route('client.guest.catalog')->with('error', "Maaf, stok bahan baku untuk menu {$menu->name} habis.");
                }
            }

            if (!empty($item['customization_ids'])) {
                $customizations = \App\Models\Customization::whereIn('id', $item['customization_ids'])->get();
                foreach ($customizations as $cust) {
                    $realPrice += $cust->additional_price;
                    if ($cust->deduct_ingredient_id) {
                        $ingredient = \App\Models\Ingredient::find($cust->deduct_ingredient_id);
                        if ($ingredient && $ingredient->stock_qty < ($cust->deduct_qty * $item['qty'])) {
                            return redirect()->route('client.guest.catalog')->with('error', "Maaf, stok bahan tambahan kustomisasi tidak mencukupi.");
                        }
                    }
                }
            }

            $realSubtotal = $realPrice * $item['qty'];
            $subtotalAmount += $realSubtotal;
            
            // Update cart item with real price to be saved in DB
            $item['price'] = $realPrice;
            $item['subtotal'] = $realSubtotal;
        }

        $discount = 0;
        // Merekam Redemption jika ada promo
        if (Session::has('voucher_promo_id')) {
            $promoId = Session::get('voucher_promo_id');
            $promotion = \App\Models\Promotion::find($promoId);
            
            if ($promotion && $promotion->isValid()) {
                $discount = Session::get('voucher_discount', 0);
                if ($promotion->discount_type === 'percentage') {
                    $discount = ($subtotalAmount * $promotion->discount_value) / 100;
                    if ($promotion->max_discount && $discount > $promotion->max_discount) {
                        $discount = $promotion->max_discount;
                    }
                }
            } else {
                Session::forget(['voucher_code', 'voucher_discount', 'voucher_promo_id']);
            }
        }

        $totalAmount = max(0, $subtotalAmount - $discount);

            DB::beginTransaction();

            try {
                $tableId = $request->table_id ?? Session::get('table_id');

            // ── Cek apakah ada order aktif di meja ini (Add-on Flow) ──
            $existingOrder = null;
            if ($tableId) {
                $existingOrder = Order::where('table_id', $tableId)
                    ->where('order_type', 'dine_in')
                    ->whereNotIn('order_status', ['served', 'completed', 'cancelled'])
                    ->where('payment_status', 'unpaid')
                    ->latest()
                    ->first();
            }

            if ($existingOrder) {
                // ── ADD-ON: Gabungkan ke order yang sudah ada ──
                $order = $existingOrder;

                foreach ($cart as $item) {
                    $order->items()->create([
                        'menu_id' => $item['menu_id'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                        'customization_notes' => isset($item['customization_notes']) ? json_encode($item['customization_notes']) : null,
                        'customization_ids' => $item['customization_ids'] ?? [],
                    ]);
                }

                // Update total amount
                $order->subtotal_amount += $subtotalAmount;
                $order->total_amount += $totalAmount;
                $order->order_status = 'cooking'; // Kembali ke cooking jika ada pesanan baru
                $order->save();

            } else {
                // ── BARU: Buat order dine-in baru ──
                $order = Order::create([
                    'order_type'       => 'dine_in',
                    'table_id'         => $tableId,
                    'customer_name'    => $request->customer_name,
                    'customer_whatsapp'=> $request->customer_whatsapp,
                    'subtotal_amount'  => $subtotalAmount,
                    'total_amount'     => $totalAmount,
                    'payment_method'   => null,       // Belum bayar, ditentukan kasir nanti
                    'order_status'     => 'cooking',  // Langsung masuk dapur
                    'payment_status'   => 'unpaid',   // Belum bayar (post-paid dine-in)
                ]);

                foreach ($cart as $item) {
                    $order->items()->create([
                        'menu_id' => $item['menu_id'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                        'customization_notes' => isset($item['customization_notes']) ? json_encode($item['customization_notes']) : null,
                        'customization_ids' => $item['customization_ids'] ?? [],
                    ]);
                }
            }

            // Kurangi stok harian dan bahan baku
            foreach ($cart as $item) {
                $menu = \App\Models\Menu::with('ingredients')->find($item['menu_id']);
                if ($menu) {
                    if ($menu->daily_stock !== null) {
                        $menu->decrement('daily_stock', $item['qty']);
                    }
                    foreach ($menu->ingredients as $ingredient) {
                        $qtyNeeded = $ingredient->pivot->qty_needed * $item['qty'];
                        $ingredient->decrement('stock_qty', $qtyNeeded);
                    }
                }
                
                // Kurangi stok untuk kustomisasi (bahan tambahan)
                if (!empty($item['customization_ids'])) {
                    $customizations = \App\Models\Customization::whereIn('id', $item['customization_ids'])->get();
                    foreach ($customizations as $cust) {
                        if ($cust->deduct_ingredient_id) {
                            $ing = \App\Models\Ingredient::find($cust->deduct_ingredient_id);
                            if ($ing) {
                                $ing->decrement('stock_qty', $cust->deduct_qty * $item['qty']);
                            }
                        }
                    }
                }
            }

            // Tandai order bahwa stok sudah dideduksi
            $order->stock_deducted = true;
            $order->save();

            DB::commit();

            // Jika ada promo, update count dan buat redemption
            if (Session::has('voucher_promo_id')) {
                $promoId = Session::get('voucher_promo_id');
                \App\Models\Promotion::where('id', $promoId)->increment('used_count');
                
                // Set promotion_id pada order
                $order->promotion_id = $promoId;
                $order->discount_amount += $discount; // add discount to existing if add-on
                $order->save();

                \App\Models\PromotionRedemption::create([
                    'user_id' => null, // Guest dine-in
                    'promotion_id' => $promoId,
                    'order_id' => $order->id,
                    'discount_applied' => $discount,
                    'status' => 'reserved',
                ]);
            }

            // Hapus keranjang dari Session setelah berhasil
            Session::forget('cart');
            Session::forget('voucher_code');
            Session::forget('voucher_discount');
            Session::forget('voucher_promo_id');
            
            // Simpan ID pesanan ke session untuk akses cepat
            Session::put('recent_order_id', $order->id);

            $message = $existingOrder 
                ? 'Pesanan tambahan berhasil dikirim ke dapur!' 
                : 'Pesanan berhasil dibuat dan dikirim ke dapur!';

            return redirect()->route('client.guest.orders.show', $order->id)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan status pesanan berdasarkan ID
     */
    public function showOrder($id)
    {
        Order::autoProgressAllActive();
        $order = Order::with(['items.menu', 'table'])->findOrFail($id);
        return view('client.order_status', compact('order'));
    }

    /**
     * Membatalkan pesanan (hanya bisa jika belum dibayar dan belum diproses)
     */
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Pesanan yang sudah dibayar tidak dapat dibatalkan.');
        }

        // Cek status yang tidak bisa dibatalkan
        $disallowedStatuses = ['completed', 'cancelled'];

        if (in_array($order->order_status, $disallowedStatuses)) {
            return back()->with('error', 'Pesanan sudah diproses dan tidak dapat dibatalkan.');
        }

        $order->order_status = 'cancelled';
        if (in_array($order->payment_status, ['pending', 'unpaid'])) {
            $order->payment_status = 'cancelled';
        }
        $order->save();

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * [DEPRECATED] Client-side payment callback.
     * 
     * Logika pembayaran sekarang ditangani oleh server-to-server webhook:
     * @see \App\Http\Controllers\Api\MidtransWebhookController::handle()
     * 
     * Endpoint ini dipertahankan untuk backward-compatibility namun
     * TIDAK lagi mengubah status pembayaran. Hanya mengembalikan status terkini.
     */
    public function paymentSuccessCallback(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        return response()->json([
            'success' => $order->payment_status === 'paid',
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status,
            'message' => $order->payment_status === 'paid'
                ? 'Pembayaran berhasil diverifikasi.'
                : 'Menunggu konfirmasi pembayaran dari server.',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // SPA AJAX API METHODS
    // ═══════════════════════════════════════════════════════════════

    public function getCartApi()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['subtotal'];
        }

        $discount = 0;
        $promoCode = '';
        $promoId = Session::get('voucher_promo_id');
        if ($promoId) {
            $promo = \App\Models\Promotion::find($promoId);
            if ($promo && $promo->isValid() && $total >= $promo->min_order_amount) {
                $discount = $promo->calculateDiscount($total);
                $promoCode = $promo->code;
            }
        }

        $grandTotal = max(0, $total - $discount);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $total,
            'discount' => $discount,
            'promoCode' => $promoCode,
            'grandTotal' => $grandTotal,
            'count' => count($cart),
            'order_mode' => Session::get('order_mode', '')
        ]);
    }

    public function setModeApi(Request $request)
    {
        $request->validate(['mode' => 'required|in:delivery,pickup,dine_in']);
        Session::put('order_mode', $request->mode);
        return response()->json(['success' => true, 'mode' => $request->mode]);
    }

    public function getQuickViewApi($id)
    {
        $menu = Menu::findOrFail($id);
        if (!$menu->is_available) {
            return response()->json(['error' => 'Menu tidak tersedia.'], 404);
        }

        $customizations = \App\Models\Customization::where('menu_id', $id)
            ->orWhere('category_id', $menu->category_id)
            ->orWhere(function($query) {
                $query->whereNull('menu_id')->whereNull('category_id');
            })
            ->get();

        $sizes = $customizations->where('type', 'size');
        $crusts = $customizations->where('type', 'crust');
        $toppings = $customizations->where('type', 'topping');
        $temperatures = $customizations->where('type', 'temperature');

        // Render view partial and return as JSON
        $html = view('client.partials.quick_view_modal', compact('menu', 'sizes', 'crusts', 'toppings', 'temperatures'))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function orderStatusApi($id)
    {
        Order::autoProgressAllActive();
        $order = Order::findOrFail($id);
        return response()->json([
            'order_status' => $order->order_status,
            'order_status_label' => $order->order_status_label,
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->payment_status_label,
            'is_paid' => $order->payment_status === 'paid',
            'is_completed' => in_array($order->order_status, ['completed', 'cancelled']),
        ]);
    }

    public function downloadReceipt($order_number)
    {
        $order = Order::with(['items.menu', 'table'])->where('order_number', $order_number)->firstOrFail();
        
        if ($order->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Struk hanya bisa diunduh untuk pesanan yang sudah lunas.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('client.receipt', compact('order'));
        return $pdf->download("Struk-{$order->order_number}.pdf");
    }
}
