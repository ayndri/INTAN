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
            'brand_name' => $faker->company,
            'description' => $faker->sentence(6, true),
            'status' => true,
        ];
    }
}
