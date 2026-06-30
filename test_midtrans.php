<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Midtrans\Config::$serverKey = 'SB-Mid-server-GwUP_WGbJPXsDzsNEBRs8IYA';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
$params = ['transaction_details' => ['order_id' => 'TEST-'.rand(), 'gross_amount' => 10000]];
try {
    $token = \Midtrans\Snap::getSnapToken($params);
    echo "SUCCESS TOKEN: " . $token;
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage();
}
