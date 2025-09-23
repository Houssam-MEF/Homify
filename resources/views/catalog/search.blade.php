@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Search Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-[#405F80] mb-4">
                    @if($query)
                        Résultats de recherche pour "{{ $query }}"
                    @else
                        Recherche de produits
                    @endif
                </h1>
                <p class="text-sm text-gray-500">{{ $products->total() }} produit(s) trouvé(s)</p>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <form method="GET" action="{{ route('catalog.search') }}" class="max-w-2xl mx-auto">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <label for="q" class="sr-only">Rechercher</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   name="q" 
                                   id="q"
                                   value="{{ $query }}"
                                   placeholder="Rechercher un produit..."
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF] sm:text-sm">
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="sm:w-48">
                        <label for="category" class="sr-only">Catégorie</label>
                        <select name="category" 
                                id="category"
                                class="block w-full px-3 py-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF] sm:text-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Button -->
                    <button type="submit" 
                            class="px-6 py-3 bg-[#3075BF] text-white rounded-lg font-medium hover:bg-[#405F80] transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="group bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-200 hover:border-[#3075BF]">
                        <!-- Product Image -->
                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200">
                            @if($product->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="h-48 w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('catalog.product', $product->slug) }}" class="hover:text-[#3075BF] transition-colors duration-200">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            
                            @if($product->category)
                                <p class="text-xs text-gray-500 mb-2">{{ $product->category->name }}</p>
                            @endif
                            
                            @if($product->description)
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($product->description, 100) }}</p>
                            @endif

                            <div class="flex items-center justify-between">
                                <div class="text-lg font-bold text-[#3075BF]">
                                    {{ number_format($product->price_amount / 100, 2) }} €
                                </div>
                                
                                @if($product->stock > 0)
                                    <span class="text-sm text-green-600 font-medium">En stock</span>
                                @else
                                    <span class="text-sm text-red-600 font-medium">Rupture de stock</span>
                                @endif
                            </div>

                            <!-- Add to Cart Button -->
                            <div class="mt-4">
                                @if($product->stock > 0)
                                    <form action="{{ route('cart.add') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit" 
                                                class="w-full bg-[#3075BF] text-white px-4 py-2 rounded-lg font-medium hover:bg-[#405F80] transition-colors duration-200 flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                            </svg>
                                            Ajouter au panier
                                        </button>
                                    </form>
                                @else
                                    <button disabled 
                                            class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-medium cursor-not-allowed">
                                        Indisponible
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <!-- No Products Found -->
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if($query)
                        Aucun produit trouvé pour "{{ $query }}"
                    @else
                        Aucun produit trouvé
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if($query)
                        Essayez de modifier vos critères de recherche ou de parcourir nos catégories.
                    @else
                        Utilisez le formulaire de recherche ci-dessus pour trouver des produits.
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('catalog.home') }}" 
                       class="inline-flex items-center px-4 py-2 bg-[#3075BF] text-white rounded-lg hover:bg-[#405F80] transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour à l'accueil
                    </a>
                    @if($query)
                        <button onclick="document.getElementById('q').focus()" 
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Nouvelle recherche
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection



