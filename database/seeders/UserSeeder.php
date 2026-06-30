<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@pizzaria.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        
        \App\Models\User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@pizzaria.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
        ]);
        
        \App\Models\User::create([
            'name' => 'Client User',
            'email' => 'client@pizzaria.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);
    }
}
