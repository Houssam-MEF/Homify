@extends('layouts.admin')

@section('title', 'Détails du produit')
@section('page-title', 'Détails du produit')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Détails du produit</h1>
            <p class="text-gray-600 mt-2">Informations complètes sur le produit</p>
        </div>
        <div class="flex space-x-4">
            <a href="{{ route('admin.products.edit', $product) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                ✏️ Modifier
            </a>
            <a href="{{ route('admin.products.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                ← Retour aux produits
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Informations principales -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex-shrink-0 h-20 w-20">
                        @if($product->images->count() > 0)
                            <img class="h-20 w-20 rounded-lg object-cover" 
                                 src="{{ $product->images->first()->image_path }}" 
                                 alt="{{ $product->name }}">
                        @else
                            <div class="h-20 w-20 rounded-lg bg-gray-100 flex items-center justify-center">
                                <span class="text-xl font-medium text-gray-600">
                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                        <p class="text-gray-600">{{ $product->slug }}</p>
                        @if($product->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mt-2">
                                ✅ Produit actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 mt-2">
                                ❌ Produit inactive
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                @if($product->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $product->description }}</p>
                    </div>
                @endif

                <!-- Informations détaillées -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($product->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Aucune catégorie</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Prix</dt>
                                <dd class="text-sm text-gray-900 font-semibold">{{ number_format($product->price / 100, 2) }}€</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Stock</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($product->stock > 10) bg-green-100 text-green-800
                                        @elseif($product->stock > 0) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $product->stock }} unités
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                                <dd class="text-sm text-gray-900">{{ $product->created_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                                <dd class="text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Images</dt>
                                <dd class="text-sm text-gray-900">{{ $product->images->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Commandes</dt>
                                <dd class="text-sm text-gray-900">{{ $product->orderItems()->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Panier</dt>
                                <dd class="text-sm text-gray-900">{{ $product->cartItems()->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Images du produit -->
            @if($product->images->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Images du produit</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($product->images as $image)
                            <div class="relative group">
                                <img src="{{ $image->image_path }}" alt="{{ $product->name }}" class="w-full h-24 object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                    <button type="button" 
                                            class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm hover:bg-red-600"
                                            onclick="removeImage({{ $image->id }})">
                                        ×
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions sidebar -->
        <div class="space-y-6">
            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}">
                        @csrf
                        <button type="submit" 
                                class="w-full text-left px-4 py-2 text-sm text-{{ $product->is_active ? 'yellow' : 'green' }}-700 hover:bg-{{ $product->is_active ? 'yellow' : 'green' }}-50 rounded-lg transition-colors">
                            {{ $product->is_active ? '🔓 Désactiver le produit' : '🔐 Activer le produit' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.products.edit', $product) }}" 
                       class="block w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                        ✏️ Modifier le produit
                    </a>

                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action ne peut pas être annulée.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                            🗑️ Supprimer le produit
                        </button>
                    </form>
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations système</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">ID</dt>
                        <dd class="text-gray-900 font-mono">{{ $product->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Slug</dt>
                        <dd class="text-gray-900 font-mono">{{ $product->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Prix (centimes)</dt>
                        <dd class="text-gray-900 font-mono">{{ $product->price }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
// Supprimer une image
function removeImage(imageId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
        // Ici vous pouvez ajouter une requête AJAX pour supprimer l'image
        console.log('Supprimer l\'image:', imageId);
    }
}
</script>
@endsection



