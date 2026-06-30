<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ═══════════════════════════════════════════════════════════════
// SCHEDULER: Auto-expire unpaid online orders (every minute)
// Pesanan online yang melewati batas 15 menit akan otomatis dibatalkan.
// ═══════════════════════════════════════════════════════════════
Schedule::command('orders:expire-unpaid')->everyMinute();
