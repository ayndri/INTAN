<?php

namespace Database\Seeders;

use App\Models\AccountingEntry;
use App\Models\Brand;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sale;
use App\Models\SaleProduct;
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
    \App\Models\User::factory(10)->create();  // Create 30 users

    $this->call([
      CountriesTableSeeder::class,
      StatesTableSeeder::class,
      CitiesTableSeeder::class,
    ]);

    // Create 8 unique units
    $units = [
      ['unit_name' => 'Centimeters', 'short_name' => 'cm'],
      ['unit_name' => 'Meters', 'short_name' => 'm'],
      ['unit_name' => 'Kilograms', 'short_name' => 'kg'],
      ['unit_name' => 'Grams', 'short_name' => 'g'],
      ['unit_name' => 'Liters', 'short_name' => 'l'],
      ['unit_name' => 'Milliliters', 'short_name' => 'ml'],
      ['unit_name' => 'Pieces', 'short_name' => 'pcs'],
      ['unit_name' => 'Dozen', 'short_name' => 'dz'],
    ];

    foreach ($units as $unit) {
      Unit::create([
        'unit_name' => $unit['unit_name'],
        'short_name' => $unit['short_name'],
        'status' => true,
      ]);
    }

    \App\Models\Brand::factory(10)->create();
    \App\Models\Category::factory(10)->create();  // Membuat 20 brand

    \App\Models\Supplier::factory(10)->create();  // Membuat 20 supplier
    \App\Models\Customer::factory(10)->create();  // Membuat 20 customer

    \App\Models\Product::factory(10)->create();

    Purchase::factory()
      ->count(10)
      ->has(PurchaseDetail::factory()->count(rand(2, 5)))
      ->create();

    Sale::factory()
      ->count(10)
      ->has(SaleProduct::factory()->count(rand(2, 5)))
      ->create();

    // // Create 30 products and associate them with existing units and brands
    // Product::factory(30)->create()->each(function ($product) {
    //     // Randomly select a brand and unit
    //     $brand = Brand::inRandomOrder()->first();
    //     $unit = Unit::inRandomOrder()->first();

    //     // Update product with brand_id and unit_id
    //     $product->update([
    //         'brand_id' => $brand->id,
    //         'unit_id' => $unit->id,
    //     ]);

    //     // Create 10 purchases (expense) for each product
    //     \App\Models\Purchase::factory(10)->create([
    //         'product_id' => $product->id,
    //         'supplier_id' => \App\Models\Supplier::inRandomOrder()->first()->id,  // Random supplier
    //     ])->each(function ($purchase) use ($product) {
    //         // Create accounting entries for each purchase (expense)
    //         AccountingEntry::factory()->create([
    //             'description' => 'Purchase entry for product ' . $product->id,
    //             'amount' => $purchase->total,  // Amount based on the total of the purchase
    //             'type' => 'expense',  // Type is 'expense'
    //             'purchase_id' => $purchase->id,  // Associate with the purchase
    //             'sale_id' => null,  // Sale_id should be null
    //         ]);

    //         // Create inventory movement for each purchase (incoming stock)
    //         InventoryMovement::factory(1)->create([
    //             'product_id' => $product->id,
    //             'purchase_id' => $purchase->id,  // Associate movement with the purchase
    //             'sale_id' => null,  // Sale_id should be null
    //             'type' => 'in',  // Incoming inventory movement
    //             'quantity' => $purchase->quantity,
    //         ]);
    //     });

    //     // Create 10 sales (income) for each product
    //     Sale::factory(10)->create([
    //         'product_id' => $product->id,
    //         'customer_id' => \App\Models\Customer::inRandomOrder()->first()->id,  // Random customer
    //     ])->each(function ($sale) use ($product) {
    //         // Create accounting entries for each sale (income)
    //         AccountingEntry::factory()->create([
    //             'description' => 'Sale entry for product ' . $product->id,
    //             'amount' => $sale->total,  // Amount based on the total of the sale
    //             'type' => 'income',  // Type is 'income'
    //             'sale_id' => $sale->id,  // Associate with the sale
    //             'purchase_id' => null,  // Purchase_id should be null
    //         ]);

    //         // Create inventory movement for each sale (outgoing stock)
    //         InventoryMovement::factory(1)->create([
    //             'product_id' => $product->id,
    //             'sale_id' => $sale->id,  // Associate movement with the sale
    //             'purchase_id' => null,  // Purchase_id should be null
    //             'type' => 'out',  // Outgoing inventory movement
    //             'quantity' => $sale->quantity,
    //         ]);
    //     });
    // });
  }
}
