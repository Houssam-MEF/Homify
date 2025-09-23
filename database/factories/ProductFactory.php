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
    public function definition()
    {
        $name = fake()->words(rand(2, 4), true);
        $price = fake()->randomFloat(2, 5, 500); // $5 to $500
        
        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'price_amount' => (int) ($price * 100), // Convert to cents
            'price_currency' => 'USD',
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => fake()->boolean(85), // 85% chance of being active
        ];
    }

    /**
     * Create a product with high stock.
     *
     * @return static
     */
    public function inStock()
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(10, 100),
        ]);
    }

    /**
     * Create a product that's out of stock.
     *
     * @return static
     */
    public function outOfStock()
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Create an active product.
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
     * Create an inactive product.
     *
     * @return static
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a product with low stock.
     *
     * @return static
     */
    public function lowStock()
    {
        return $this->state(fn (array $attributes) => [
            'stock' => fake()->numberBetween(1, 5),
        ]);
    }

    /**
     * Create an expensive product.
     *
     * @return static
     */
    public function expensive()
    {
        $price = fake()->randomFloat(2, 200, 1000);
        
        return $this->state(fn (array $attributes) => [
            'price_amount' => (int) ($price * 100),
        ]);
    }

    /**
     * Create a cheap product.
     *
     * @return static
     */
    public function cheap()
    {
        $price = fake()->randomFloat(2, 1, 50);
        
        return $this->state(fn (array $attributes) => [
            'price_amount' => (int) ($price * 100),
        ]);
    }
}

