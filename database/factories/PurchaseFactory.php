<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Purchase::class;
    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 100); // Quantity of products purchased
        $costPrice = $this->faker->randomFloat(2, 10, 1000); // Random purchase price
        $total = $quantity * $costPrice; // Calculate total purchase price

        return [
            'product_id' => Product::inRandomOrder()->first()->id, // Generate a random product_id
            'supplier_id' => Supplier::inRandomOrder()->first()->id, // Generate a random supplier_id
            'quantity' => $quantity,
            'cost_price' => $costPrice,
            'total' => $total, // Calculated total
            'purchase_date' => $this->faker->dateTimeThisYear(), // Random purchase date within this year
        ];
    }
}
