<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Sale::class;
    public function definition()
    {
        $orderType = $this->faker->randomElement(['online', 'offline']);
        $shippingCost = $orderType === 'online' ? $this->faker->numberBetween(10000, 1000000) . '.00' : 0;

        return [
            'product_id' => Product::inRandomOrder()->first()->id, // Select a random product
            'customer_id' => Customer::inRandomOrder()->first()->id, // Select a random customer
            'quantity' => $this->faker->numberBetween(1, 100), // Random quantity
            'selling_price' => $this->faker->numberBetween(10000, 1000000) . '.00',
            'total' => 0, // Total will be calculated later
            'sale_date' => $this->faker->dateTimeThisYear(), // Sale date in the current year
            'status' => $this->faker->randomElement(['pending', 'in-progress', 'completed', 'cancelled']), // Random status
            'order_type' => $orderType, // Random order type (online or offline)
            'shipping_cost' => $shippingCost, // Shipping cost for online orders, 0 for offline orders
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Sale $sale) {
            // You can perform additional actions here after making the sale object
        })->afterCreating(function (Sale $sale) {
            // Calculate the total and save it after the sale has been created
            $sale->total = ($sale->selling_price * $sale->quantity) + $sale->shipping_cost;
            $sale->save();
        });
    }
}
