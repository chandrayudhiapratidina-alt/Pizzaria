<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Http\Requests\OnlineCheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class OnlineOrderController extends Controller
{
    // Koordinat default (jika belum diset di admin)
    const DEFAULT_STORE_LAT = -7.4116;
    const DEFAULT_STORE_LNG = 109.2638;
    /**
     * Landing page — pelanggan memilih Delivery atau Pickup.
     */
    public function index()
    {
        $latestReviews = \App\Models\StoreReview::where('is_approved', true)
            ->with(['user'])
            ->latest()
            ->take(6)
            ->get();

        // Welcome Promo (FOMO Logic)
        $showWelcomeBanner = false;
        $welcomePromoTitle = '';
        $welcomePromoSubtitle = '';
        $welcomePromoCode = '';

        if (\App\Models\Setting::get('welcome_promo_active', '0') === '1' && !session()->has('has_seen_welcome')) {
            $promoId = \App\Models\Setting::get('welcome_promo_id');
            if ($promoId) {
                $promo = \App\Models\Promotion::find($promoId);
                if ($promo && $promo->is_active) {
                    // Cek jika user sudah login, apakah punya riwayat pesanan
                    $canSee = true;
                    if (auth()->check()) {
                        $pastOrders = \App\Models\Order::where('user_id', auth()->id())
                            ->whereIn('order_status', ['completed', 'served', 'ready', 'cooking', 'new'])
                            ->count();
                        if ($pastOrders > 0) $canSee = false;
                    }

                    if ($canSee) {
                        $showWelcomeBanner = true;
                        $welcomePromoTitle = \App\Models\Setting::get('welcome_promo_title', 'Pesan Sekarang & Dapatkan Diskon 20% untuk Pesanan Pertama Anda');
                        $welcomePromoSubtitle = \App\Models\Setting::get('welcome_promo_subtitle', 'Nikmati pizza fine dining dari kenyamanan rumah Anda.');
                        $welcomePromoCode = $promo->code;
                        
                        // SET SESSION: Hanya muncul sekali!
                        session()->put('has_seen_welcome', true);
                    }
                }
            }
        }

        return view('client.online_landing', compact('latestReviews', 'showWelcomeBanner', 'welcomePromoTitle', 'welcomePromoSubtitle', 'welcomePromoCode'));
    }

    /**
     * Set mode order (delivery/pickup) lalu redirect ke katalog.
     */
    public function setMode($type)
    {
        if (!in_array($type, ['delivery', 'pickup'])) {
            return redirect()->route('client.online.landing')->with('error', 'Tipe pesanan tidak valid.');
        }

        // Hapus session meja jika ada (supaya tidak tercampur dine-in)
        Session::forget('table_id');
        Session::forget('table_number');
        Session::put('order_mode', $type);

        return redirect()->route('client.guest.catalog')->with('success', 
            $type === 'delivery' 
                ? 'Mode Delivery aktif. Silakan pilih menu!' 
                : 'Mode Pickup aktif. Silakan pilih menu!'
        );
    }

    /**
     * Menampilkan halaman checkout untuk online order (Delivery / Pickup).
     * Pembayaran wajib di muka — tidak ada COD.
     */
    public function checkout()
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('client.guest.catalog')->with('error', 'Keranjang Anda kosong.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['subtotal'];
        }

        $orderMode = Session::get('order_mode', 'pickup');

        // Markup fee akan ditambahkan secara dinamis setelah pelanggan memilih titik tujuan
        // Di halaman depan kita beri base fee estimasi 0 dulu
        $deliveryFee = 0;

        $user = \Illuminate\Support\Facades\Auth::user();
        $addresses = [];
        if ($user && $user->role === 'client') {
            $addresses = \App\Models\CustomerAddress::where('user_id', $user->id)->get();
        }

        $storeLat = \App\Models\Setting::get('store_latitude', self::DEFAULT_STORE_LAT);
        $storeLng = \App\Models\Setting::get('store_longitude', self::DEFAULT_STORE_LNG);

        // Hitung diskon secara dinamis
        $discountAmt = 0;
        $promoCode = null;
        $promoId = Session::get('voucher_promo_id');
        if ($promoId) {
            $promo = \App\Models\Promotion::find($promoId);
            if ($promo && $promo->isValid() && $total >= $promo->min_order_amount) {
                $discountAmt = $promo->calculateDiscount($total);
                $promoCode = $promo->code;
            } else {
                // Promo is no longer valid or minimum order amount is not met
                Session::forget(['voucher_code', 'voucher_promo_id']);
                session()->now('error', 'Kode promo dilepas karena total pesanan kurang dari minimum belanja.');
            }
        }

        return view('client.online_checkout', compact(
            'cart', 'total', 'orderMode', 'deliveryFee', 
            'user', 'addresses', 'storeLat', 'storeLng', 'discountAmt', 'promoCode'
        ));
    }

    /**
     * Proses checkout online order.
     * 
     * FLOW ANTI-SCAM:
     * 1. Buat order dengan status pending_payment
     * 2. Set expires_at = 15 menit dari sekarang
     * 3. Generate Midtrans Snap Token
     * 4. Redirect ke halaman status (dengan countdown timer)
     * 5. Dapur TIDAK menerima pesanan sampai webhook settlement diterima
     */
    public function processCheckout(OnlineCheckoutRequest $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('client.guest.catalog')->with('error', 'Keranjang Anda kosong.');
        }

        $orderMode = $request->input('order_type', Session::get('order_mode', 'pickup'));

        // Hitung total_amount secara real-time dari database dan verifikasi stok
        $totalAmount = 0;
        foreach ($cart as &$item) {
            $menu = \App\Models\Menu::with('ingredients')->find($item['menu_id']);
            if (!$menu || !$menu->checkAvailability()) {
                return redirect()->route('client.guest.catalog')->with('error', "Maaf, menu {$item['menu_name']} sudah tidak tersedia.");
            }
            
            if ($menu->daily_stock !== null && $menu->daily_stock < $item['qty']) {
                return redirect()->route('client.guest.catalog')->with('error', "Maaf, stok harian untuk menu {$menu->name} tidak mencukupi.");
            }

            // Real-time price calculation
            $realPrice = $menu->base_price;
            
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
            $totalAmount += $realSubtotal;
            
            // Update cart item with real price to be saved in DB
            $item['price'] = $realPrice;
            $item['subtotal'] = $realSubtotal;
        }

        DB::beginTransaction();

        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            $deliveryFee = 0;
            $customerAddress = $request->customer_address;
            $customerLat = null;
            $customerLng = null;
            $distanceKm = null;

            if ($orderMode === 'delivery') {
                if ($request->address_id == 'new') {
                    $customerAddress = $request->new_address;
                    $customerLat = $request->latitude;
                    $customerLng = $request->longitude;

                    // Handle new address for logged in user
                    if ($user && $user->role === 'client') {
                        $newAddr = \App\Models\CustomerAddress::create([
                            'user_id' => $user->id,
                            'label' => 'Alamat ' . date('Y-m-d H:i'),
                            'recipient_name' => $request->customer_name,
                            'phone_number' => $request->customer_whatsapp,
                            'full_address' => $customerAddress,
                            'latitude' => $customerLat,
                            'longitude' => $customerLng,
                            'is_primary' => false,
                        ]);
                    }
                } else if ($request->address_id) {
                    $addr = \App\Models\CustomerAddress::find($request->address_id);
                    if ($addr) {
                        $customerAddress = $addr->full_address;
                        $customerLat = $addr->latitude;
                        $customerLng = $addr->longitude;
                    }
                }

                if ($customerLat && $customerLng) {
                    $storeLat = \App\Models\Setting::get('store_latitude', self::DEFAULT_STORE_LAT);
                    $storeLng = \App\Models\Setting::get('store_longitude', self::DEFAULT_STORE_LNG);

                    // Coba ambil tarif dari Biteship API
                    $biteshipService = new \App\Services\BiteshipService();
                    $rateResponse = $biteshipService->getRates($storeLat, $storeLng, $customerLat, $customerLng);

                    if ($rateResponse['success']) {
                        $deliveryFee = $rateResponse['fee'];
                    } else {
                        // Fallback: hitung manual berdasarkan jarak
                        $distanceKm = $this->haversineDistance($storeLat, $storeLng, $customerLat, $customerLng);
                        $deliveryFee = $this->calculateDeliveryFee($distanceKm);
                    }
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Koordinat lokasi tidak ditemukan. Silakan tandai lokasi Anda di peta.');
                }
            }

            // ═══════════════════════════════════════════════════════
            // ANTI-SCAM: Status = pending_payment, bukan cooking
            // Dapur TIDAK menerima pesanan ini sampai webhook diterima
            // ═══════════════════════════════════════════════════════
            $grandTotal = $totalAmount + $deliveryFee;

            $order = Order::create([
                'order_type'       => $orderMode, // delivery atau pickup
                'table_id'         => null, // Online tidak pakai meja
                'customer_name'    => $request->customer_name,
                'customer_whatsapp'=> $request->customer_whatsapp,
                'customer_email'   => $request->customer_email,
                'customer_address' => $customerAddress,
                'latitude'         => $customerLat,
                'longitude'        => $customerLng,
                'delivery_distance_km' => $distanceKm,
                'subtotal_amount'  => $totalAmount,
                'total_amount'     => $grandTotal,
                'delivery_fee'     => $deliveryFee,
                'payment_method'   => $request->payment_method,
                'order_status'     => 'pending_payment',  // ← MENUNGGU BAYAR
                'payment_status'   => 'pending',           // ← BELUM LUNAS
                'expires_at'       => now()->addMinutes((int) \App\Models\Setting::get('auto_payment_expiry_minutes', 15)), // ← DYNAMIC COUNTDOWN
                'user_id'          => $user ? $user->id : null,
            ]);

            // Buat OrderItem
            foreach ($cart as $item) {
                $order->items()->create([
                    'menu_id' => $item['menu_id'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                    'customization_notes' => isset($item['customization_notes']) ? json_encode($item['customization_notes']) : null,
                    'customization_ids' => $item['customization_ids'] ?? [],
                ]);
            }

            // Potong stok (Reserve on Checkout) menggunakan Service agar konsisten dan perhitungan presisi (termasuk BOM bahan baku)
            $stockService = new \App\Services\StockDeductionService();
            $stockService->deductOrderStock($order);

            // Merekam Redemption jika ada promo
            $promoId = Session::get('voucher_promo_id');
            if ($promoId) {
                $promotion = \App\Models\Promotion::find($promoId);
                
                // Verifikasi validitas promo saat checkout
                if ($promotion && $promotion->isValid() && $totalAmount >= $promotion->min_order_amount) {
                    $discountAmt = $promotion->calculateDiscount($totalAmount);

                    $order->promotion_id = $promoId;
                    $order->discount_amount = $discountAmt;
                    $order->total_amount -= $discountAmt;
                    $order->save();

                    \App\Models\PromotionRedemption::create([
                        'user_id' => $user->id,
                        'promotion_id' => $promoId,
                        'order_id' => $order->id,
                        'discount_applied' => $discountAmt,
                        'status' => 'reserved',
                    ]);
                } else {
                    // Promo sudah expired / tidak valid
                    Session::forget(['voucher_code', 'voucher_discount', 'voucher_promo_id']);
                }
            }

            // Generate Midtrans Snap Token untuk pembayaran online
            $snapToken = null;
            try {
                // Gunakan kunci dari Settings database (Admin Panel) jika ada, fallback ke config/.env
                $serverKey = \App\Models\Setting::get('midtrans_server_key') ?: config('midtrans.server_key');
                $isProduction = \App\Models\Setting::get('midtrans_environment', 'sandbox') === 'production';

                \Midtrans\Config::$serverKey = $serverKey;
                \Midtrans\Config::$isProduction = $isProduction;
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                // $enabledPayments restriction removed, Midtrans will show all available options

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->order_number,  // Gunakan order_number, bukan ORD-{id}
                        'gross_amount' => (int) $grandTotal,
                    ],
                    'customer_details' => [
                        'first_name' => $request->customer_name,
                        'phone' => $request->customer_whatsapp,
                        'email' => $request->customer_email ?? null,
                    ],

                    'expiry' => [
                        'start_time' => date('Y-m-d H:i:s O'),
                        'unit' => 'minutes',
                        'duration' => (int) \App\Models\Setting::get('auto_payment_expiry_minutes', 15),
                    ],
                ];

                if ($orderMode === 'delivery' && $customerAddress) {
                    $params['customer_details']['shipping_address'] = [
                        'address' => $customerAddress,
                    ];
                }

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $order->snap_token = $snapToken;
                $order->save();

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Midtrans Error (Online Order): ' . $e->getMessage());
                // Lanjut tanpa token — pelanggan bisa coba lagi dari halaman status
            }

            DB::commit();

            // Hapus keranjang
            Session::forget('cart');
            Session::forget('voucher_code');
            Session::forget('voucher_discount');
            Session::forget('voucher_promo_id');
            Session::put('recent_order_id', $order->id);
            $expiryMinutes = (int) \App\Models\Setting::get('auto_payment_expiry_minutes', 15);

            return redirect()->route('client.online.orders.show', $order->id)->with('success', "Pesanan berhasil dibuat! Silakan selesaikan pembayaran dalam {$expiryMinutes} menit.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan status pesanan online
     */
    public function showOrder($id)
    {
        Order::autoProgressAllActive();
        $order = Order::with(['items.menu'])->findOrFail($id);
        return view('client.online_order_status', compact('order'));
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
        $disallowedStatuses = ['cooking', 'ready', 'served', 'on_delivery', 'completed', 'cancelled'];
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
     * TIDAK lagi mengubah status pembayaran. Hanya mengembalikan
     * status terkini dari order.
     */
    public function paymentCallback(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Jika mode development/lokal (Tugas Kuliah), karena Webhook dari Midtrans tidak bisa masuk ke localhost,
        // kita percaya saja pada callback client-side (onSuccess) untuk mempermudah demo.
        if (config('app.env') === 'local' && $order->payment_status !== 'paid') {
            $order->payment_status = 'paid';
            $order->order_status = 'pending';
            $order->save();
        }

        return response()->json([
            'success' => $order->payment_status === 'paid',
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status,
            'message' => $order->payment_status === 'paid' 
                ? 'Pembayaran berhasil diverifikasi.' 
                : 'Menunggu konfirmasi pembayaran dari server. Halaman akan otomatis diperbarui.',
        ]);
    }

    /**
     * BYPASS PEMBAYARAN (Hanya untuk keperluan Tugas Kuliah/Dev)
     */
    public function simulatePayment(Request $request, $id)
    {
        if (config('app.env') !== 'local') {
            return back()->with('error', 'Fitur simulasi hanya aktif di mode development.');
        }

        $order = Order::findOrFail($id);
        
        if ($order->payment_status !== 'paid') {
            $order->payment_status = 'paid';
            $order->order_status = 'pending'; // Siap diproses dapur
            $order->save();
        }

        return back()->with('success', 'Pembayaran berhasil disimulasikan! (Bypass Midtrans)');
    }

    public function setOrderType(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:delivery,pickup'
        ]);

        session(['order_type' => $request->order_type]);

        return redirect()->route('client.guest.catalog');
    }

    /**
     * Klaim welcome promo dan redirect ke katalog.
     */
    public function claimWelcomePromo(Request $request)
    {
        $code = $request->input('code');
        if ($code) {
            $promo = \App\Models\Promotion::where('code', $code)->first();
            if ($promo && $promo->is_active) {
                session([
                    'voucher_code' => $promo->code,
                    'voucher_promo_id' => $promo->id
                ]);
            }
        }
        
        // Asumsikan default ke delivery untuk mempermudah flow
        if (!session()->has('order_type')) {
            session(['order_type' => 'delivery']);
        }
        
        return redirect()->route('client.guest.catalog')->with('success', 'Kode Promo ' . $code . ' berhasil diklaim! Silakan pilih menu Anda, diskon akan dihitung otomatis di keranjang.');
    }

    /**
     * Terapkan kode promo saat checkout
     */
    public function applyPromo(Request $request)
    {
        $request->validate(['promo_code' => 'required|string']);
        $promo = \App\Models\Promotion::where('code', $request->promo_code)->where('is_active', true)->first();
        
        if ($promo && $promo->isValid()) {
            // Check minimum order amount
            $cart = session()->get('cart', []);
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['subtotal'];
            }

            if ($subtotal < $promo->min_order_amount) {
                return back()->with('error', 'Minimal belanja untuk promo ini adalah Rp ' . number_format($promo->min_order_amount, 0, ',', '.'));
            }

            session([
                'voucher_code' => $promo->code,
                'voucher_promo_id' => $promo->id
            ]);
            return back()->with('success', 'Kode Promo ' . $promo->code . ' berhasil diterapkan!');
        }
        
        return back()->with('error', 'Kode promo tidak ditemukan atau sudah kadaluarsa.');
    }

    /**
     * Hapus kode promo dari checkout
     */
    public function removePromo()
    {
        session()->forget(['voucher_code', 'voucher_promo_id']);
        return back()->with('success', 'Promo berhasil dihapus.');
    }

    public function calculateDeliveryFeeApi(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $storeLat = \App\Models\Setting::get('store_latitude', self::DEFAULT_STORE_LAT);
        $storeLng = \App\Models\Setting::get('store_longitude', self::DEFAULT_STORE_LNG);

        // Coba Biteship API dulu
        $biteshipService = new \App\Services\BiteshipService();
        $rateResponse = $biteshipService->getRates($storeLat, $storeLng, $request->lat, $request->lng);

        if ($rateResponse['success']) {
            $markupFee = \App\Models\Setting::get('delivery_markup_fee', 0);
            $deliveryFee = $rateResponse['fee'] + $markupFee;
            return response()->json([
                'success' => true,
                'fee' => $deliveryFee,
                'provider' => strtoupper($rateResponse['courier_company']) . ' ' . ucfirst($rateResponse['courier_type']),
                'message' => $rateResponse['message'] ?? 'Berhasil dihitung.'
            ]);
        }

        // Fallback: hitung manual
        $distanceKm = $this->haversineDistance($storeLat, $storeLng, $request->lat, $request->lng);
        $deliveryFee = $this->calculateDeliveryFee($distanceKm);

        return response()->json([
            'success' => true,
            'fee' => $deliveryFee,
            'distance_km' => round($distanceKm, 2),
            'message' => 'Tarif dihitung manual (fallback).'
        ]);
    }

    /**
     * Hitung jarak menggunakan Haversine formula.
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Hitung ongkos kirim berdasarkan jarak.
     * Menggunakan konfigurasi admin: base_fee, base_distance, fee_per_km, max_distance.
     */
    private function calculateDeliveryFee(float $distanceKm): int
    {
        $baseFee = (int) \App\Models\Setting::get('delivery_base_fee', 5000);
        $baseDistance = (int) \App\Models\Setting::get('delivery_base_distance_km', 3);
        $feePerKm = (int) \App\Models\Setting::get('delivery_fee_per_km', 2000);
        $maxDistance = (int) \App\Models\Setting::get('delivery_max_distance_km', 20);
        $markupFee = (int) \App\Models\Setting::get('delivery_markup_fee', 2000);

        if ($distanceKm > $maxDistance) {
            return 0; // di luar jangkauan
        }

        if ($distanceKm <= $baseDistance) {
            return $baseFee + $markupFee;
        }

        $extraKm = ceil($distanceKm - $baseDistance);
        return $baseFee + ($extraKm * $feePerKm) + $markupFee;
    }
}
