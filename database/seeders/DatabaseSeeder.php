<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@pointsale.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Cashier account
        User::create([
            'name'     => 'Cashier',
            'email'    => 'cashier@pointsale.com',
            'password' => Hash::make('password'),
            'role'     => 'cashier',
        ]);

        // Sample categories
        $beverages = Category::create(['name' => 'Beverages', 'slug' => 'beverages']);
        $food      = Category::create(['name' => 'Food',      'slug' => 'food']);
        $desserts  = Category::create(['name' => 'Desserts',  'slug' => 'desserts']);

        // Sample products
        $products = [
            // Beverages
            ['name' => 'Iced Coffee',    'sku' => 'BEV-001', 'price' => 150, 'cost' => 60,  'stock' => 50, 'category_id' => $beverages->id],
            ['name' => 'White Mocha',    'sku' => 'BEV-002', 'price' => 175, 'cost' => 70,  'stock' => 50, 'category_id' => $beverages->id],
            ['name' => 'Matcha Latte',   'sku' => 'BEV-003', 'price' => 165, 'cost' => 65,  'stock' => 40, 'category_id' => $beverages->id],

            // Food
            ['name' => 'Club Sandwich',  'sku' => 'FOD-001', 'price' => 220, 'cost' => 100, 'stock' => 30, 'category_id' => $food->id],
            ['name' => 'Truffle Pasta',  'sku' => 'FOD-002', 'price' => 260, 'cost' => 120, 'stock' => 20, 'category_id' => $food->id],
            ['name' => 'Caesar Salad',   'sku' => 'FOD-003', 'price' => 195, 'cost' => 80,  'stock' => 25, 'category_id' => $food->id],

            // Desserts
            ['name' => 'Chocolate Mousse', 'sku' => 'DES-001', 'price' => 145, 'cost' => 55, 'stock' => 30, 'category_id' => $desserts->id],
            ['name' => 'Cafe Dessert Cup', 'sku' => 'DES-002', 'price' => 155, 'cost' => 60, 'stock' => 25, 'category_id' => $desserts->id],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}