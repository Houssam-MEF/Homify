<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create a few regular users
        User::factory(5)->create();

        // Create root categories
        $electronics = Category::factory()->root()->active()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $clothing = Category::factory()->root()->active()->create([
            'name' => 'Clothing',
            'slug' => 'clothing',
        ]);

        $home = Category::factory()->root()->active()->create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
        ]);

        $books = Category::factory()->root()->active()->create([
            'name' => 'Books',
            'slug' => 'books',
        ]);

        // Create subcategories for Electronics
        $smartphones = Category::factory()->child($electronics->id)->active()->create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
        ]);

        $laptops = Category::factory()->child($electronics->id)->active()->create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        $headphones = Category::factory()->child($electronics->id)->active()->create([
            'name' => 'Headphones',
            'slug' => 'headphones',
        ]);

        // Create subcategories for Clothing
        $mensClothing = Category::factory()->child($clothing->id)->active()->create([
            'name' => 'Men\'s Clothing',
            'slug' => 'mens-clothing',
        ]);

        $womensClothing = Category::factory()->child($clothing->id)->active()->create([
            'name' => 'Women\'s Clothing',
            'slug' => 'womens-clothing',
        ]);

        // Create subcategories for Home & Garden
        $furniture = Category::factory()->child($home->id)->active()->create([
            'name' => 'Furniture',
            'slug' => 'furniture',
        ]);

        $kitchenware = Category::factory()->child($home->id)->active()->create([
            'name' => 'Kitchenware',
            'slug' => 'kitchenware',
        ]);

        // Collect all categories for product assignment
        $allCategories = collect([
            $smartphones, $laptops, $headphones, 
            $mensClothing, $womensClothing, 
            $furniture, $kitchenware, $books
        ]);

        // Create products for each category
        $allCategories->each(function ($category) {
            $productCount = rand(3, 8);
            
            for ($i = 0; $i < $productCount; $i++) {
                $product = Product::factory()
                    ->active()
                    ->inStock()
                    ->create([
                        'category_id' => $category->id,
                        'name' => $this->getProductNameForCategory($category->name),
                    ]);

                // Add 1-3 images per product
                $imageCount = rand(1, 3);
                for ($j = 0; $j < $imageCount; $j++) {
                    ProductImage::factory()->create([
                        'product_id' => $product->id,
                        'sort' => $j,
                        'path' => "products/placeholder-600x600-{$product->id}-{$j}.jpg",
                        'alt' => "{$product->name} image " . ($j + 1),
                    ]);
                }
            }
        });

        // Create some out-of-stock products
        Product::factory(5)
            ->outOfStock()
            ->active()
            ->create([
                'category_id' => $allCategories->random()->id,
            ]);

        // Create some inactive products
        Product::factory(3)
            ->inactive()
            ->inStock()
            ->create([
                'category_id' => $allCategories->random()->id,
            ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin user: admin@example.com / password');
    }

    /**
     * Get a realistic product name for the given category.
     *
     * @param string $categoryName
     * @return string
     */
    private function getProductNameForCategory(string $categoryName): string
    {
        $productNames = [
            'Smartphones' => [
                'iPhone 14 Pro Max',
                'Samsung Galaxy S23',
                'Google Pixel 7',
                'OnePlus 11',
                'Xiaomi 13 Pro',
                'iPhone 13',
                'Samsung Galaxy A54',
                'Google Pixel 6a',
            ],
            'Laptops' => [
                'MacBook Pro 16"',
                'Dell XPS 13',
                'HP Spectre x360',
                'Lenovo ThinkPad X1',
                'ASUS ZenBook',
                'Microsoft Surface Laptop',
                'Acer Swift 3',
                'HP Pavilion',
            ],
            'Headphones' => [
                'Sony WH-1000XM5',
                'Bose QuietComfort 45',
                'Apple AirPods Pro',
                'Sennheiser HD 660S',
                'Audio-Technica ATH-M50x',
                'Beats Studio3',
                'JBL Live 650BTNC',
                'Jabra Elite 85h',
            ],
            'Men\'s Clothing' => [
                'Classic Fit Polo Shirt',
                'Slim Fit Chino Pants',
                'Cotton Crew Neck T-Shirt',
                'Denim Jacket',
                'Wool Blend Sweater',
                'Oxford Button-Down Shirt',
                'Athletic Joggers',
                'Leather Belt',
            ],
            'Women\'s Clothing' => [
                'Floral Summer Dress',
                'High-Waisted Jeans',
                'Silk Blouse',
                'Cashmere Cardigan',
                'Little Black Dress',
                'Yoga Leggings',
                'Denim Jacket',
                'Ankle Boots',
            ],
            'Furniture' => [
                'Modern Sofa',
                'Oak Dining Table',
                'Ergonomic Office Chair',
                'Queen Size Bed Frame',
                'Coffee Table',
                'Bookshelf',
                'Nightstand',
                'Accent Chair',
            ],
            'Kitchenware' => [
                'Stainless Steel Cookware Set',
                'Non-Stick Frying Pan',
                'Stand Mixer',
                'Coffee Maker',
                'Knife Set',
                'Cutting Board',
                'Food Processor',
                'Blender',
            ],
            'Books' => [
                'The Great Gatsby',
                'To Kill a Mockingbird',
                '1984',
                'Pride and Prejudice',
                'The Catcher in the Rye',
                'Lord of the Flies',
                'The Hobbit',
                'Harry Potter Series',
            ],
        ];

        $names = $productNames[$categoryName] ?? [
            'Premium Product',
            'Quality Item',
            'Best Seller',
            'Top Rated Product',
            'Customer Favorite',
        ];

        return $names[array_rand($names)];
    }
}
