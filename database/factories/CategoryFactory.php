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
    public function definition()
    {
        $name = fake()->words(rand(1, 2), true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Create a root category (no parent).
     *
     * @return static
     */
    public function root()
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    /**
     * Create a child category.
     *
     * @param int $parentId
     * @return static
     */
    public function child($parentId = null)
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId ?? Category::factory()->root()->create()->id,
        ]);
    }

    /**
     * Create an active category.
     *
     * @return static
     */
    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive category.
     *
     * @return static
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

