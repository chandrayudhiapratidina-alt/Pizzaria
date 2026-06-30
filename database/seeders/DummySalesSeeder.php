<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummySalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = \App\Models\Menu::all();
        if ($menus->isEmpty()) {
            $this->command->info('Tidak ada menu, membatalkan seeder.');
            return;
        }

        $startDate = \Carbon\Carbon::create(2026, 6, 1);
        $endDate = \Carbon\Carbon::create(2026, 6, 30);
        $currentDate = clone $startDate;

        $targetPerDay = 500000; // Misal target 500k per hari

        while ($currentDate <= $endDate) {
            // Randomize jumlah order per hari (ada yang sepi, ada yang ramai untuk variasi target)
            // Biar ada hari yang ga capai target, dan ada yang melampaui target
            $numOrders = rand(2, 15);

            for ($i = 0; $i < $numOrders; $i++) {
                $orderTotal = 0;
                $items = [];
                
                // Pilih 1 - 4 menu secara acak
                $numItems = rand(1, 4);
                $selectedMenus = $menus->random($numItems);

                foreach ($selectedMenus as $menu) {
                    $qty = rand(1, 3);
                    $price = $menu->base_price;
                    $subtotal = $qty * $price;
                    $orderTotal += $subtotal;

                    $items[] = [
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'qty' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];
                }

                // Tentukan metode pembayaran
                $paymentMethod = rand(1, 10) > 4 ? 'cash' : 'qris';
                $cashTendered = null;
                $cashChange = null;
                $reference = null;

                if ($paymentMethod === 'cash') {
                    // Beri kembalian: bayar dengan pecahan 50rb atau 100rb ke atas
                    $pecahan = [50000, 100000, 150000, 200000, 300000];
                    foreach ($pecahan as $p) {
                        if ($p >= $orderTotal) {
                            $cashTendered = $p;
                            break;
                        }
                    }
                    if (!$cashTendered) $cashTendered = ceil($orderTotal / 100000) * 100000;
                    
                    // Kadang bayar pas
                    if (rand(1, 3) == 1) {
                        $cashTendered = $orderTotal;
                    }
                    
                    $cashChange = $cashTendered - $orderTotal;
                } else {
                    $reference = 'INV/QR/' . rand(100000, 999999);
                }

                // Randomize jam order dari jam 10 pagi - 9 malam
                $orderTime = (clone $currentDate)->setHour(rand(10, 21))->setMinute(rand(0, 59));

                $order = \App\Models\Order::create([
                    'order_type' => rand(1, 2) == 1 ? 'dine_in' : 'pickup',
                    'customer_name' => 'Pelanggan ' . rand(1, 100),
                    'subtotal_amount' => $orderTotal,
                    'total_amount' => $orderTotal,
                    'delivery_fee' => 0,
                    'discount_amount' => 0,
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'paid',
                    'order_status' => 'completed',
                    'paid_at' => $orderTime,
                    'created_at' => $orderTime,
                    'updated_at' => $orderTime,
                    'cash_tendered' => $cashTendered,
                    'cash_change' => $cashChange,
                    'payment_reference' => $reference,
                ]);

                foreach ($items as $item) {
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $item['menu_id'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                        'created_at' => $orderTime,
                        'updated_at' => $orderTime,
                    ]);
                }
            }

            $currentDate->addDay();
        }

        $this->command->info('Berhasil membuat data penjualan Juni 2026!');
    }
}
