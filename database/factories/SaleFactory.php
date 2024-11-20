<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = Sale::class;
  public function definition()
  {
    return [
      'customer_id' => Customer::inRandomOrder()->first()->id, // Assuming you have CustomerFactory
      'total' => $this->faker->randomFloat(2, 50, 1000),
      'sale_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
      'status' => $this->faker->randomElement(['pending', 'in-progress', 'completed', 'cancelled']),
      'order_type' => $this->faker->randomElement(['offline', 'online']),
      'shipping_cost' => $this->faker->randomFloat(2, 0, 50),
    ];
  }
}
