<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = Purchase::class;
  public function definition()
  {
    return [
      'supplier_id' => Supplier::inRandomOrder()->first()->id,
      'tax' => $this->faker->randomFloat(2, 0, 1000),
      'discount' => $this->faker->randomFloat(2, 0, 500),
      'shipping' => $this->faker->randomFloat(2, 0, 200),
      'total' => $this->faker->randomFloat(2, 500, 5000),
      'purchase_date' => $this->faker->dateTimeThisYear(),
      'reference' => $this->faker->unique()->bothify('REF###??'),
      'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
    ];
  }
}
