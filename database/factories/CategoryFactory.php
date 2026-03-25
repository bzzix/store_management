<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'أسمدة', 'مبيدات', 'بذور', 'أدوات زراعية', 'معدات ري',
            'أعلاف', 'منتجات عضوية', 'مستلزمات حيوانية', 'أدوية بيطرية', 'معدات حصاد'
        ];

        $name = $this->faker->randomElement($categories);
        $slug = Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999);

        return [
            'parent_id' => null, // Will be set manually for subcategories
            'name' => $name,
            'slug' => $slug,
            'description' => $this->faker->optional()->sentence(),
            'image' => null,
            'is_active' => $this->faker->boolean(90), // 90% active
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the category is a subcategory.
     */
    public function subcategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Category::factory(),
        ]);
    }

}
