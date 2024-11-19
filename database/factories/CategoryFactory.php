<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition()
  {
    return [
      'name' => $this->faker->word(), // Nama kategori dalam bentuk kata random
      'slug' => function (array $attributes) {
        return Str::slug($attributes['name']);
      },
      'status' => true, // Status bisa 'active' atau 'inactive'
    ];
  }
}
