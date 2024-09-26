<?php

namespace Database\Factories;

use App\Models\Brand;
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
        $faker = \Faker\Factory::create('id_ID');

        return [
            'name' => $faker->words(2, true),
            'sku' => $faker->unique()->numerify('SKU###'),
            'price' => $faker->numberBetween(10000, 1000000) . '.00',
            'cost' => $faker->numberBetween(10000, 1000000) . '.00', // biaya acak antara 10.000 dan 1.000.000
            'stock' => $faker->numberBetween(1, 100),
            'unit_id' => Unit::inRandomOrder()->first()->id,  // Pilih unit secara acak dari yang sudah ada
            'brand_id' => Brand::inRandomOrder()->first()->id,  // Pilih brand secara acak dari yang sudah ada
            'status' => true,
            'product_image' => $faker->imageUrl(640, 480, 'technics', true),
        ];
    }
}
