@extends('layouts.admin')

@section('title', 'Order #' . $order->id)
@section('page-title', 'Order #' . $order->id)

@section('content')
<!-- Header -->
<div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.orders.index') }}" 
                   class="inline-flex items-center text-[#3075BF] hover:text-[#405F80] transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour aux commandes
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-[#405F80]">Commande #{{ $order->id }}</h1>
                    <p class="text-gray-600">Détails de la commande</p>
                </div>
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
        <!-- Détails de la commande -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations générales -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-[#405F80] mb-4">Informations de la commande</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Commande</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium">Numéro :</span> #{{ $order->id }}</p>
                            <p><span class="font-medium">Date :</span> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                            <p><span class="font-medium">Statut :</span> 
                                @if($order->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        En attente
                                    </span>
                                @elseif($order->status === 'confirmed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Confirmée
                                    </span>
                                @elseif($order->status === 'shipped')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Expédiée
                                    </span>
                                @elseif($order->status === 'delivered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Livrée
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Client</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium">Nom :</span> {{ $order->user->name }}</p>
                            <p><span class="font-medium">Email :</span> {{ $order->user->email }}</p>
                            <p><span class="font-medium">ID Client :</span> #{{ $order->user->id }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Articles commandés -->
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
                                @if($item->product)
                                    <p class="text-sm text-gray-500">Produit ID : {{ $item->product->id }}</p>
                                @endif
                            </div>
                            <div class="text-sm font-medium text-[#3075BF]">
                                {{ number_format($item->unit_amount / 100, 2) }}€
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Adresses -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-[#405F80] mb-4">Adresses</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Adresse de facturation -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Facturation</h3>
                        <div class="text-sm text-gray-600">
                            <p>{{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}</p>
                            <p>{{ $order->billingAddress->line1 }}</p>
                            @if($order->billingAddress->line2)
                                <p>{{ $order->billingAddress->line2 }}</p>
                            @endif
                            <p>{{ $order->billingAddress->postal_code }} {{ $order->billingAddress->city }}</p>
                            <p>{{ $order->billingAddress->country }}</p>
                            <p>{{ $order->billingAddress->phone }}</p>
                        </div>
                    </div>

                    <!-- Adresse de livraison -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Livraison</h3>
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
        </div>

        <!-- Actions et résumé -->
        <div class="space-y-6">
            <!-- Actions sur la commande -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-[#405F80] mb-4">Actions</h2>
                
                <div class="space-y-3">
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('admin.orders.confirm', $order) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Confirmer la commande
                            </button>
                        </form>
                    @endif
                    
                    @if($order->status === 'confirmed')
                        <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Marquer comme expédiée
                            </button>
                        </form>
                    @endif
                    
                    @if($order->status === 'shipped')
                        <form method="POST" action="{{ route('admin.orders.deliver', $order) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Marquer comme livrée
                            </button>
                        </form>
                    @endif
                    
                    @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                        <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors"
                                    onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Annuler la commande
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Changement de statut -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-[#405F80] mb-4">Changer le statut</h2>
                
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Nouveau statut</label>
                        <select name="status" id="status" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Expédiée</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Livrée</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                    
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#3075BF] hover:bg-[#405F80] transition-colors">
                        Mettre à jour le statut
                    </button>
                </form>
            </div>

            <!-- Résumé financier -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-[#405F80] mb-4">Résumé financier</h2>
                
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
        </div>
    </div>
@endsection



