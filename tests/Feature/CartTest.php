<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /** @test */
    public function guest_can_view_empty_cart()
    {
        $response = $this->get(route('cart.show'));

        $response->assertOk();
        $response->assertSee('Your cart is empty');
    }

    /** @test */
    public function guest_can_add_product_to_cart()
    {
        $product = Product::factory()->active()->inStock()->create();

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'qty' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify cart has the item
        $cartService = app(CartService::class);
        $cart = $cartService->getCurrentCart();
        
        $this->assertEquals(1, $cart->items->count());
        $this->assertEquals(2, $cart->items->first()->qty);
        $this->assertEquals($product->id, $cart->items->first()->product_id);
    }

    /** @test */
    public function guest_cannot_add_out_of_stock_product_to_cart()
    {
        $product = Product::factory()->active()->outOfStock()->create();

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'qty' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function guest_cannot_add_more_than_available_stock()
    {
        $product = Product::factory()->active()->create(['stock' => 5]);

        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'qty' => 10,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function guest_can_update_cart_item_quantity()
    {
        $product = Product::factory()->active()->inStock()->create();
        
        // Add to cart first
        $cartService = app(CartService::class);
        $cartItem = $cartService->addToCart($product, 2);

        $response = $this->patch(route('cart.update', $cartItem), [
            'qty' => 5,
        ]);

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('success');

        $cartItem->refresh();
        $this->assertEquals(5, $cartItem->qty);
    }

    /** @test */
    public function guest_can_remove_item_by_setting_quantity_to_zero()
    {
        $product = Product::factory()->active()->inStock()->create();
        
        // Add to cart first
        $cartService = app(CartService::class);
        $cartItem = $cartService->addToCart($product, 2);

        $response = $this->patch(route('cart.update', $cartItem), [
            'qty' => 0,
        ]);

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    /** @test */
    public function guest_can_remove_cart_item()
    {
        $product = Product::factory()->active()->inStock()->create();
        
        // Add to cart first
        $cartService = app(CartService::class);
        $cartItem = $cartService->addToCart($product, 2);

        $response = $this->delete(route('cart.remove', $cartItem));

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    /** @test */
    public function guest_can_clear_entire_cart()
    {
        $product1 = Product::factory()->active()->inStock()->create();
        $product2 = Product::factory()->active()->inStock()->create();
        
        // Add multiple items to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product1, 2);
        $cartService->addToCart($product2, 1);

        $cart = $cartService->getCurrentCart();
        $this->assertEquals(2, $cart->items->count());

        $response = $this->delete(route('cart.clear'));

        $response->assertRedirect(route('cart.show'));
        $response->assertSessionHas('success');

        $cart->refresh();
        $this->assertEquals(0, $cart->items->count());
    }

    /** @test */
    public function authenticated_user_cart_is_merged_on_login()
    {
        $user = User::factory()->create();
        $product = Product::factory()->active()->inStock()->create();

        // Add to cart as guest
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 2);

        // Login user
        $this->actingAs($user);

        // Manually trigger merge (in real app this happens in auth middleware)
        $cartService->mergeGuestCartIntoUserCart($user);

        // Check that user cart now has the item
        $userCart = $cartService->getCurrentCart($user);
        $this->assertEquals(1, $userCart->items->count());
        $this->assertEquals(2, $userCart->items->first()->qty);
        $this->assertEquals($product->id, $userCart->items->first()->product_id);
    }

    /** @test */
    public function cart_count_api_returns_correct_count()
    {
        $product = Product::factory()->active()->inStock()->create();
        
        // Add to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 3);

        $response = $this->get(route('cart.count'));

        $response->assertOk();
        $response->assertJson(['count' => 3]);
    }

    /** @test */
    public function cart_shows_validation_errors_for_invalid_products()
    {
        $product = Product::factory()->active()->create(['stock' => 5]);
        
        // Add to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 2);

        // Make product inactive
        $product->update(['is_active' => false]);

        $response = $this->get(route('cart.show'));

        $response->assertOk();
        $response->assertSee('Cart Issues');
        $response->assertSee('no longer active');
    }

    /** @test */
    public function cart_totals_are_calculated_correctly()
    {
        $product1 = Product::factory()->active()->inStock()->create(['price_amount' => 1000]); // $10.00
        $product2 = Product::factory()->active()->inStock()->create(['price_amount' => 2500]); // $25.00
        
        // Add to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product1, 2); // $20.00
        $cartService->addToCart($product2, 1); // $25.00
        // Subtotal: $45.00

        $cart = $cartService->getCurrentCart();
        $totals = $cartService->getCartTotals($cart);

        $this->assertEquals(4500, $totals['subtotal']); // $45.00 in cents
        $this->assertEquals(360, $totals['tax_total']); // 8% tax
        $this->assertEquals(500, $totals['shipping_total']); // $5.00 shipping (under $50)
        $this->assertEquals(5360, $totals['grand_total']); // $53.60
    }

    /** @test */
    public function cart_shows_free_shipping_over_fifty_dollars()
    {
        $product = Product::factory()->active()->inStock()->create(['price_amount' => 6000]); // $60.00
        
        // Add to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($product, 1);

        $cart = $cartService->getCurrentCart();
        $totals = $cartService->getCartTotals($cart);

        $this->assertEquals(0, $totals['shipping_total']); // Free shipping over $50
    }
}

