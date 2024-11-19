<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = Supplier::class;
  public function definition()
  {
    return [
      'name' => $this->faker->company, // Supplier name
      'email' => $this->faker->unique()->safeEmail, // Unique email
      'phone_code' => $this->faker->randomElement(['+1', '+44', '+91', '+62']), // Random country phone code
      'phone' => $this->faker->unique()->numerify('##########'), // 10-digit phone number
      'address' => $this->faker->address, // Random address
      'city_id' => $this->faker->numberBetween(1, 500), // Random city ID
      'country_id' => $this->faker->numberBetween(1, 200), // Random country ID
      'avatar' => $this->faker->imageUrl(100, 100, 'people', true, 'Avatar'), // Random avatar URL
      'description' => $this->faker->sentence(10), // Random description
      'code' => $this->faker->unique()->bothify('SUP-####'), // Unique supplier code
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
