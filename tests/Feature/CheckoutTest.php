<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function guest_cannot_access_checkout()
    {
        $response = $this->get(route('checkout.start'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_cannot_checkout_with_empty_cart()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('checkout.start'));

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function user_can_access_checkout_with_valid_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create();

        // Add product to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 1, $user);

        $response = $this->actingAs($user)
            ->get(route('checkout.start'));

        $response->assertOk();
        $response->assertSee('Billing Address');
        $response->assertSee('Shipping Address');
    }

    /** @test */
    public function user_cannot_checkout_with_invalid_cart_items()
    {
        $user = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create();

        // Add product to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 1, $user);

        // Make product inactive
        $product->update(['is_active' => false]);

        $response = $this->actingAs($user)
            ->get(route('checkout.start'));

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function user_can_place_order_with_valid_data()
    {
        $user = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create(['price_amount' => 2000]); // $20.00

        // Add product to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 2, $user);

        $addressData = [
            'billing_name' => 'John Doe',
            'billing_line1' => '123 Main St',
            'billing_city' => 'New York',
            'billing_region' => 'NY',
            'billing_postal_code' => '10001',
            'billing_country' => 'US',
            'same_as_billing' => true,
        ];

        $response = $this->actingAs($user)
            ->post(route('checkout.place'), $addressData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify order was created
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
            'subtotal' => 4000, // $40.00 in cents
        ]);

        // Verify order items were created
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'qty' => 2,
            'unit_amount' => 2000,
        ]);

        // Verify addresses were created
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'type' => 'billing',
            'name' => 'John Doe',
            'line1' => '123 Main St',
        ]);

        // Verify cart was cleared
        $cart = $cartService->getCurrentCart($user);
        $this->assertTrue($cart->isEmpty());
    }

    /** @test */
    public function user_can_place_order_with_different_shipping_address()
    {
        $user = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create();

        // Add product to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 1, $user);

        $addressData = [
            'billing_name' => 'John Doe',
            'billing_line1' => '123 Main St',
            'billing_city' => 'New York',
            'billing_region' => 'NY',
            'billing_postal_code' => '10001',
            'billing_country' => 'US',
            'same_as_billing' => false,
            'shipping_name' => 'Jane Doe',
            'shipping_line1' => '456 Oak Ave',
            'shipping_city' => 'Los Angeles',
            'shipping_region' => 'CA',
            'shipping_postal_code' => '90001',
            'shipping_country' => 'US',
        ];

        $response = $this->actingAs($user)
            ->post(route('checkout.place'), $addressData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify both addresses were created
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'type' => 'billing',
            'name' => 'John Doe',
            'city' => 'New York',
        ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'type' => 'shipping',
            'name' => 'Jane Doe',
            'city' => 'Los Angeles',
        ]);
    }

    /** @test */
    public function checkout_requires_valid_address_data()
    {
        $user = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create();

        // Add product to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 1, $user);

        $response = $this->actingAs($user)
            ->post(route('checkout.place'), [
                // Missing required fields
                'billing_name' => '',
                'billing_line1' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'billing_name',
            'billing_line1',
            'billing_city',
            'billing_postal_code',
            'billing_country',
        ]);
    }

    /** @test */
    public function checkout_validates_shipping_address_when_different_from_billing()
    {
        $user = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create();

        // Add product to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 1, $user);

        $addressData = [
            'billing_name' => 'John Doe',
            'billing_line1' => '123 Main St',
            'billing_city' => 'New York',
            'billing_region' => 'NY',
            'billing_postal_code' => '10001',
            'billing_country' => 'US',
            'same_as_billing' => false,
            // Missing shipping address fields
            'shipping_name' => '',
            'shipping_line1' => '',
        ];

        $response = $this->actingAs($user)
            ->post(route('checkout.place'), $addressData);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'shipping_name',
            'shipping_line1',
            'shipping_city',
            'shipping_postal_code',
            'shipping_country',
        ]);
    }

    /** @test */
    public function user_can_access_payment_page_for_their_order()
    {
        $user = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create();

        // Add product to cart and place order
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 1, $user);

        $addressData = [
            'billing_name' => 'John Doe',
            'billing_line1' => '123 Main St',
            'billing_city' => 'New York',
            'billing_region' => 'NY',
            'billing_postal_code' => '10001',
            'billing_country' => 'US',
            'same_as_billing' => true,
        ];

        $this->actingAs($user)
            ->post(route('checkout.place'), $addressData);

        $order = $user->orders()->first();

        $response = $this->actingAs($user)
            ->get(route('checkout.payment', $order));

        $response->assertOk();
        $response->assertSee('Complete Your Payment');
        $response->assertSee('Payment Details');
    }

    /** @test */
    public function user_cannot_access_payment_page_for_other_users_order()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create();

        // Create order for user1
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 1, $user1);

        $addressData = [
            'billing_name' => 'John Doe',
            'billing_line1' => '123 Main St',
            'billing_city' => 'New York',
            'billing_region' => 'NY',
            'billing_postal_code' => '10001',
            'billing_country' => 'US',
            'same_as_billing' => true,
        ];

        $this->actingAs($user1)
            ->post(route('checkout.place'), $addressData);

        $order = $user1->orders()->first();

        // Try to access as user2
        $response = $this->actingAs($user2)
            ->get(route('checkout.payment', $order));

        $response->assertForbidden();
    }

    /** @test */
    public function order_totals_are_calculated_correctly()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->active()->inStock()->create(['price_amount' => 1500]); // $15.00
        $product2 = Product::factory()->active()->inStock()->create(['price_amount' => 2500]); // $25.00

        // Add products to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product1, 2, $user); // $30.00
        $cartService->addToCart($product2, 1, $user); // $25.00
        // Subtotal: $55.00

        $addressData = [
            'billing_name' => 'John Doe',
            'billing_line1' => '123 Main St',
            'billing_city' => 'New York',
            'billing_region' => 'NY',
            'billing_postal_code' => '10001',
            'billing_country' => 'US',
            'same_as_billing' => true,
        ];

        $this->actingAs($user)
            ->post(route('checkout.place'), $addressData);

        $order = $user->orders()->first();

        $this->assertEquals(5500, $order->subtotal); // $55.00
        $this->assertEquals(440, $order->tax_total); // 8% tax
        $this->assertEquals(0, $order->shipping_total); // Free shipping over $50
        $this->assertEquals(5940, $order->grand_total); // $59.40
    }
}

