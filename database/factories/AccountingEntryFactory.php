<?php

namespace Database\Factories;

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

        return [
            'description' => $faker->sentence, // Random sentence as description
            'amount' => $faker->randomFloat(2, 100, 10000), // Random amount between 100 and 10000 with 2 decimal places
            'type' => $faker->randomElement(['income', 'expense']), // Randomly choose between income and expense
            'entry_date' => $faker->dateTimeBetween('-1 years', 'now'), // Random entry date within the last year
        ];
    }
}
