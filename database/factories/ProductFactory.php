<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'سماد NPK', 'مبيد حشري', 'بذور طماطم', 'بذور خيار', 'سماد عضوي',
            'مبيد فطري', 'بذور فلفل', 'علف حيواني', 'أدوية بيطرية', 'معدات ري'
        ];

        $units = ['كيس', 'كجم', 'لتر', 'قطعة', 'عبوة', 'صندوق'];

        $name = $this->faker->randomElement($products) . ' ' . $this->faker->numberBetween(1, 100);
        $slug = Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 99999);

        return [
            'category_id' => Category::factory(),
            'warehouse_id' => 1, // Default warehouse
            'profit_margin_tier_id' => 1, // Default tier
            'name' => $name,
            'slug' => $slug,
            'sku' => 'PRD-' . strtoupper($this->faker->unique()->bothify('??###')),
            'barcode' => $this->faker->optional()->ean13(),
            'description' => $this->faker->optional()->paragraph(),
            'short_description' => $this->faker->optional()->sentence(),
            'current_cost_price' => null, // Will be set after first purchase
            'current_base_price' => null,
            'base_unit' => $this->faker->randomElement($units),
            'stock_quantity' => $this->faker->numberBetween(0, 500),
            'min_stock_level' => $this->faker->numberBetween(5, 20),
            'max_stock_level' => $this->faker->numberBetween(100, 1000),
            'main_image' => null,
            'is_active' => $this->faker->boolean(85), // 85% active
            'is_featured' => $this->faker->boolean(20), // 20% featured
            'track_inventory' => true,
            'meta_title' => $this->faker->optional()->sentence(),
            'meta_description' => $this->faker->optional()->sentence(),
            'meta_keywords' => $this->faker->optional()->words(5, true),
        ];
    }

    /**
     * Indicate that the product has a price.
     */
    public function withPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_cost_price' => $this->faker->randomFloat(2, 10, 500),
            'current_base_price' => $this->faker->randomFloat(2, 15, 600),
        ]);
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

}
