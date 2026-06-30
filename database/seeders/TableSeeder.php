<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $table = \App\Models\Table::create([
                'table_number' => (string) $i,
                'status' => 'available',
            ]);
            $table->qr_code_url = url('/client/catalog?table=' . $table->id);
            $table->save();
        }
    }
}
