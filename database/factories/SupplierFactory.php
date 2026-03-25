<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companies = [
            'شركة الزراعة الحديثة', 'مؤسسة الأسمدة المتقدمة', 'شركة البذور الذهبية',
            'مؤسسة المبيدات الزراعية', 'شركة الأعلاف الوطنية', 'مؤسسة المعدات الزراعية'
        ];

        $companyName = $this->faker->randomElement($companies);

        return [
            'name' => $this->faker->name(),
            'company_name' => $companyName,
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'phone_2' => $this->faker->optional()->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country' => 'مصر',
            'tax_number' => $this->faker->optional()->numerify('##########'),
            'credit_limit' => $this->faker->randomFloat(2, 10000, 100000),
            'current_balance' => 0, // Start with zero balance
            'notes' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(90),
        ];
    }

    /**
     * Indicate that the supplier has a balance.
     */
    public function withBalance(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_balance' => $this->faker->randomFloat(2, 1000, 50000),
        ]);
    }

}
