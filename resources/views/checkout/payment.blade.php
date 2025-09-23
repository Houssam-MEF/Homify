@extends('layouts.app')

@section('title', '- Paiement')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-[#405F80] mb-8">Paiement</h1>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-[#3075BF] text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                <span class="ml-2 text-[#3075BF] font-medium">Adresses</span>
            </div>
            <div class="w-16 h-0.5 bg-[#3075BF]"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-[#3075BF] text-white rounded-full flex items-center justify-center text-sm font-medium">2</div>
                <span class="ml-2 text-[#3075BF] font-medium">Paiement</span>
            </div>
            <div class="w-16 h-0.5 bg-gray-300"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center text-sm font-medium">3</div>
                <span class="ml-2 text-gray-600">Confirmation</span>
            </div>
        </div>
    </div>

    <form action="{{ route('checkout.process-payment') }}" method="POST" id="payment-form">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Payment Form -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-[#405F80] mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Informations de paiement
                </h2>

                <!-- Payment Method Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Méthode de paiement</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="card" checked 
                                   class="text-[#3075BF] focus:ring-[#3075BF]">
                            <div class="ml-3">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    <span class="font-medium">Carte bancaire</span>
                                </div>
                                <p class="text-sm text-gray-500">Visa, Mastercard, American Express</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Card Details -->
                <div id="card-details" class="space-y-4">
                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-700 mb-1">Numéro de carte *</label>
                        <input type="text" name="card_number" id="card_number" 
                               placeholder="1234 5678 9012 3456"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                               maxlength="19"
                               required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="card_expiry" class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration *</label>
                            <input type="text" name="card_expiry" id="card_expiry" 
                                   placeholder="MM/AA"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                                   maxlength="5"
                                   required>
                        </div>
                        <div>
                            <label for="card_cvv" class="block text-sm font-medium text-gray-700 mb-1">CVV *</label>
                            <input type="text" name="card_cvv" id="card_cvv" 
                                   placeholder="123"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                                   maxlength="4"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="cardholder_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du titulaire *</label>
                        <input type="text" name="cardholder_name" id="cardholder_name" 
                               placeholder="Jean Dupont"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF]"
                               required>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Paiement sécurisé</h3>
                            <p class="mt-1 text-sm text-blue-700">Vos informations de paiement sont protégées par un chiffrement SSL 256 bits.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-[#405F80] mb-4">Résumé de la commande</h2>
                
                <!-- Order Items -->
                <div class="space-y-3 mb-6">
                    @foreach($cart->items as $item)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-12 h-12">
                                @if($item->product && $item->product->main_image)
                                    <img src="{{ $item->product->main_image->url }}" 
                                         alt="{{ $item->product->main_image->alt_text }}"
                                         class="w-12 h-12 object-cover rounded">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-gray-400 text-xs">Aucune image</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item->name }}</p>
                                <p class="text-sm text-gray-500">Quantité: {{ $item->qty }}</p>
                            </div>
                            <div class="text-sm font-medium text-[#3075BF]">
                                {{ $item->formatted_total }}€
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="space-y-3 border-t pt-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Sous-total</span>
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

                <!-- Address Summary -->
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Adresse de livraison</h3>
                    <p class="text-sm text-gray-600">{{ $deliveryAddress->first_name }} {{ $deliveryAddress->last_name }}</p>
                    <p class="text-sm text-gray-600">{{ $deliveryAddress->line1 }}</p>
                    @if($deliveryAddress->line2)
                        <p class="text-sm text-gray-600">{{ $deliveryAddress->line2 }}</p>
                    @endif
                    <p class="text-sm text-gray-600">{{ $deliveryAddress->postal_code }} {{ $deliveryAddress->city }}</p>
                    <p class="text-sm text-gray-600">{{ $deliveryAddress->country }}</p>
                    <p class="text-sm text-gray-600">{{ $deliveryAddress->phone }}</p>
                </div>

                <!-- Payment Button -->
                <div class="mt-6">
                    <button type="submit" 
                            class="w-full bg-[#3075BF] text-white py-3 px-4 rounded-lg font-medium hover:bg-[#405F80] transition text-center flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Payer {{ $totals['formatted_grand_total'] }}€
                    </button>
                </div>

                <!-- Terms -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500">
                        En cliquant sur "Payer", vous acceptez nos 
                        <a href="#" class="text-[#3075BF] hover:underline">conditions générales de vente</a>
                        et notre 
                        <a href="#" class="text-[#3075BF] hover:underline">politique de confidentialité</a>.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Format card number
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Format expiry date
document.getElementById('card_expiry').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});

// Format CVV
document.getElementById('card_cvv').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});
</script>
@endsection