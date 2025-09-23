<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payment\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * The Stripe payment service instance.
     */
    protected StripePaymentService $stripeService;

    /**
     * Create a new controller instance.
     */
    public function __construct(StripePaymentService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create a PaymentIntent for the given order.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createIntent(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        try {
            $order = Order::findOrFail($request->order_id);

            // Ensure the order belongs to the authenticated user
            if ($order->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized access to order.'], 403);
            }

            // Ensure the order is still pending
            if (!$order->isPending()) {
                return response()->json(['error' => 'This order has already been processed.'], 400);
            }

            $result = $this->stripeService->createPaymentIntent($order);

            return response()->json([
                'client_secret' => $result['client_secret'],
                'payment_intent_id' => $result['payment_intent_id'],
            ]);
        } catch (\Exception $e) {
            Log::error('PaymentIntent creation failed', [
                'order_id' => $request->order_id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Failed to create payment intent. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle Stripe webhook events.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $this->stripeService->handleWebhook($payload, $signature);

            return response('Webhook handled successfully', 200);
        } catch (\Exception $e) {
            Log::error('Stripe webhook handling failed', [
                'error' => $e->getMessage(),
                'payload_length' => strlen($payload),
            ]);

            return response('Webhook handling failed', 400);
        }
    }

    /**
     * Handle successful payment (redirect from Stripe).
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request, Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Check if payment was successful
        if ($order->isPaid()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Payment successful! Your order has been confirmed.');
        }

        // Payment might still be processing
        return redirect()
            ->route('orders.show', $order)
            ->with('info', 'Your payment is being processed. You will receive a confirmation email shortly.');
    }

    /**
     * Handle failed payment (redirect from Stripe).
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function failed(Request $request, Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order.');
        }

        return redirect()
            ->route('checkout.payment', $order)
            ->withErrors(['payment' => 'Payment failed. Please try again or use a different payment method.']);
    }

    /**
     * Cancel payment and return to checkout.
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

        return redirect()
            ->route('checkout.payment', $order)
            ->with('info', 'Payment was cancelled. You can try again when ready.');
    }
}

