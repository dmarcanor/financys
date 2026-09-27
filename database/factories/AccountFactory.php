<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'user_id' => fake()->uuid(),
            'name' => fake()->name(),
            'code' => fake()->word(),
            'balance' => (string) fake()->randomFloat(nbMaxDecimals: 8, min: 0),
            'currency' => fake()->randomElement(['bs', 'usd']),
        ];
    }
}
