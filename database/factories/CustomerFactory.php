<?php

namespace Database\Factories;

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
    public function definition(): array
    {
        $customerType = $this->faker->randomElement(['individual', 'company']);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->boolean(70) ? $this->faker->unique()->safeEmail() : null,
            'phone' => $this->faker->phoneNumber(),
            'phone_2' => $this->faker->optional()->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country' => 'السعودية',
            'tax_number' => $customerType === 'company' ? $this->faker->numerify('##########') : null,
            'credit_limit' => $this->faker->randomFloat(2, 5000, 50000),
            'current_balance' => 0,
            'customer_type' => $customerType,
            'company_name' => $customerType === 'company' ? $this->faker->company() : null,
            'notes' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(90),
        ];
    }

    /**
     * Indicate that the customer is an individual.
     */
    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'individual',
            'company_name' => null,
            'tax_number' => null,
        ]);
    }

    /**
     * Indicate that the customer is a company.
     */
    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_type' => 'company',
            'company_name' => $this->faker->company(),
            'tax_number' => $this->faker->numerify('##########'),
        ]);
    }

    /**
     * Indicate that the customer has a balance.
     */
    public function withBalance(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_balance' => $this->faker->randomFloat(2, 500, 20000),
        ]);
    }

}
