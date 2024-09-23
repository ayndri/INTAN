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
        $quantity = $this->faker->numberBetween(1, 100); // Jumlah barang yang dibeli
        $costPrice = $this->faker->randomFloat(2, 10, 1000); // Harga beli acak
        $total = $quantity * $costPrice; // Menghitung total harga beli

        return [
            'product_id' => Product::factory(), // Menghasilkan product_id acak
            'supplier_id' => Supplier::factory(), // Menghasilkan supplier_id acak
            'quantity' => $quantity,
            'cost_price' => $costPrice,
            'total' => $total,
            'purchase_date' => $this->faker->dateTimeThisYear(), // Tanggal pembelian acak
        ];
    }
}
