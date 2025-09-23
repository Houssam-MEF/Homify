@extends('layouts.app')

@section('title', '- Adresses de livraison')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-[#405F80] mb-8">Adresse de livraison</h1>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-[#3075BF] text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                <span class="ml-2 text-[#3075BF] font-medium">Adresse</span>
            </div>
            <div class="w-16 h-0.5 bg-gray-300"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">2</div>
                <span class="ml-2 text-gray-600">Paiement</span>
            </div>
            <div class="w-16 h-0.5 bg-gray-300"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">3</div>
                <span class="ml-2 text-gray-600">Confirmation</span>
            </div>
        </div>
    </div>

    <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
        @csrf
        
        <!-- Delivery Address -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-[#405F80] mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Adresse de livraison
                </h2>

                <!-- Use existing address -->
                @if($deliveryAddresses->isNotEmpty())
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Utiliser une adresse existante :</label>
                        <select name="shipping_address_id" id="shipping_address_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]">
                            <option value="">Nouvelle adresse</option>
                            @foreach($deliveryAddresses as $address)
                                <option value="{{ $address->id }}">
                                    {{ $address->first_name }} {{ $address->last_name }}, {{ $address->line1 }}, {{ $address->city }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Address Form -->
                <div id="address-form" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="shipping_first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                            <input type="text" name="shipping_first_name" id="shipping_first_name" 
                                   value="{{ old('shipping_first_name', auth()->user()->name ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                                   required>
                        </div>
                        <div>
                            <label for="shipping_last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input type="text" name="shipping_last_name" id="shipping_last_name" 
                                   value="{{ old('shipping_last_name') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                        <input type="tel" name="shipping_phone" id="shipping_phone" 
                               value="{{ old('shipping_phone') }}"
                               placeholder="06 12 34 56 78"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                               required>
                    </div>

                    <div>
                        <label for="shipping_line1" class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                        <input type="text" name="shipping_line1" id="shipping_line1" 
                               value="{{ old('shipping_line1') }}"
                               placeholder="123 rue de la Paix"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                               required>
                    </div>

                    <div>
                        <label for="shipping_line2" class="block text-sm font-medium text-gray-700 mb-1">Complément d'adresse</label>
                        <input type="text" name="shipping_line2" id="shipping_line2" 
                               value="{{ old('shipping_line2') }}"
                               placeholder="Appartement 4B, étage 2"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 mb-1">Code postal *</label>
                            <input type="text" name="shipping_postal_code" id="shipping_postal_code" 
                                   value="{{ old('shipping_postal_code') }}"
                                   placeholder="75001"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                                   required>
                        </div>
                        <div class="col-span-2">
                            <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                            <input type="text" name="shipping_city" id="shipping_city" 
                                   value="{{ old('shipping_city') }}"
                                   placeholder="Paris"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-1">Pays *</label>
                        <select name="shipping_country" id="shipping_country" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                                required>
                            <option value="FR" {{ old('shipping_country', 'FR') == 'FR' ? 'selected' : '' }}>France</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-[#405F80] mb-4">Résumé de la commande</h2>
            
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
                
                <div class="border-t pt-3">
                    <div class="flex justify-between text-lg font-medium">
                        <span class="text-[#405F80]">Total</span>
                        <span class="text-[#3075BF]">{{ $totals['formatted_grand_total'] }}€</span>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" 
                        class="w-full bg-[#3075BF] text-white py-3 px-4 rounded-lg font-medium hover:bg-[#405F80] transition text-center">
                    Continuer vers le paiement
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
