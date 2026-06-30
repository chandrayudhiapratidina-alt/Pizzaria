<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Profil Toko & Operasional
        $storeName = Setting::get('store_name', 'Pizzaria');
        $storeNameFirst = Setting::get('store_name_first', 'PIZZA');
        $storeNameSecond = Setting::get('store_name_second', 'RIA');
        $brandColorFirst = Setting::get('brand_color_first', '#ffffff');
        $brandColorSecond = Setting::get('brand_color_second', '#c00a27');
        $storeAddress = Setting::get('store_address', 'Jl. Jenderal Sudirman No. 123, Purwokerto');
        $storeLogo = Setting::get('store_logo');
        $storeOpeningHours = Setting::get('store_opening_hours', '10:00 - 22:00');
        $storePhone = Setting::get('store_phone', '+62 811 2233 4455');
        $storeEmail = Setting::get('store_email', 'ciao@pizzaria.com');
        $estimatedTimeDinein = Setting::get('estimated_time_dinein', '15 - 20 Menit');
        $estimatedTimeOnline = Setting::get('estimated_time_online', '30 - 45 Menit');
        $autoPaymentExpiryMinutes = Setting::get('auto_payment_expiry_minutes', 15);
        $autoProcessSeconds = Setting::get('auto_process_seconds', 0);
        $autoCompleteSeconds = Setting::get('auto_complete_seconds', 0);
        
        // Pembayaran Manual & QRIS
        $qrisImage = Setting::get('qris_image');
        $bankName = Setting::get('bank_name');
        $bankAccountNumber = Setting::get('bank_account_number');
        $bankAccountOwner = Setting::get('bank_account_owner');
        
        // Midtrans Gateway
        $midtransEnvironment = Setting::get('midtrans_environment', 'sandbox');
        $midtransServerKey = Setting::get('midtrans_server_key');
        $midtransClientKey = Setting::get('midtrans_client_key');
        
        // Pajak & Biaya
        $taxRate = Setting::get('tax_rate', 11);
        
        // Ongkos Kirim (Biteship Integration)
        $biteshipApiKey = Setting::get('biteship_api_key');
        $deliveryMarkupFee = Setting::get('delivery_markup_fee', 2000);
        $storeLatitude = Setting::get('store_latitude', '-7.4116');
        $storeLongitude = Setting::get('store_longitude', '109.2638');
        
        // Welcome Promo Settings
        $welcomePromoActive = Setting::get('welcome_promo_active', '0');
        $welcomePromoId = Setting::get('welcome_promo_id');
        $welcomePromoTitle = Setting::get('welcome_promo_title', 'Pesan Sekarang & Dapatkan Diskon 20% untuk Pesanan Pertama Anda');
        $welcomePromoSubtitle = Setting::get('welcome_promo_subtitle', 'Nikmati pizza fine dining dari kenyamanan rumah Anda.');
        
        $targetDaily = Setting::get('target_daily', 5000000);
        $targetWeekly = Setting::get('target_weekly', 35000000);
        $targetMonthly = Setting::get('target_monthly', 150000000);

        // Ambil SEMUA promo aktif agar admin bisa bebas memilih promo apa yang ingin diiklankan
        $promotions = \App\Models\Promotion::where('is_active', true)->get();

        return view('admin.settings.index', compact(
            'storeName', 'storeNameFirst', 'storeNameSecond', 'brandColorFirst', 'brandColorSecond',
            'storeAddress', 'storeLogo', 'storeOpeningHours', 'storePhone', 'storeEmail',
            'estimatedTimeDinein', 'estimatedTimeOnline',
            'autoPaymentExpiryMinutes', 'autoProcessSeconds', 'autoCompleteSeconds',
            'qrisImage', 'bankName', 'bankAccountNumber', 'bankAccountOwner',
            'midtransEnvironment', 'midtransServerKey', 'midtransClientKey',
            'taxRate', 
            'biteshipApiKey', 'deliveryMarkupFee', 'storeLatitude', 'storeLongitude',
            'targetDaily', 'targetWeekly', 'targetMonthly',
            'welcomePromoActive', 'welcomePromoId', 'welcomePromoTitle', 'welcomePromoSubtitle', 'promotions'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'qris_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($request->hasFile('qris_image')) {
            $imagePath = $request->file('qris_image')->store('settings', 'public');
            $oldImage = Setting::get('qris_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            Setting::set('qris_image', $imagePath);
        }

        // Hapus logo jika dicentang
        if ($request->input('delete_logo') == '1') {
            $oldLogo = Setting::get('store_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('store_logo', null);
        }

        if ($request->hasFile('store_logo')) {
            $logoPath = $request->file('store_logo')->store('settings', 'public');
            $oldLogo = Setting::get('store_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('store_logo', $logoPath);
        }

        // Simpan semua keys teks secara berulang
        $keysToSave = [
            'store_name_first', 'store_name_second', 'brand_color_first', 'brand_color_second',
            'store_address', 'store_opening_hours', 'store_phone', 'store_email',
            'bank_name', 'bank_account_number', 'bank_account_owner',
            'midtrans_environment', 'midtrans_server_key', 'midtrans_client_key',
            'tax_rate',
            'biteship_api_key',
            'delivery_base_fee', 'delivery_base_distance_km', 'delivery_fee_per_km', 'delivery_max_distance_km',
            'delivery_markup_fee', 'store_latitude', 'store_longitude',
            'estimated_time_dinein', 'estimated_time_online',
            'auto_payment_expiry_minutes', 'auto_process_seconds', 'auto_complete_seconds',
            'target_daily', 'target_weekly', 'target_monthly',
            'welcome_promo_active', 'welcome_promo_id', 'welcome_promo_title', 'welcome_promo_subtitle'
        ];

        $settings = $request->except(['_token', '_method', 'qris_image', 'store_logo', 'quick_promo_discount_type', 'quick_promo_discount_value', 'welcome_promo_code']);

        foreach ($keysToSave as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        // Sinkronisasi store_name utama
        $firstName = Setting::get('store_name_first', 'PIZZA');
        $secondName = Setting::get('store_name_second', 'RIA');
        Setting::set('store_name', $firstName . $secondName);

        // Smart Promo Auto-Creation Logic
        if ($request->filled('welcome_promo_code')) {
            $code = strtoupper(trim($request->welcome_promo_code));
            
            // Check if it exists or create new
            $promo = \App\Models\Promotion::firstOrCreate(
                ['code' => $code],
                [
                    'description' => 'Promo ' . $code,
                    'discount_type' => $request->input('quick_promo_discount_type') ?: 'percentage',
                    'discount_value' => $request->input('quick_promo_discount_value') ?: 10,
                    'min_order_amount' => 0,
                    'is_active' => true,
                    'expires_at' => $request->input('quick_promo_expires_at') ?: now()->addDays(30),
                    'theme_color' => $request->input('quick_promo_theme_color', '#E8304A'),
                    'icon' => $request->input('quick_promo_icon', 'fa-bolt')
                ]
            );

            // Update thematic fields & duration
            $promoUpdateData = [];
            
            if ($request->filled('quick_promo_discount_value')) {
                $promoUpdateData['discount_type'] = $request->quick_promo_discount_type;
                $promoUpdateData['discount_value'] = $request->quick_promo_discount_value;
            }
            if ($request->filled('quick_promo_expires_at')) {
                $promoUpdateData['expires_at'] = $request->quick_promo_expires_at;
            }
            if ($request->filled('quick_promo_min_order_amount')) {
                $promoUpdateData['min_order_amount'] = $request->quick_promo_min_order_amount;
            }
            if ($request->filled('quick_promo_theme_color')) {
                $promoUpdateData['theme_color'] = $request->quick_promo_theme_color;
            }
            if ($request->filled('quick_promo_icon')) {
                $promoUpdateData['icon'] = $request->quick_promo_icon;
            }
            if ($request->filled('welcome_promo_title')) {
                $promoUpdateData['banner_title'] = $request->welcome_promo_title;
            }
            if ($request->filled('welcome_promo_subtitle')) {
                $promoUpdateData['banner_subtitle'] = $request->welcome_promo_subtitle;
            }

            if ($request->hasFile('quick_promo_background_image')) {
                $path = $request->file('quick_promo_background_image')->store('promotions', 'public');
                $promoUpdateData['background_image'] = '/storage/' . $path;
            }

            if (!empty($promoUpdateData)) {
                $promo->update($promoUpdateData);
            }
            
            // Save the ID to settings
            Setting::set('welcome_promo_id', $promo->id);
        } else {
            Setting::set('welcome_promo_id', null);
        }

        foreach ($keysToSave as $key) {
            // Checkbox value needs fallback to 0 if not present
            if ($key === 'welcome_promo_active') {
                Setting::set($key, $request->has($key) ? '1' : '0');
            } else if ($key !== 'welcome_promo_id' && $request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
