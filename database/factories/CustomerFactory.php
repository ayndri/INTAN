<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = Customer::class;
  public function definition()
  {
    return [
      'name' => $this->faker->name,
      'email' => $this->faker->unique()->safeEmail,
      'phone_code' => $this->faker->optional()->randomElement(['+1', '+44', '+62', '+91']),
      'phone' => $this->faker->optional()->numerify('##########'), // 10-digit number
      'address' => $this->faker->optional()->address,
      'city_id' => $this->faker->optional()->numberBetween(1, 100), // Replace range with actual city IDs if available
      'country_id' => $this->faker->optional()->numberBetween(1, 200), // Replace range with actual country IDs if available
      'avatar' => $this->faker->optional()->imageUrl(100, 100, 'people'), // Random avatar
      'description' => $this->faker->optional()->sentence,
      'code' => Str::random(10), // Unique customer code
    ];
  }
}
