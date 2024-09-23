<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Customer::class;
    public function definition()
    {
        $faker = \Faker\Factory::create('id_ID');

        return [
            'name' => $faker->name(),         // Menghasilkan nama acak
            'email' => $faker->unique()->safeEmail(), // Menghasilkan email unik acak
            'phone' => $faker->phoneNumber(),
        ];
    }
}
