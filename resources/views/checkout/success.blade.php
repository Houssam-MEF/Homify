@extends('layouts.app')

@section('title', '- Commande confirmée')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Success Header -->
    <div class="text-center mb-8">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-[#405F80] mb-2">Commande confirmée !</h1>
        <p class="text-lg text-gray-600">Votre commande #{{ $order->id }} a été créée avec succès</p>
    </div>

    <!-- Order Details -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-[#405F80] mb-4">Détails de la commande</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Order Info -->
            <div>
                <h3 class="text-sm font-medium text-gray-900 mb-2">Informations de la commande</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <p><span class="font-medium">Numéro de commande :</span> #{{ $order->id }}</p>
                    <p><span class="font-medium">Date :</span> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    <p><span class="font-medium">Statut :</span> 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            En attente de validation
                        </span>
                    </p>
                </div>
            </div>

            <!-- Delivery Address -->
            <div>
                <h3 class="text-sm font-medium text-gray-900 mb-2">Adresse de livraison</h3>
                <div class="text-sm text-gray-600">
                    <p>{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                    <p>{{ $order->shippingAddress->line1 }}</p>
                    @if($order->shippingAddress->line2)
                        <p>{{ $order->shippingAddress->line2 }}</p>
                    @endif
                    <p>{{ $order->shippingAddress->postal_code }} {{ $order->shippingAddress->city }}</p>
                    <p>{{ $order->shippingAddress->country }}</p>
                    <p>{{ $order->shippingAddress->phone }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-[#405F80] mb-4">Articles commandés</h2>
        
        <div class="space-y-4">
            @foreach($order->items as $item)
                <div class="flex items-center space-x-4 py-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-gray-900">{{ $item->name }}</h3>
                        <p class="text-sm text-gray-500">Quantité : {{ $item->qty }}</p>
                    </div>
                    <div class="text-sm font-medium text-[#3075BF]">
                        {{ number_format($item->unit_amount / 100, 2) }}€
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Order Summary -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-[#405F80] mb-4">Résumé de la commande</h2>
        
        <div class="space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Sous-total</span>
                <span class="font-medium">{{ number_format($order->subtotal / 100, 2) }}€</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Livraison</span>
                <span class="font-medium">{{ number_format($order->shipping_total / 100, 2) }}€</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">TVA</span>
                <span class="font-medium">{{ number_format($order->tax_total / 100, 2) }}€</span>
            </div>
            @if($order->discount_total > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Remise</span>
                    <span class="font-medium text-green-600">-{{ number_format($order->discount_total / 100, 2) }}€</span>
                </div>
            @endif
            <div class="border-t border-gray-200 pt-2">
                <div class="flex justify-between text-lg font-semibold">
                    <span class="text-[#405F80]">Total</span>
                    <span class="text-[#3075BF]">{{ number_format($order->grand_total / 100, 2) }}€</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Paiement en cours de validation</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Votre commande a été créée avec succès. Notre équipe va vérifier le paiement et valider votre commande dans les plus brefs délais.</p>
                    <p class="mt-1">Vous recevrez un email de confirmation une fois la commande validée.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('catalog.home') }}" 
           class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#3075BF] hover:bg-[#405F80] transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Continuer mes achats
        </a>
        
        <a href="{{ route('orders.index') }}" 
           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Mes commandes
        </a>
    </div>
</div>
@endsection





