<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class StripePaymentService
{
    /**
     * Create a new StripePaymentService instance.
     */
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a PaymentIntent for the given order.
     *
     * @param Order $order
     * @return array
     * @throws \Exception
     */
    public function createPaymentIntent(Order $order): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $order->grand_total,
                'currency' => strtolower($order->currency),
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->number,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Create a payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => 'stripe',
                'reference' => $paymentIntent->id,
                'amount' => $order->grand_total,
                'status' => $this->mapStripeStatusToLocal($paymentIntent->status),
                'payload' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                ],
            ]);

            return [
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'payment_id' => $payment->id,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to create payment intent: ' . $e->getMessage());
        }
    }

    /**
     * Handle Stripe webhook events.
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     * @throws \Exception
     */
    public function handleWebhook(string $payload, string $signature): bool
    {
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in Stripe webhook', ['error' => $e->getMessage()]);
            throw new \Exception('Invalid payload');
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid signature in Stripe webhook', ['error' => $e->getMessage()]);
            throw new \Exception('Invalid signature');
        }

        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'id' => $event->id,
        ]);

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'payment_intent.canceled':
                $this->handlePaymentIntentCanceled($event->data->object);
                break;

            case 'payment_intent.processing':
                $this->handlePaymentIntentProcessing($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event type', ['type' => $event->type]);
        }

        return true;
    }

    /**
     * Handle successful payment intent.
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    protected function handlePaymentIntentSucceeded(PaymentIntent $paymentIntent): void
    {
        $payment = Payment::where('reference', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for successful PaymentIntent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        $payment->update([
            'status' => 'paid',
            'payload' => array_merge($payment->payload ?? [], [
                'payment_method' => $paymentIntent->payment_method,
                'charges' => $paymentIntent->charges->data ?? [],
            ]),
        ]);

        // Mark the order as paid
        $payment->order->markAsPaid();

        // Decrement product stock
        $this->decrementProductStock($payment->order);

        Log::info('Payment marked as successful', [
            'order_id' => $payment->order_id,
            'payment_id' => $payment->id,
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }

    /**
     * Handle failed payment intent.
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    protected function handlePaymentIntentFailed(PaymentIntent $paymentIntent): void
    {
        $payment = Payment::where('reference', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for failed PaymentIntent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        $payment->update([
            'status' => 'failed',
            'payload' => array_merge($payment->payload ?? [], [
                'last_payment_error' => $paymentIntent->last_payment_error,
            ]),
        ]);

        Log::info('Payment marked as failed', [
            'order_id' => $payment->order_id,
            'payment_id' => $payment->id,
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }

    /**
     * Handle canceled payment intent.
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    protected function handlePaymentIntentCanceled(PaymentIntent $paymentIntent): void
    {
        $payment = Payment::where('reference', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for canceled PaymentIntent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        $payment->update([
            'status' => 'failed',
            'payload' => array_merge($payment->payload ?? [], [
                'cancellation_reason' => $paymentIntent->cancellation_reason,
            ]),
        ]);

        // Also cancel the order
        $payment->order->markAsCancelled();

        Log::info('Payment marked as canceled', [
            'order_id' => $payment->order_id,
            'payment_id' => $payment->id,
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }

    /**
     * Handle processing payment intent.
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    protected function handlePaymentIntentProcessing(PaymentIntent $paymentIntent): void
    {
        $payment = Payment::where('reference', $paymentIntent->id)->first();

        if (!$payment) {
            Log::warning('Payment not found for processing PaymentIntent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        $payment->update([
            'status' => 'processing',
        ]);

        Log::info('Payment marked as processing', [
            'order_id' => $payment->order_id,
            'payment_id' => $payment->id,
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }

    /**
     * Map Stripe status to local payment status.
     *
     * @param string $stripeStatus
     * @return string
     */
    protected function mapStripeStatusToLocal(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'requires_payment_method', 'requires_confirmation', 'requires_action' => 'requires_payment',
            'processing' => 'processing',
            'succeeded' => 'paid',
            'canceled' => 'failed',
            default => 'requires_payment',
        };
    }

    /**
     * Decrement product stock after successful payment.
     *
     * @param Order $order
     * @return void
     */
    protected function decrementProductStock(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decreaseStock($item->qty);
                
                Log::info('Product stock decremented', [
                    'product_id' => $item->product_id,
                    'quantity' => $item->qty,
                    'remaining_stock' => $item->product->fresh()->stock,
                ]);
            }
        }
    }

    /**
     * Retrieve a PaymentIntent from Stripe.
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws \Exception
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve PaymentIntent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to retrieve payment intent: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a PaymentIntent.
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws \Exception
     */
    public function cancelPaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            return $paymentIntent->cancel();
        } catch (\Exception $e) {
            Log::error('Failed to cancel PaymentIntent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to cancel payment intent: ' . $e->getMessage());
        }
    }
}

