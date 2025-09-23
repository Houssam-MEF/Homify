<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * The cart service instance.
     */
    protected CartService $cartService;

    /**
     * Create a new controller instance.
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the cart.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $cart = $this->cartService->getCurrentCart();
        $totals = $this->cartService->getCartTotals($cart);
        $validationErrors = $this->cartService->validateCartForCheckout($cart);

        return view('cart.show', compact('cart', 'totals', 'validationErrors'));
    }

    /**
     * Add a product to the cart.
     *
     * @param AddToCartRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(AddToCartRequest $request)
    {
        try {
            $product = Product::findOrFail($request->product_id);
            $cartItem = $this->cartService->addToCart($product, $request->qty);

            return redirect()
                ->back()
                ->with('success', "Added {$product->name} to cart successfully!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update cart item quantity.
     *
     * @param Request $request
     * @param CartItem $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'qty' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $updatedItem = $this->cartService->updateCartItem($item, $request->qty);

            if ($updatedItem) {
                $message = 'Cart updated successfully!';
            } else {
                $message = 'Item removed from cart.';
            }

            return redirect()
                ->route('cart.show')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('cart.show')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove item from cart.
     *
     * @param CartItem $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(CartItem $item)
    {
        try {
            $productName = $item->name;
            $this->cartService->removeFromCart($item);

            return redirect()
                ->route('cart.show')
                ->with('success', "Removed {$productName} from cart.");
        } catch (\Exception $e) {
            return redirect()
                ->route('cart.show')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Clear the entire cart.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        try {
            $this->cartService->clearCart();

            return redirect()
                ->route('cart.show')
                ->with('success', 'Cart cleared successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('cart.show')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get cart count for AJAX requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $count = $this->cartService->getCartItemCount();

        return response()->json(['count' => $count]);
    }

    /**
     * Merge guest cart into user cart after login.
     * This method is called by the LoginController after successful authentication.
     *
     * @return void
     */
    public function mergeOnLogin()
    {
        if (auth()->check()) {
            $this->cartService->mergeGuestCartIntoUserCart(auth()->user());
        }
    }
}

