<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the user's orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.product', 'billingAddress', 'shippingAddress'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        $order->load([
            'items.product.images',
            'billingAddress',
            'shippingAddress',
            'payments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        return view('orders.show', compact('order'));
    }

    /**
     * Download order invoice (if implemented).
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function invoice(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Only allow invoice download for paid orders
        if (!$order->isPaid()) {
            return redirect()
                ->route('orders.show', $order)
                ->withErrors(['error' => 'Invoice is only available for paid orders.']);
        }

        // For now, redirect back with a message
        // In a real application, you would generate and return a PDF invoice
        return redirect()
            ->route('orders.show', $order)
            ->with('info', 'Invoice download feature will be available soon.');
    }

    /**
     * Cancel an order (if still pending).
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Only allow cancellation of pending orders
        if (!$order->isPending()) {
            return redirect()
                ->route('orders.show', $order)
                ->withErrors(['error' => 'Only pending orders can be cancelled.']);
        }

        try {
            $order->markAsCancelled();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order has been cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('orders.show', $order)
                ->withErrors(['error' => 'Failed to cancel order: ' . $e->getMessage()]);
        }
    }

    /**
     * Reorder - add all items from this order to the cart.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reorder(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        try {
            $cartService = app(\App\Services\Cart\CartService::class);
            $addedItems = 0;
            $skippedItems = [];

            foreach ($order->items as $orderItem) {
                if ($orderItem->product && $orderItem->product->isAvailable($orderItem->qty)) {
                    $cartService->addToCart($orderItem->product, $orderItem->qty);
                    $addedItems++;
                } else {
                    $skippedItems[] = $orderItem->name;
                }
            }

            $message = "Added {$addedItems} items to your cart.";
            
            if (!empty($skippedItems)) {
                $message .= ' Some items were skipped because they are no longer available: ' . implode(', ', $skippedItems);
            }

            return redirect()
                ->route('cart.show')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('orders.show', $order)
                ->withErrors(['error' => 'Failed to reorder: ' . $e->getMessage()]);
        }
    }
}

