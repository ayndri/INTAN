<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountingEntry>
 */
class AccountingEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('id_ID');

        $type = $faker->randomElement(['income', 'expense']); // Randomly choose between income and expense

        // Set sale_id or purchase_id based on the type, with null checks
        $sale = $type === 'income' ? Sale::inRandomOrder()->first() : null;
        $purchase = $type === 'expense' ? Purchase::inRandomOrder()->first() : null;

        // Assign the IDs only if the records are found
        $saleId = $sale ? $sale->id : null;
        $purchaseId = $purchase ? $purchase->id : null;

        return [
            'description' => $faker->sentence, // Random sentence as description
            'amount' => $faker->numberBetween(10000, 1000000) . '.00',
            'type' => $type, // Set type as either 'income' or 'expense'
            'entry_date' => $faker->dateTimeBetween('-1 years', 'now'), // Random entry date within the last year
            'sale_id' => $saleId, // Set sale_id if type is 'income', otherwise null
            'purchase_id' => $purchaseId, // Set purchase_id if type is 'expense', otherwise null
        ];
    }
}
