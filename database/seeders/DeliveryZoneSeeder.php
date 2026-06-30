<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryZone;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name'            => 'Zona Dekat',
                'description'     => 'Area sekitar restoran (radius 0-3 km)',
                'fee'             => 10000,
                'min_distance_km' => 0,
                'max_distance_km' => 3,
                'sort_order'      => 1,
            ],
            [
                'name'            => 'Zona Sedang',
                'description'     => 'Area kecamatan terdekat (radius 3-7 km)',
                'fee'             => 15000,
                'min_distance_km' => 3,
                'max_distance_km' => 7,
                'sort_order'      => 2,
            ],
            [
                'name'            => 'Zona Jauh',
                'description'     => 'Area kabupaten/kota (radius 7-15 km)',
                'fee'             => 25000,
                'min_distance_km' => 7,
                'max_distance_km' => 15,
                'sort_order'      => 3,
            ],
        ];

        foreach ($zones as $zone) {
            DeliveryZone::updateOrCreate(
                ['name' => $zone['name']],
                $zone
            );
        }
    }
}
