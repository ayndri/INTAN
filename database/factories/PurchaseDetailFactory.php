<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\SaleProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseDetail>
 */
class PurchaseDetailFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = PurchaseDetail::class;
  public function definition()
  {
    $quantity = $this->faker->numberBetween(1, 100);
    $purchase_price = $this->faker->randomFloat(2, 1, 100);
    $subtotal = $quantity * $purchase_price;

    return [
      'purchase_id' => Purchase::inRandomOrder()->first()->id,
      'product_id' => Product::inRandomOrder()->first()->id,
      'quantity' => $quantity,
      'purchase_price' => $purchase_price,
      'subtotal' => $subtotal,
    ];
  }
}
