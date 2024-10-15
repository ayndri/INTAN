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
        $faker->unique(true); // Reset the unique generator

        return [
            'name' => $faker->words(2, true),
            'sku' => $faker->unique()->numerify('SKU###'), // Generate unique SKU
            'price' => $faker->numberBetween(10000, 1000000) . '.00',
            'cost' => $faker->numberBetween(10000, 1000000) . '.00',
            'stock' => $faker->numberBetween(1, 100),
            'unit_id' => Unit::inRandomOrder()->first()->id,
            'brand_id' => Brand::inRandomOrder()->first()->id,
            'status' => true,
            'product_image' => $faker->imageUrl(640, 480, 'technics', true),
        ];
    }
}
