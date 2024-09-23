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
        \App\Models\User::factory(30)->create();
        \App\Models\Unit::factory(20)->create();
        \App\Models\Brand::factory(20)->create();
        \App\Models\Supplier::factory(20)->create();
        \App\Models\Customer::factory(20)->create();

        Product::factory(30)->create()->each(function ($product) {
            $brand = Brand::inRandomOrder()->first();
            $unit = Unit::inRandomOrder()->first();

            $product->update([
                'brand_id' => $brand->id,
                'unit_id' => $unit->id,
            ]);

            InventoryMovement::factory(10)->create(['product_id' => $product->id]);

            \App\Models\Purchase::factory(10)->create([
                'product_id' => $product->id,
                'supplier_id' => \App\Models\Supplier::inRandomOrder()->first()->id, // Supplier acak untuk setiap pembelian
            ]);

            Sale::factory(10)->create([
                'product_id' => $product->id,
                'customer_id' => \App\Models\Customer::inRandomOrder()->first()->id, // Customer acak untuk setiap penjualan
            ]);

            AccountingEntry::factory(10)->create();
        });
    }
}
