<?php
use App\Models\Promotion;
$promo = Promotion::where('code', 'WELCOME20')->first();
if ($promo) {
    $promo->description = 'Diskon 30% khusus pengguna baru';
    $promo->save();
}
echo "Fixed";
