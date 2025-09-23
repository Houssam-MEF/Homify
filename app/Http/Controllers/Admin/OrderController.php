<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $this->middleware('admin');
    }

    /**
     * Display a listing of all orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = Order::with(['user', 'items.product', 'billingAddress', 'shippingAddress'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'billingAddress', 'shippingAddress']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the order status.
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()
            ->back()
            ->with('success', 'Statut de la commande mis à jour avec succès.');
    }

    /**
     * Mark order as confirmed.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirm(Order $order)
    {
        $order->update(['status' => 'confirmed']);

        return redirect()
            ->back()
            ->with('success', 'Commande confirmée avec succès.');
    }

    /**
     * Mark order as shipped.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ship(Order $order)
    {
        $order->update(['status' => 'shipped']);

        return redirect()
            ->back()
            ->with('success', 'Commande marquée comme expédiée.');
    }

    /**
     * Mark order as delivered.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deliver(Order $order)
    {
        $order->update(['status' => 'delivered']);

        return redirect()
            ->back()
            ->with('success', 'Commande marquée comme livrée.');
    }

    /**
     * Cancel the order.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Order $order)
    {
        $order->update(['status' => 'cancelled']);

        return redirect()
            ->back()
            ->with('success', 'Commande annulée.');
    }
}