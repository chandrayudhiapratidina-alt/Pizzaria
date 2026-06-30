<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share store settings globally with all views
        view()->composer('*', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    $storeNameFirst = \App\Models\Setting::get('store_name_first', 'PIZZA');
                    $storeNameSecond = \App\Models\Setting::get('store_name_second', 'RIA');
                    $brandColorFirst = \App\Models\Setting::get('brand_color_first', '#ffffff');
                    $brandColorSecond = \App\Models\Setting::get('brand_color_second', '#c00a27');
                    $storeName = $storeNameFirst . $storeNameSecond;
                    $storeLogo = \App\Models\Setting::get('store_logo');
                    $storeAddress = \App\Models\Setting::get('store_address', 'Jl. Jenderal Sudirman No. 123, Purwokerto');
                    $storePhone = \App\Models\Setting::get('store_phone', '+62 811 2233 4455');
                    $storeEmail = \App\Models\Setting::get('store_email', 'ciao@pizzaria.com');
                } else {
                    $storeNameFirst = 'PIZZA';
                    $storeNameSecond = 'RIA';
                    $brandColorFirst = '#ffffff';
                    $brandColorSecond = '#c00a27';
                    $storeName = 'Pizzaria';
                    $storeLogo = null;
                    $storeAddress = 'Jl. Jenderal Sudirman No. 123, Purwokerto';
                    $storePhone = '+62 811 2233 4455';
                    $storeEmail = 'ciao@pizzaria.com';
                }
            } catch (\Exception $e) {
                $storeNameFirst = 'PIZZA';
                $storeNameSecond = 'RIA';
                $brandColorFirst = '#ffffff';
                $brandColorSecond = '#c00a27';
                $storeName = 'Pizzaria';
                $storeLogo = null;
                $storeAddress = 'Jl. Jenderal Sudirman No. 123, Purwokerto';
                $storePhone = '+62 811 2233 4455';
                $storeEmail = 'ciao@pizzaria.com';
            }

            $view->with(compact('storeName', 'storeNameFirst', 'storeNameSecond', 'brandColorFirst', 'brandColorSecond', 'storeLogo', 'storeAddress', 'storePhone', 'storeEmail'));
        });
    }
}
