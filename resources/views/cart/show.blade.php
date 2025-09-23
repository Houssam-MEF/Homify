@extends('layouts.app')

@section('title', '- Panier')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-[#405F80] mb-8">Panier</h1>

    @if($cart->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9m-9 0h9" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Votre panier est vide</h3>
            <p class="mt-1 text-sm text-gray-500">Commencez vos achats pour ajouter des articles à votre panier.</p>
            <div class="mt-6">
                <a href="{{ route('catalog.home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#3075BF] hover:bg-[#405F80]">
                    Continuer mes achats
                </a>
            </div>
        </div>
    @else
        <!-- Validation Errors -->
        @if(!empty($validationErrors))
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Problèmes dans le panier</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($validationErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-[#405F80]">Articles dans votre panier</h2>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($cart->items as $item)
                            <div class="p-6">
                                <div class="flex items-center">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-16 h-16">
                                        @if($item->product && $item->product->main_image)
                                            <img src="{{ $item->product->main_image->url }}" 
                                                 alt="{{ $item->product->main_image->alt_text }}"
                                                 class="w-16 h-16 object-cover rounded">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                                <span class="text-gray-400 text-xs">Aucune image</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between">
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-900">
                                                    @if($item->product)
                                                        <a href="{{ route('catalog.product', $item->product->slug) }}" class="hover:text-[#3075BF]">
                                                            {{ $item->name }}
                                                        </a>
                                                    @else
                                                        {{ $item->name }}
                                                        <span class="text-red-500 text-xs">(Produit plus disponible)</span>
                                                    @endif
                                                </h3>
                                                <p class="text-sm text-gray-500">{{ $item->formatted_unit_price }}€ chacun</p>
                                            </div>
                                            
                                            <div class="flex items-center space-x-4">
                                                <!-- Quantity Update with Number Input -->
                                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center" id="qty-form-{{ $item->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <label for="qty-{{ $item->id }}" class="sr-only">Quantité</label>
                                                    <div class="flex items-center border border-gray-300 rounded-md">
                                                        <button type="button" 
                                                                data-action="decrement"
                                                                data-item-id="{{ $item->id }}"
                                                                data-max-qty="{{ $item->product ? min(20, $item->product->stock) : 20 }}"
                                                                class="px-2 py-1 text-gray-600 hover:text-[#3075BF] hover:bg-gray-50 rounded-l-md transition-colors duration-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                            </svg>
                                                        </button>
                                                        <input type="number" 
                                                               name="qty" 
                                                               id="qty-{{ $item->id }}" 
                                                               value="{{ $item->qty }}"
                                                               min="1" 
                                                               max="{{ $item->product ? min(20, $item->product->stock) : 20 }}"
                                                               onchange="this.form.submit()"
                                                               class="w-16 px-2 py-1 text-center text-sm border-0 focus:ring-2 focus:ring-[#3075BF] focus:outline-none">
                                                        <button type="button" 
                                                                data-action="increment"
                                                                data-item-id="{{ $item->id }}"
                                                                data-max-qty="{{ $item->product ? min(20, $item->product->stock) : 20 }}"
                                                                class="px-2 py-1 text-gray-600 hover:text-[#3075BF] hover:bg-gray-50 rounded-r-md transition-colors duration-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </form>

                                                <!-- Item Total -->
                                                <div class="text-right">
                                                    <p class="text-sm font-medium text-[#3075BF]">{{ $item->formatted_total }}€</p>
                                                </div>

                                                <!-- Remove Button -->
                                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm transition-colors duration-200">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Clear Cart -->
                <div class="mt-4">
                    <form action="{{ route('cart.clear') }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir vider votre panier ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm transition-colors duration-200">
                            Vider le panier
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-[#405F80] mb-4">Résumé de la commande</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Sous-total ({{ $cart->total_items }} article{{ $cart->total_items > 1 ? 's' : '' }})</span>
                            <span class="text-gray-900">{{ $totals['formatted_subtotal'] }}€</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Livraison</span>
                            <span class="text-gray-900">{{ $totals['formatted_shipping_total'] }}€</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">TVA</span>
                            <span class="text-gray-900">{{ $totals['formatted_tax_total'] }}€</span>
                        </div>
                        
                        @if($totals['discount_total'] > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Remise</span>
                                <span class="text-green-600">-{{ $totals['formatted_discount_total'] }}€</span>
                            </div>
                        @endif
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-medium">
                                <span class="text-[#405F80]">Total</span>
                                <span class="text-[#3075BF]">{{ $totals['formatted_grand_total'] }}€</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        @if(empty($validationErrors))
                            @auth
                                <a href="{{ route('checkout.start') }}" 
                                   class="w-full bg-[#3075BF] text-white py-3 px-4 rounded-lg font-medium hover:bg-[#405F80] transition text-center block">
                                    Procéder au paiement
                                </a>
                            @else
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 mb-4">Veuillez vous connecter pour finaliser la commande</p>
                                    <a href="{{ route('login') }}" 
                                       class="w-full bg-[#3075BF] text-white py-3 px-4 rounded-lg font-medium hover:bg-[#405F80] transition text-center block">
                                        Se connecter pour commander
                                    </a>
                                </div>
                            @endauth
                        @else
                            <button disabled 
                                    class="w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-medium">
                                Veuillez résoudre les problèmes du panier
                            </button>
                        @endif
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('catalog.home') }}" 
                           class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-200 transition text-center block">
                            Continuer mes achats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity increment/decrement buttons
    document.querySelectorAll('[data-action="increment"], [data-action="decrement"]').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const itemId = this.getAttribute('data-item-id');
            const maxQty = parseInt(this.getAttribute('data-max-qty'));
            const input = document.getElementById('qty-' + itemId);
            const currentValue = parseInt(input.value);
            
            if (action === 'increment' && currentValue < maxQty) {
                input.value = currentValue + 1;
                input.form.submit();
            } else if (action === 'decrement' && currentValue > 1) {
                input.value = currentValue - 1;
                input.form.submit();
            }
        });
    });
});
</script>
@endsection

