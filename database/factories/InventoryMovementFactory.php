<?php

namespace Database\Factories;

use App\Models\InventoryMovement;
use App\Models\InventoryMovements;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryMovement>
 */
class InventoryMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = InventoryMovement::class;
    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'type' => $this->faker->randomElement(['in', 'out']),
            'quantity' => $this->faker->numberBetween(1, 100),
            'transaction_date' => $this->faker->dateTimeThisYear(),
            'description' => $this->faker->sentence(),
        ];
    }
}
