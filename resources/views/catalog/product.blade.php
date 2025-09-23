@extends('layouts.app')

@section('title', "- {$product->name}")

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            @foreach($breadcrumbs as $breadcrumb)
                <li class="flex items-center">
                    @if(!$loop->last && $breadcrumb['url'])
                        <a href="{{ $breadcrumb['url'] }}" class="hover:text-gray-700">{{ $breadcrumb['name'] }}</a>
                        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    @else
                        <span class="text-gray-900">{{ $breadcrumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Images -->
        <div>
            @if($product->images->isNotEmpty())
                <div class="space-y-4">
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                        <img src="{{ $product->images->first()->url }}" 
                             alt="{{ $product->images->first()->alt_text }}"
                             class="w-full h-full object-cover">
                    </div>
                    
                    @if($product->images->count() > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($product->images->skip(1)->take(3) as $image)
                                <div class="aspect-square bg-gray-100 rounded overflow-hidden">
                                    <img src="{{ $image->url }}" 
                                         alt="{{ $image->alt_text }}"
                                         class="w-full h-full object-cover cursor-pointer hover:opacity-75">
                                </div>
                            @endforeach
                            
                            @if($product->images->count() > 4)
                                <div class="aspect-square bg-gray-800 rounded flex items-center justify-center text-white text-sm">
                                    +{{ $product->images->count() - 4 }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div class="aspect-square bg-gray-200 rounded-lg flex items-center justify-center">
                    <span class="text-gray-400 text-lg">No Image Available</span>
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
            
            <div class="flex items-center mb-4">
                <span class="text-3xl font-bold text-indigo-600">${{ $product->price }}</span>
                <span class="ml-4 text-sm text-gray-500">{{ $product->price_currency }}</span>
            </div>

            <div class="mb-6">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    @if($product->stock > 0)
                        {{ $product->stock }} in stock
                    @else
                        Out of stock
                    @endif
                </span>
            </div>

            @if($product->description)
                <div class="prose prose-sm mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Add to Cart Form -->
            @if($product->stock > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div>
                        <label for="qty" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <select name="qty" id="qty" class="border border-gray-300 rounded-md px-3 py-2 w-20">
                            @for($i = 1; $i <= min(10, $product->stock); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-[#3075BF] text-white py-3 px-6 rounded-lg font-medium hover:bg-[#405F80] transition">
                        Add to Cart
                    </button>
                </form>
            @else
                <button disabled class="w-full bg-gray-300 text-gray-500 py-3 px-6 rounded-lg font-medium">
                    Out of Stock
                </button>
            @endif

            <!-- Product Details -->
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Details</h3>
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="text-sm font-medium text-gray-500 w-24">Category:</dt>
                        <dd class="text-sm text-gray-900">
                            <a href="{{ route('catalog.category', $product->category->slug) }}" class="hover:text-indigo-600">
                                {{ $product->category->full_path }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex">
                        <dt class="text-sm font-medium text-gray-500 w-24">SKU:</dt>
                        <dd class="text-sm text-gray-900">{{ $product->id }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        <a href="{{ route('catalog.product', $relatedProduct->slug) }}">
                            @if($relatedProduct->main_image)
                                <img src="{{ $relatedProduct->main_image->url }}" 
                                     alt="{{ $relatedProduct->main_image->alt_text }}"
                                     class="w-full h-48 object-cover rounded-t-lg">
                            @else
                                <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                        </a>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2">
                                <a href="{{ route('catalog.product', $relatedProduct->slug) }}" class="hover:text-indigo-600">
                                    {{ $relatedProduct->name }}
                                </a>
                            </h3>
                            <p class="text-lg font-bold text-indigo-600">${{ $relatedProduct->price }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

