<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('client.guest.catalog');
});

Route::get('/init-seeder', function () {
    if (\App\Models\Category::count() === 0) {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return "Database berhasil di-seed! Silakan kembali ke halaman utama.";
    }
    return "Database sudah memiliki data, seeding dibatalkan (untuk mencegah duplikasi).";
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $incoming_orders = \App\Models\Order::with('items.menu')->where('payment_status', 'paid')->whereIn('order_status', ['confirmed', 'cooking'])->get();
        $payment_queue = \App\Models\Order::with('items.menu')->where('payment_status', 'pending')->whereIn('order_status', ['pending_payment', 'cooking'])->get();
        
        // Calculate Statistics
        $today = \Carbon\Carbon::today();
        $startOfWeek = \Carbon\Carbon::now()->startOfWeek();
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        
        $daily_sales = \App\Models\Order::where('payment_status', 'paid')->whereDate('created_at', $today)->sum('total_amount');
        $weekly_sales = \App\Models\Order::where('payment_status', 'paid')->where('created_at', '>=', $startOfWeek)->sum('total_amount');
        $monthly_sales = \App\Models\Order::where('payment_status', 'paid')->where('created_at', '>=', $startOfMonth)->sum('total_amount');
        
        // Dynamic targets from Database (Settings)
        $daily_target = \App\Models\Setting::get('target_daily', 1000000);
        $weekly_target = \App\Models\Setting::get('target_weekly', 7000000);
        $monthly_target = \App\Models\Setting::get('target_monthly', 30000000);

        return view('admin.dashboard', compact(
            'incoming_orders', 'payment_queue', 
            'daily_sales', 'weekly_sales', 'monthly_sales',
            'daily_target', 'weekly_target', 'monthly_target'
        ));
    })->name('dashboard');
    
    Route::resource('/categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('/menus', \App\Http\Controllers\Admin\MenuController::class);
    Route::resource('/customizations', \App\Http\Controllers\Admin\CustomizationController::class)->except(['show']);
    Route::resource('/ingredients', \App\Http\Controllers\Admin\IngredientController::class);
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
    Route::post('orders/{order}/update-status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('orders/{order}/update-payment', [App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.updatePayment');
    
    // Customization Management
    // (Route already defined above)

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    Route::resource('/staff', \App\Http\Controllers\Admin\StaffController::class);

    // Promotions & Discounts
    Route::resource('/promotions', \App\Http\Controllers\Admin\PromotionController::class);

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export-pdf', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.exportPdf');
    Route::get('/analytics/export-excel', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportExcel'])->name('analytics.exportExcel');

    Route::get('/tables', [\App\Http\Controllers\Admin\TableController::class, 'index'])->name('tables.index');
    Route::post('/tables', [\App\Http\Controllers\Admin\TableController::class, 'store'])->name('tables.store');
    Route::delete('/tables/{table}', [\App\Http\Controllers\Admin\TableController::class, 'destroy'])->name('tables.destroy');

    // Admin Advanced Notification API (Polling)
    Route::get('/api/notifications', function () {
        $notifications = [];

        // 1. Low Stock Alerts
        $lowStocks = \App\Models\Ingredient::whereColumn('stock_qty', '<=', 'minimum_stock_alert')->get();
        foreach($lowStocks as $item) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => '<i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i>',
                'title' => 'Stok Kritis',
                'message' => 'Stok ' . $item->name . ' tersisa ' . $item->stock_qty . ' ' . $item->unit . '.',
                'time' => 'Sekarang',
                'link' => route('admin.ingredients.index')
            ];
        }

        // 2. Bad Reviews
        $badReviews = \App\Models\ProductReview::with('menu')
            ->where('rating', '<=', 2)
            ->where('is_approved', false)
            ->get();
        foreach($badReviews as $review) {
            $menuName = $review->menu ? $review->menu->name : 'Menu';
            $notifications[] = [
                'type' => 'danger',
                'icon' => '<i class="fa-solid fa-star" style="color: #ef4444;"></i>',
                'title' => 'Ulasan Buruk',
                'message' => 'Review ' . $review->rating . ' bintang pada ' . $menuName . '.',
                'time' => $review->created_at->diffForHumans(),
                'link' => route('admin.reviews.index')
            ];
        }

        // 3. Operational Alerts (End of shift reminder)
        $currentHour = (int)\Carbon\Carbon::now()->format('H');
        if($currentHour >= 21 || $currentHour < 3) {
            $notifications[] = [
                'type' => 'info',
                'icon' => '<i class="fa-solid fa-cash-register" style="color: #3b82f6;"></i>',
                'title' => 'Peringatan Sistem Kasir',
                'message' => 'Sudah malam, jangan lupa tutup buku (End of Shift)!',
                'time' => \Carbon\Carbon::now()->format('H:i'),
                'link' => '#'
            ];
        }

        return response()->json([
            'count' => count($notifications),
            'notifications' => $notifications
        ]);
    })->name('api.notifications');

    // Product Reviews Moderation
    Route::get('reviews', [App\Http\Controllers\Admin\ProductReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{id}/toggle', [App\Http\Controllers\Admin\ProductReviewController::class, 'toggleApproval'])->name('reviews.toggle');

    // Store Reviews Moderation
    Route::get('store-reviews', [App\Http\Controllers\Admin\StoreReviewController::class, 'index'])->name('store_reviews.index');
    Route::get('store-reviews/create', [App\Http\Controllers\Admin\StoreReviewController::class, 'create'])->name('store_reviews.create');
    Route::post('store-reviews', [App\Http\Controllers\Admin\StoreReviewController::class, 'store'])->name('store_reviews.store');
    Route::get('store-reviews/{id}/edit', [App\Http\Controllers\Admin\StoreReviewController::class, 'edit'])->name('store_reviews.edit');
    Route::put('store-reviews/{id}', [App\Http\Controllers\Admin\StoreReviewController::class, 'update'])->name('store_reviews.update');
    Route::patch('store-reviews/{id}/toggle', [App\Http\Controllers\Admin\StoreReviewController::class, 'toggleApproval'])->name('store_reviews.toggle');
    Route::delete('store-reviews/{id}', [App\Http\Controllers\Admin\StoreReviewController::class, 'destroy'])->name('store_reviews.destroy');
});

Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/cashier/dashboard', [\App\Http\Controllers\Cashier\OrderController::class, 'index'])->name('cashier.dashboard');
    Route::get('/cashier/pos', [\App\Http\Controllers\Cashier\PosController::class, 'index'])->name('cashier.pos');
    Route::post('/cashier/pos/checkout', [\App\Http\Controllers\Cashier\PosController::class, 'store'])->name('cashier.pos.checkout');
    Route::post('/cashier/orders/{order}/status', [\App\Http\Controllers\Cashier\OrderController::class, 'updateStatus'])->name('cashier.orders.status');
    Route::post('/cashier/orders/{order}/promo', [\App\Http\Controllers\Cashier\OrderController::class, 'applyPromotion'])->name('cashier.orders.promo');
    Route::get('/cashier/history', [\App\Http\Controllers\Cashier\OrderController::class, 'history'])->name('cashier.history');
    Route::get('/cashier/orders/{order}/receipt', [\App\Http\Controllers\Cashier\OrderController::class, 'receipt'])->name('cashier.orders.receipt');
});

// ═══════════════════════════════════════════════════════════════
// CLIENT ROUTES — Dine-In (QR Code) & Shared
// ═══════════════════════════════════════════════════════════════
Route::prefix('client')->name('client.guest.')->group(function () {
    Route::get('/catalog', [\App\Http\Controllers\ClientMenuController::class, 'index'])->name('catalog');
    Route::get('/menu/{id}', [\App\Http\Controllers\ClientMenuController::class, 'show'])->name('menu.show');
    

    Route::post('/cart/add', [\App\Http\Controllers\ClientMenuController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [\App\Http\Controllers\ClientMenuController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/apply-voucher', [\App\Http\Controllers\ClientMenuController::class, 'applyVoucher'])->name('cart.voucher');
    Route::post('/cart/remove-voucher', [\App\Http\Controllers\ClientMenuController::class, 'removeVoucher'])->name('cart.voucher.remove');
    Route::get('/checkout/dine-in', [\App\Http\Controllers\ClientMenuController::class, 'dineInCheckoutForm'])->name('dinein.checkout');
    Route::post('/checkout', [\App\Http\Controllers\ClientMenuController::class, 'checkout'])->name('checkout');
    Route::get('/orders/{id}', [\App\Http\Controllers\ClientMenuController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [\App\Http\Controllers\ClientMenuController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{id}/payment-success', [\App\Http\Controllers\ClientMenuController::class, 'paymentSuccessCallback'])->name('orders.payment-success');
    Route::get('/orders/{order_number}/receipt', [\App\Http\Controllers\ClientMenuController::class, 'downloadReceipt'])->name('orders.receipt');

    // ── SPA AJAX API ROUTES ──
    Route::get('/api/cart', [\App\Http\Controllers\ClientMenuController::class, 'getCartApi'])->name('api.cart');
    Route::post('/api/set-mode', [\App\Http\Controllers\ClientMenuController::class, 'setModeApi'])->name('api.mode');
    Route::get('/api/quick-view/{id}', [\App\Http\Controllers\ClientMenuController::class, 'getQuickViewApi'])->name('api.quick-view');
    Route::get('/api/orders/{id}/status', [\App\Http\Controllers\ClientMenuController::class, 'orderStatusApi'])->name('api.orders.status');
    Route::post('/api/login', [\App\Http\Controllers\AuthController::class, 'loginApi'])->name('api.login');
    
    // Store Review Submission
    Route::post('/api/store-reviews', [\App\Http\Controllers\Client\StoreReviewController::class, 'store'])->name('api.store-reviews');
});

// ═══════════════════════════════════════════════════════════════
// CLIENT ROUTES — Online (Delivery / Pickup)
// Pembayaran WAJIB di muka. Tidak ada COD.
// ═══════════════════════════════════════════════════════════════
Route::prefix('client/online')->name('client.online.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Client\OnlineOrderController::class, 'index'])->name('landing');
    Route::post('/welcome-promo/claim', [\App\Http\Controllers\Client\OnlineOrderController::class, 'claimWelcomePromo'])->name('welcome.claim');
    Route::get('/mode/{type}', [\App\Http\Controllers\Client\OnlineOrderController::class, 'setMode'])->name('mode');
    
    // Online Delivery/Pickup Checkout (Requires Auth)
    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [\App\Http\Controllers\Client\OnlineOrderController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [\App\Http\Controllers\Client\OnlineOrderController::class, 'processCheckout'])->name('checkout.process');
        Route::post('/checkout/promo/apply', [\App\Http\Controllers\Client\OnlineOrderController::class, 'applyPromo'])->name('checkout.promo.apply');
        Route::post('/checkout/promo/remove', [\App\Http\Controllers\Client\OnlineOrderController::class, 'removePromo'])->name('checkout.promo.remove');
        Route::post('/biteship-rates', [\App\Http\Controllers\Client\OnlineOrderController::class, 'calculateDeliveryFeeApi'])->name('checkout.biteship-rates');
        
        // Profile & Address
        Route::get('/profile', [App\Http\Controllers\Client\CustomerProfileController::class, 'index'])->name('profile');
        Route::post('/profile', [App\Http\Controllers\Client\CustomerProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/address', [App\Http\Controllers\Client\CustomerProfileController::class, 'storeAddress'])->name('profile.address.store');
        Route::delete('/profile/address/{id}', [App\Http\Controllers\Client\CustomerProfileController::class, 'destroyAddress'])->name('profile.address.destroy');

        // Product Reviews
        Route::get('/orders/{order}/reviews/create', [App\Http\Controllers\Client\ProductReviewController::class, 'create'])->name('orders.reviews.create');
        Route::post('/orders/{order}/reviews', [App\Http\Controllers\Client\ProductReviewController::class, 'store'])->name('orders.reviews.store');
        
        // Store Reviews (My Reviews)
        Route::put('/profile/store-reviews/{id}', [App\Http\Controllers\Client\CustomerProfileController::class, 'updateStoreReview'])->name('profile.store-reviews.update');
        Route::delete('/profile/store-reviews/{id}', [App\Http\Controllers\Client\CustomerProfileController::class, 'destroyStoreReview'])->name('profile.store-reviews.destroy');
    });

    Route::get('/orders/{id}', [\App\Http\Controllers\Client\OnlineOrderController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [\App\Http\Controllers\Client\OnlineOrderController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/{id}/payment-verify', [\App\Http\Controllers\Client\OnlineOrderController::class, 'paymentCallback'])->name('orders.verify');
    Route::post('/orders/{id}/simulate-payment', [\App\Http\Controllers\Client\OnlineOrderController::class, 'simulatePayment'])->name('orders.simulate-payment');
});

// Static Pages
Route::view('/about', 'client.about')->name('about');
Route::view('/faq', 'client.static.faq')->name('faq');
Route::view('/privacy', 'client.static.privacy')->name('privacy');
Route::view('/terms', 'client.static.terms')->name('terms');
