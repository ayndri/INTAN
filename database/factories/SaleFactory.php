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
        $shippingCost = $orderType === 'online' ? $this->faker->randomFloat(2, 10, 100) : 0;

        return [
            'product_id' => Product::inRandomOrder()->first()->id, // Buat produk acak
            'customer_id' => Customer::inRandomOrder()->first()->id, // Buat customer acak
            'quantity' => $this->faker->numberBetween(1, 100), // Jumlah acak
            'selling_price' => $this->faker->randomFloat(2, 100, 1000), // Harga jual acak
            'total' => 0, // Total akan dihitung nanti
            'sale_date' => $this->faker->dateTimeThisYear(), // Tanggal acak dalam tahun ini
            'status' => $this->faker->randomElement(['pending', 'in-progress', 'completed', 'cancelled']), // Status acak
            'order_type' => $orderType, // Order type acak (online atau offline)
            'shipping_cost' => $shippingCost, // Ongkir jika online, 0 jika offline
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Sale $sale) {
            // Jika diperlukan sesuatu saat pembuatan tapi belum di-save
        })->afterCreating(function (Sale $sale) {
            // Hitung total dan simpan setelah penjualan dibuat
            $sale->total = ($sale->selling_price * $sale->quantity) + $sale->shipping_cost;
            $sale->save();
        });
    }
}
