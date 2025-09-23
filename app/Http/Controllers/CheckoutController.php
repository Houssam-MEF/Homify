<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutAddressRequest;
use App\Http\Requests\CheckoutPaymentRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
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
        $this->middleware('auth');
    }

    /**
     * Start the checkout process - show address form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function start()
    {
        $cart = $this->cartService->getCurrentCart(auth()->user());
        $validationErrors = $this->cartService->validateCartForCheckout($cart);

        if (!empty($validationErrors)) {
            return redirect()
                ->route('cart.show')
                ->withErrors($validationErrors);
        }

        $totals = $this->cartService->getCartTotals($cart);

        // Get user's existing delivery addresses
        $deliveryAddresses = auth()->user()->addresses()->where('type', 'shipping')->get();

        return view('checkout.addresses', compact('cart', 'totals', 'deliveryAddresses'));
    }

    /**
     * Process address form and redirect to payment.
     *
     * @param CheckoutAddressRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function placeOrder(CheckoutAddressRequest $request)
    {
        $user = auth()->user();
        $cart = $this->cartService->getCurrentCart($user);
        
        // Validate cart again
        $validationErrors = $this->cartService->validateCartForCheckout($cart);
        if (!empty($validationErrors)) {
            return redirect()
                ->route('cart.show')
                ->withErrors($validationErrors);
        }

        try {
            // Create or get delivery address
            $deliveryAddress = $this->createOrGetAddress($user, $request->getDeliveryAddressData());
            
            // Store address in session for payment page
            session([
                'checkout.delivery_address_id' => $deliveryAddress->id,
            ]);
            
            // Redirect to payment page
            return redirect()->route('checkout.payment');
            
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Erreur lors de la création des adresses: ' . $e->getMessage()]);
        }
    }

    /**
     * Show payment page.
     *
     * @return \Illuminate\View\View
     */
    public function payment()
    {
        $user = auth()->user();
        $cart = $this->cartService->getCurrentCart($user);
        
        // Validate cart
        $validationErrors = $this->cartService->validateCartForCheckout($cart);
        if (!empty($validationErrors)) {
            return redirect()
                ->route('cart.show')
                ->withErrors($validationErrors);
        }

        // Get delivery address from session
        $deliveryAddressId = session('checkout.delivery_address_id');
        
        if (!$deliveryAddressId) {
            return redirect()->route('checkout.start');
        }
        
        $deliveryAddress = Address::findOrFail($deliveryAddressId);
        $totals = $this->cartService->getCartTotals($cart);
        
        return view('checkout.payment', compact('cart', 'totals', 'deliveryAddress'));
    }

    /**
     * Process payment and create order.
     *
     * @param CheckoutPaymentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(CheckoutPaymentRequest $request)
    {
        $user = auth()->user();
        $cart = $this->cartService->getCurrentCart($user);
        
        // Validate cart again
        $validationErrors = $this->cartService->validateCartForCheckout($cart);
        if (!empty($validationErrors)) {
            return redirect()
                ->route('cart.show')
                ->withErrors($validationErrors);
        }

        try {
            $order = DB::transaction(function () use ($request, $user, $cart) {
                // Get delivery address from session
                $deliveryAddressId = session('checkout.delivery_address_id');
                
                if (!$deliveryAddressId) {
                    throw new \Exception('Adresse de livraison manquante');
                }
                
                $deliveryAddress = Address::findOrFail($deliveryAddressId);

                // Calculate totals
                $totals = $this->cartService->getCartTotals($cart);

                // Create the order
                $order = Order::create([
                    'user_id' => $user->id,
                    'currency' => $cart->currency,
                    'subtotal' => $totals['subtotal'],
                    'tax_total' => $totals['tax_total'],
                    'shipping_total' => $totals['shipping_total'],
                    'discount_total' => $totals['discount_total'],
                    'grand_total' => $totals['grand_total'],
                    'billing_address_id' => $deliveryAddress->id, // Same as shipping for online payments
                    'shipping_address_id' => $deliveryAddress->id,
                ]);

                // Create order items from cart items
                foreach ($cart->items as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'name' => $cartItem->name,
                        'unit_amount' => $cartItem->unit_amount,
                        'qty' => $cartItem->qty,
                    ]);
                }

                // Clear the cart
                $cart->clear();

                return $order;
            });

            return redirect()
                ->route('checkout.success', $order)
                ->with('success', 'Commande créée avec succès ! Paiement en cours de traitement.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show order success page.
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function success(Order $order)
    {
        // Verify the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('checkout.success', compact('order'));
    }


    /**
     * Create or get an address for the user.
     *
     * @param \App\Models\User $user
     * @param array $addressData
     * @return Address
     */
    protected function createOrGetAddress($user, array $addressData): Address
    {
        // Check if an identical address already exists
        $existingAddress = $user->addresses()
            ->where('type', $addressData['type'])
            ->where('name', $addressData['name'])
            ->where('line1', $addressData['line1'])
            ->where('line2', $addressData['line2'])
            ->where('city', $addressData['city'])
            ->where('region', $addressData['region'])
            ->where('postal_code', $addressData['postal_code'])
            ->where('country', $addressData['country'])
            ->first();

        if ($existingAddress) {
            return $existingAddress;
        }

        // Create new address
        return $user->addresses()->create($addressData);
    }
}

