<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\Order::autoProgressAllActive();
$o = App\Models\Order::find(30);
if ($o) {
    echo "Status: {$o->order_status}\n";
}
