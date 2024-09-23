<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Unit::class;
    public function definition()
    {
        $unitNames = ['Pcs', 'Box', 'Kg', 'Liter', 'Dozen', 'Pack', 'Bottle', 'Carton'];

        return [
            'unit_name' => $this->faker->randomElement($unitNames),
            'description' => $this->faker->sentence(6, true),
            'status' => true,
        ];
    }
}
