<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */

  protected $model = Brand::class;
  public function definition()
  {
    $faker = \Faker\Factory::create('id_ID');

    return [
      'brand_name' => $faker->company, // Nama brand diambil dari generator nama perusahaan
      'image' => $faker->imageUrl(640, 480, 'business', true, 'brand'), // Gambar URL acak
      'status' => true, // Status selalu true
    ];
  }
}
