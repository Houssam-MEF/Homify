<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create the current cart.
     *
     * @param User|null $user
     * @return Cart
     */
    public function getCurrentCart(?User $user = null): Cart
    {
        $user = $user ?? auth()->user();
        $sessionId = Session::getId();

        if ($user) {
            // For authenticated users, find or create a user cart
            $cart = Cart::where('user_id', $user->id)->first();
            
            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'currency' => 'USD',
                ]);
            }
            
            return $cart;
        }

        // For guests, use session-based cart
        $cart = Cart::where('session_id', $sessionId)->first();
        
        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'currency' => 'USD',
            ]);
        }
        
        return $cart;
    }

    /**
     * Add a product to the cart.
     *
     * @param Product $product
     * @param int $quantity
     * @param User|null $user
     * @return CartItem
     * @throws \Exception
     */
    public function addToCart(Product $product, int $quantity, ?User $user = null): CartItem
    {
        if (!$product->isAvailable($quantity)) {
            throw new \Exception('Product is not available or insufficient stock.');
        }

        $cart = $this->getCurrentCart($user);

        return DB::transaction(function () use ($cart, $product, $quantity) {
            $existingItem = $cart->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                $newQuantity = $existingItem->qty + $quantity;
                
                if (!$product->inStock($newQuantity)) {
                    throw new \Exception('Not enough stock available. Available: ' . $product->stock);
                }

                $existingItem->update([
                    'qty' => $newQuantity,
                    'unit_amount' => $product->price_amount, // Update price snapshot
                    'name' => $product->name, // Update name snapshot
                ]);

                return $existingItem;
            }

            return CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'qty' => $quantity,
                'unit_amount' => $product->price_amount,
                'name' => $product->name,
            ]);
        });
    }

    /**
     * Update cart item quantity.
     *
     * @param CartItem $cartItem
     * @param int $quantity
     * @return CartItem|null
     * @throws \Exception
     */
    public function updateCartItem(CartItem $cartItem, int $quantity): ?CartItem
    {
        if ($quantity <= 0) {
            $cartItem->delete();
            return null;
        }

        if ($cartItem->product && !$cartItem->product->inStock($quantity)) {
            throw new \Exception('Not enough stock available. Available: ' . $cartItem->product->stock);
        }

        $cartItem->update([
            'qty' => $quantity,
            'unit_amount' => $cartItem->product ? $cartItem->product->price_amount : $cartItem->unit_amount,
            'name' => $cartItem->product ? $cartItem->product->name : $cartItem->name,
        ]);

        return $cartItem;
    }

    /**
     * Remove item from cart.
     *
     * @param CartItem $cartItem
     * @return void
     */
    public function removeFromCart(CartItem $cartItem): void
    {
        $cartItem->delete();
    }

    /**
     * Clear the entire cart.
     *
     * @param User|null $user
     * @return void
     */
    public function clearCart(?User $user = null): void
    {
        $cart = $this->getCurrentCart($user);
        $cart->clear();
    }

    /**
     * Get cart totals.
     *
     * @param Cart $cart
     * @return array
     */
    public function getCartTotals(Cart $cart): array
    {
        $subtotal = $cart->subtotal;
        $taxTotal = $this->calculateTax($subtotal);
        $shippingTotal = $this->calculateShipping($subtotal);
        $discountTotal = 0; // For future implementation
        $grandTotal = $subtotal + $taxTotal + $shippingTotal - $discountTotal;

        return [
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'shipping_total' => $shippingTotal,
            'discount_total' => $discountTotal,
            'grand_total' => $grandTotal,
            'formatted_subtotal' => number_format($subtotal / 100, 2),
            'formatted_tax_total' => number_format($taxTotal / 100, 2),
            'formatted_shipping_total' => number_format($shippingTotal / 100, 2),
            'formatted_discount_total' => number_format($discountTotal / 100, 2),
            'formatted_grand_total' => number_format($grandTotal / 100, 2),
        ];
    }

    /**
     * Calculate tax for the given subtotal.
     *
     * @param int $subtotal
     * @return int
     */
    protected function calculateTax(int $subtotal): int
    {
        // Simple 8% tax rate - in real app this would be more complex
        return (int) round($subtotal * 0.08);
    }

    /**
     * Calculate shipping for the given subtotal.
     *
     * @param int $subtotal
     * @return int
     */
    protected function calculateShipping(int $subtotal): int
    {
        // Free shipping over $50, otherwise $5
        if ($subtotal >= 5000) { // $50.00
            return 0;
        }
        
        return 500; // $5.00
    }

    /**
     * Merge guest cart into user cart after login.
     *
     * @param User $user
     * @param string|null $sessionId
     * @return void
     */
    public function mergeGuestCartIntoUserCart(User $user, ?string $sessionId = null): void
    {
        $sessionId = $sessionId ?? Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)->first();
        
        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = $this->getCurrentCart($user);

        DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $guestItem) {
                $existingUserItem = $userCart->items()
                    ->where('product_id', $guestItem->product_id)
                    ->first();

                if ($existingUserItem) {
                    // Merge quantities
                    $newQuantity = $existingUserItem->qty + $guestItem->qty;
                    $product = $existingUserItem->product;
                    
                    if ($product && $product->inStock($newQuantity)) {
                        $existingUserItem->update([
                            'qty' => $newQuantity,
                            'unit_amount' => $product->price_amount,
                            'name' => $product->name,
                        ]);
                    }
                } else {
                    // Move item to user cart
                    $guestItem->update(['cart_id' => $userCart->id]);
                }
            }

            // Delete the guest cart
            $guestCart->delete();
        });
    }

    /**
     * Get cart item count.
     *
     * @param User|null $user
     * @return int
     */
    public function getCartItemCount(?User $user = null): int
    {
        $cart = $this->getCurrentCart($user);
        return $cart->total_items;
    }

    /**
     * Check if cart is valid for checkout.
     *
     * @param Cart $cart
     * @return array
     */
    public function validateCartForCheckout(Cart $cart): array
    {
        $errors = [];
        
        if ($cart->isEmpty()) {
            $errors[] = 'Cart is empty.';
            return $errors;
        }

        foreach ($cart->items as $item) {
            if (!$item->product) {
                $errors[] = "Product '{$item->name}' is no longer available.";
                continue;
            }

            if (!$item->product->is_active) {
                $errors[] = "Product '{$item->product->name}' is no longer active.";
            }

            if (!$item->product->inStock($item->qty)) {
                $errors[] = "Product '{$item->product->name}' has insufficient stock. Available: {$item->product->stock}";
            }
        }

        return $errors;
    }
}
