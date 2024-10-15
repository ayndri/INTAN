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
        \App\Models\User::factory(30)->create();  // Create 30 users

        // Create 8 unique units
        $unitNames = ['Pcs', 'Box', 'Kg', 'Liter', 'Dozen', 'Pack', 'Bottle', 'Carton'];

        foreach ($unitNames as $unitName) {
            \App\Models\Unit::factory()->create([
                'unit_name' => $unitName,  // Set unique unit_name
            ]);
        }

        \App\Models\Brand::factory(20)->create();  // Membuat 20 brand
        \App\Models\Supplier::factory(20)->create();  // Membuat 20 supplier
        \App\Models\Customer::factory(20)->create();  // Membuat 20 customer

        // Create 30 products and associate them with existing units and brands
        Product::factory(30)->create()->each(function ($product) {
            // Randomly select a brand and unit
            $brand = Brand::inRandomOrder()->first();
            $unit = Unit::inRandomOrder()->first();

            // Update product with brand_id and unit_id
            $product->update([
                'brand_id' => $brand->id,
                'unit_id' => $unit->id,
            ]);

            // Create 10 purchases (expense) for each product
            \App\Models\Purchase::factory(10)->create([
                'product_id' => $product->id,
                'supplier_id' => \App\Models\Supplier::inRandomOrder()->first()->id,  // Random supplier
            ])->each(function ($purchase) use ($product) {
                // Create accounting entries for each purchase (expense)
                AccountingEntry::factory()->create([
                    'description' => 'Purchase entry for product ' . $product->id,
                    'amount' => $purchase->total,  // Amount based on the total of the purchase
                    'type' => 'expense',  // Type is 'expense'
                    'purchase_id' => $purchase->id,  // Associate with the purchase
                    'sale_id' => null,  // Sale_id should be null
                ]);

                // Create inventory movement for each purchase (incoming stock)
                InventoryMovement::factory(1)->create([
                    'product_id' => $product->id,
                    'purchase_id' => $purchase->id,  // Associate movement with the purchase
                    'sale_id' => null,  // Sale_id should be null
                    'type' => 'in',  // Incoming inventory movement
                    'quantity' => $purchase->quantity,
                ]);
            });

            // Create 10 sales (income) for each product
            Sale::factory(10)->create([
                'product_id' => $product->id,
                'customer_id' => \App\Models\Customer::inRandomOrder()->first()->id,  // Random customer
            ])->each(function ($sale) use ($product) {
                // Create accounting entries for each sale (income)
                AccountingEntry::factory()->create([
                    'description' => 'Sale entry for product ' . $product->id,
                    'amount' => $sale->total,  // Amount based on the total of the sale
                    'type' => 'income',  // Type is 'income'
                    'sale_id' => $sale->id,  // Associate with the sale
                    'purchase_id' => null,  // Purchase_id should be null
                ]);

                // Create inventory movement for each sale (outgoing stock)
                InventoryMovement::factory(1)->create([
                    'product_id' => $product->id,
                    'sale_id' => $sale->id,  // Associate movement with the sale
                    'purchase_id' => null,  // Purchase_id should be null
                    'type' => 'out',  // Outgoing inventory movement
                    'quantity' => $sale->quantity,
                ]);
            });
        });
    }
}
