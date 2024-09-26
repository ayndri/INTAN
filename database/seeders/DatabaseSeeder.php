<?php

namespace Database\Seeders;

use App\Models\AccountingEntry;
use App\Models\Brand;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(30)->create();  // Membuat 30 user

        // Membuat hanya 8 unit unik
        $unitNames = ['Pcs', 'Box', 'Kg', 'Liter', 'Dozen', 'Pack', 'Bottle', 'Carton'];

        foreach ($unitNames as $unitName) {
            \App\Models\Unit::factory()->create([
                'unit_name' => $unitName,  // Set unique unit_name
            ]);
        }

        \App\Models\Brand::factory(20)->create();  // Membuat 20 brand
        \App\Models\Supplier::factory(20)->create();  // Membuat 20 supplier
        \App\Models\Customer::factory(20)->create();  // Membuat 20 customer

        // Membuat produk dan mengaitkan dengan unit dan brand yang ada
        Product::factory(30)->create()->each(function ($product) {
            $brand = Brand::inRandomOrder()->first();  // Ambil brand acak
            $unit = Unit::inRandomOrder()->first();    // Ambil unit acak dari yang sudah ada

            // Update produk dengan brand_id dan unit_id
            $product->update([
                'brand_id' => $brand->id,
                'unit_id' => $unit->id,
            ]);

            // Membuat pergerakan inventory untuk setiap produk
            InventoryMovement::factory(10)->create(['product_id' => $product->id]);

            // Membuat pembelian untuk setiap produk
            \App\Models\Purchase::factory(10)->create([
                'product_id' => $product->id,
                'supplier_id' => \App\Models\Supplier::inRandomOrder()->first()->id,  // Supplier acak
            ]);

            // Membuat penjualan untuk setiap produk
            Sale::factory(10)->create([
                'product_id' => $product->id,
                'customer_id' => \App\Models\Customer::inRandomOrder()->first()->id,  // Customer acak
            ]);

            // Membuat entri akuntansi
            AccountingEntry::factory(10)->create();
        });
    }
}
