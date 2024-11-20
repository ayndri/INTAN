<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleProduct>
 */
class SaleProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = SaleProduct::class;
  public function definition()
  {
    $sellingPrice = $this->faker->randomFloat(2, 10, 500);
    $quantity = $this->faker->numberBetween(1, 10);

    return [
      'sale_id' => Sale::inRandomOrder()->first()->id,
      'product_id' => Product::inRandomOrder()->first()->id, // Assuming you have ProductFactory
      'quantity' => $quantity,
      'selling_price' => $sellingPrice,
      'total' => $sellingPrice * $quantity,
    ];
  }
}
