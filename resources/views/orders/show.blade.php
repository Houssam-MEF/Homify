@extends('layouts.app')

@section('title', '- Commande #' . $order->id)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Order Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-[#405F80] mb-2">Commande #{{ $order->id }}</h1>
                <p class="text-gray-600">Passée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-[#3075BF]">
                    {{ number_format($order->grand_total / 100, 2) }}€
                </p>
                <p class="text-sm text-gray-600">{{ $order->items->count() }} article(s)</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white shadow rounded-lg p-6">
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

            <!-- Order Status Timeline -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-[#405F80] mb-4">Statut de la commande</h2>
                
                <div class="flow-root">
                    <ul class="-mb-8">
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Commande créée</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            {{ $order->created_at->format('d/m/Y à H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        @if($order->status === 'confirmed' || $order->status === 'shipped' || $order->status === 'delivered')
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Commande confirmée</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $order->updated_at->format('d/m/Y à H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-500">En attente de confirmation</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif

                        @if($order->status === 'shipped' || $order->status === 'delivered')
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-500">Commande expédiée</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm text-gray-500">En attente d'expédition</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="space-y-6">
            <!-- Order Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-[#405F80] mb-4">Statut</h2>
                <div class="flex items-center">
                    @if($order->status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            En attente de validation
                        </span>
                    @elseif($order->status === 'confirmed')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Confirmée
                        </span>
                    @elseif($order->status === 'shipped')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Expédiée
                        </span>
                    @elseif($order->status === 'delivered')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Livrée
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            {{ ucfirst($order->status) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-[#405F80] mb-4">Adresse de livraison</h2>
                <div class="text-sm text-gray-600">
                    <p class="font-medium">{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                    <p>{{ $order->shippingAddress->line1 }}</p>
                    @if($order->shippingAddress->line2)
                        <p>{{ $order->shippingAddress->line2 }}</p>
                    @endif
                    <p>{{ $order->shippingAddress->postal_code }} {{ $order->shippingAddress->city }}</p>
                    <p>{{ $order->shippingAddress->country }}</p>
                    <p class="mt-2">{{ $order->shippingAddress->phone }}</p>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-[#405F80] mb-4">Résumé</h2>
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

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-[#405F80] mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('orders.index') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour aux commandes
                    </a>
                    
                    <a href="{{ route('catalog.home') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#3075BF] hover:bg-[#405F80] transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Continuer mes achats
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





