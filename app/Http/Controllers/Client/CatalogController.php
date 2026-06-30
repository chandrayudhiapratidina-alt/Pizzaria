<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Customization;

class CatalogController extends Controller
{
    public function index()
    {
        $menus = Menu::where('is_available', true)->get();

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

        return view('client.catalog', compact('menus', 'showWelcomeBanner', 'welcomePromoTitle', 'welcomePromoSubtitle', 'welcomePromoCode'));
    }

    public function show($id)
    {
        $menu = Menu::findOrFail($id);
        if (!$menu->is_available) {
            return redirect()->route('client.catalog')->with('error', 'Menu tidak tersedia.');
        }

        $sizes = Customization::where('menu_id', $id)->where('type', 'size')->get();
        $crusts = Customization::where('menu_id', $id)->where('type', 'crust')->get();
        $toppings = Customization::where('menu_id', $id)->where('type', 'topping')->get();

        return view('client.menu_show', compact('menu', 'sizes', 'crusts', 'toppings'));
    }
}
