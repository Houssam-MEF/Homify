@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumbs -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    @foreach($breadcrumbs as $index => $breadcrumb)
                        @if($index === 0)
                            <li class="inline-flex items-center">
                                <a href="{{ route('catalog.home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#FFA200] transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                    {{ $breadcrumb['name'] }}
                                </a>
                            </li>
                        @else
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    @if($breadcrumb['slug'])
                                        <a href="{{ route('catalog.category', $breadcrumb['slug']) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-[#3075BF] md:ml-2 transition-colors duration-200">
                                            {{ $breadcrumb['name'] }}
                                        </a>
                                    @else
                                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                                            {{ $breadcrumb['name'] }}
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>

    <!-- Category Header -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-[#405F80] mb-4">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">{{ $category->description }}</p>
                @endif
                <p class="text-sm text-gray-500 mt-4">{{ $products->total() }} produit(s) trouvé(s)</p>
            </div>
        </div>
    </div>

    <!-- Subcategories -->
    @if($category->activeChildren->isNotEmpty())
        <div class="bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-xl font-semibold text-[#405F80] mb-6">Sous-catégories</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($category->activeChildren as $subcategory)
                        <a href="{{ route('catalog.category', $subcategory->slug) }}" 
                           class="group bg-white rounded-lg p-4 text-center hover:shadow-lg transition-all duration-300 border border-gray-200 hover:border-[#3075BF]">
                            <div class="w-12 h-12 mx-auto mb-3 bg-[#FFA200] rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-[#3075BF] transition-colors duration-200">
                                {{ $subcategory->name }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
                <p class="text-gray-500 mb-6">Il n'y a actuellement aucun produit dans cette catégorie.</p>
                <a href="{{ route('catalog.home') }}" 
                   class="inline-flex items-center px-4 py-2 bg-[#3075BF] text-white rounded-lg hover:bg-[#405F80] transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour à l'accueil
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
