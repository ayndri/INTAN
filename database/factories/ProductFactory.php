<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = Product::class;

  public function definition()
  {
    return [
      'name' => $this->faker->word,
      'sku' => strtoupper($this->faker->unique()->lexify('SKU-?????')),
      'category_id' => Category::inRandomOrder()->first()->id, // Set these to IDs as needed
      'brand_id' => Brand::inRandomOrder()->first()->id, // Set these to IDs as needed
      'unit_id' => Unit::inRandomOrder()->first()->id, // Set these to IDs as needed
      'item_code' => strtoupper($this->faker->unique()->lexify('ITEM-?????')),
      'description' => $this->faker->sentence,
      'product_type' => 'single',
      'sell_price' => $this->faker->randomFloat(2, 1, 10000),
      'quantity' => $this->faker->numberBetween(0, 100),
      'quantity_alert' => $this->faker->numberBetween(1, 50),
    ];
  }
}
