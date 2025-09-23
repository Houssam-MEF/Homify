<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Generate placeholder image path
        $width = 600;
        $height = 600;
        $path = "products/placeholder-{$width}x{$height}-" . fake()->uuid() . '.jpg';
        
        return [
            'product_id' => Product::factory(),
            'path' => $path,
            'alt' => fake()->sentence(3),
            'sort' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Create the first image (sort = 0).
     *
     * @return static
     */
    public function primary()
    {
        return $this->state(fn (array $attributes) => [
            'sort' => 0,
        ]);
    }

    /**
     * Create a secondary image.
     *
     * @param int $sort
     * @return static
     */
    public function secondary($sort = 1)
    {
        return $this->state(fn (array $attributes) => [
            'sort' => $sort,
        ]);
    }
}

