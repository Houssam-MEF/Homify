<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($user)
            ->get(route('admin.products.index'));

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_access_admin_routes()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.products.index'));

        $response->assertOk();
    }

    /** @test */
    public function admin_can_view_products_index()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)
            ->get(route('admin.products.index'));

        $response->assertOk();
        $response->assertSee($product->name);
        $response->assertSee($category->name);
    }

    /** @test */
    public function admin_can_create_category()
    {
        $admin = User::factory()->admin()->create();

        $categoryData = [
            'name' => 'New Category',
            'slug' => 'new-category',
            'is_active' => true,
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), $categoryData);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_create_product()
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $productData = [
            'category_id' => $category->id,
            'name' => 'New Product',
            'slug' => 'new-product',
            'description' => 'Product description',
            'price_amount' => 2500, // $25.00
            'price_currency' => 'USD',
            'stock' => 10,
            'is_active' => true,
            'images' => [
                UploadedFile::fake()->image('product1.jpg'),
                UploadedFile::fake()->image('product2.jpg'),
            ],
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.products.store'), $productData);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'slug' => 'new-product',
            'price_amount' => 2500,
            'stock' => 10,
        ]);

        // Verify images were created
        $product = Product::where('name', 'New Product')->first();
        $this->assertEquals(2, $product->images()->count());

        // Verify files were stored
        Storage::disk('public')->assertExists($product->images->first()->path);
        Storage::disk('public')->assertExists($product->images->last()->path);
    }

    /** @test */
    public function admin_can_update_product()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $updateData = [
            'category_id' => $category->id,
            'name' => 'Updated Product Name',
            'description' => 'Updated description',
            'price_amount' => 3000,
            'price_currency' => 'USD',
            'stock' => 20,
            'is_active' => false,
        ];

        $response = $this->actingAs($admin)
            ->patch(route('admin.products.update', $product), $updateData);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals('Updated Product Name', $product->name);
        $this->assertEquals(3000, $product->price_amount);
        $this->assertEquals(20, $product->stock);
        $this->assertFalse($product->is_active);
    }

    /** @test */
    public function admin_can_toggle_product_status()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->active()->create();

        $response = $this->actingAs($admin)
            ->patch(route('admin.products.toggle-status', $product));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertFalse($product->is_active);
    }

    /** @test */
    public function admin_can_soft_delete_product()
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /** @test */
    public function admin_cannot_delete_product_with_orders()
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Create an order with this product
        $order = $user->orders()->create([
            'number' => 'TEST-ORDER-001',
            'status' => 'pending',
            'currency' => 'USD',
            'subtotal' => 1000,
            'tax_total' => 80,
            'shipping_total' => 0,
            'discount_total' => 0,
            'grand_total' => 1080,
            'billing_address_id' => 1,
            'shipping_address_id' => 1,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'name' => $product->name,
            'unit_amount' => $product->price_amount,
            'qty' => 1,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertNull($product->fresh()->deleted_at);
    }

    /** @test */
    public function admin_can_filter_products_by_category()
    {
        $admin = User::factory()->admin()->create();
        $category1 = Category::factory()->create(['name' => 'Electronics']);
        $category2 = Category::factory()->create(['name' => 'Clothing']);
        
        $product1 = Product::factory()->create(['category_id' => $category1->id, 'name' => 'Phone']);
        $product2 = Product::factory()->create(['category_id' => $category2->id, 'name' => 'Shirt']);

        $response = $this->actingAs($admin)
            ->get(route('admin.products.index', ['category_id' => $category1->id]));

        $response->assertOk();
        $response->assertSee('Phone');
        $response->assertDontSee('Shirt');
    }

    /** @test */
    public function admin_can_search_products()
    {
        $admin = User::factory()->admin()->create();
        $product1 = Product::factory()->create(['name' => 'iPhone 14']);
        $product2 = Product::factory()->create(['name' => 'Samsung Galaxy']);

        $response = $this->actingAs($admin)
            ->get(route('admin.products.index', ['search' => 'iPhone']));

        $response->assertOk();
        $response->assertSee('iPhone 14');
        $response->assertDontSee('Samsung Galaxy');
    }

    /** @test */
    public function admin_can_view_categories_index()
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Test Category']);

        $response = $this->actingAs($admin)
            ->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertSee('Test Category');
    }

    /** @test */
    public function category_creation_requires_valid_data()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => '', // Required field missing
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function product_creation_requires_valid_data()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => '', // Required field missing
                'category_id' => 999, // Non-existent category
                'price_amount' => -100, // Invalid price
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'name',
            'category_id',
            'price_amount',
        ]);
    }
}

