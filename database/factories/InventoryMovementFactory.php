<?php

namespace Database\Factories;

use App\Models\InventoryMovement;
use App\Models\InventoryMovements;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
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
        $type = $this->faker->randomElement(['in', 'out']); // 'in' for incoming (purchase), 'out' for outgoing (sale)

        // If the type is 'in', we link it to a purchase. If 'out', we link it to a sale.
        $sale = $type === 'out' ? Sale::inRandomOrder()->first() : null;
        $purchase = $type === 'in' ? Purchase::inRandomOrder()->first() : null;

        // Assign the IDs only if the records are found
        $saleId = $sale ? $sale->id : null;
        $purchaseId = $purchase ? $purchase->id : null;

        return [
            'product_id' => Product::inRandomOrder()->first()->id, // Random product
            'sale_id' => $saleId, // Set sale_id only if the type is 'out'
            'purchase_id' => $purchaseId, // Set purchase_id only if the type is 'in'
            'type' => $type, // 'in' or 'out'
            'quantity' => $this->faker->numberBetween(1, 100), // Random quantity
            'transaction_date' => $this->faker->dateTimeThisYear(), // Random transaction date within this year
            'description' => $this->faker->optional()->sentence(), // Optional description
        ];
    }
}
