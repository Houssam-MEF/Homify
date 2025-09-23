@extends('layouts.admin')

@section('title', 'Détails de la catégorie')
@section('page-title', 'Détails de la catégorie')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Détails de la catégorie</h1>
            <p class="text-gray-600 mt-2">Informations complètes sur la catégorie</p>
        </div>
        <div class="flex space-x-4">
            @if(!$category->parent)
                {{-- Actions pour catégorie principale --}}
                <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    ➕ Ajouter une sous-catégorie
                </a>
                <a href="{{ route('admin.categories.index', ['parent' => $category->id]) }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    📁 Voir toutes les sous-catégories
                </a>
            @endif
            <a href="{{ route('admin.categories.edit', $category) }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                ✏️ Modifier
            </a>
            <a href="{{ route('admin.categories.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                ← Retour aux catégories
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Informations principales -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex-shrink-0 h-16 w-16">
                        <div class="h-16 w-16 rounded-lg bg-{{ $category->is_active ? 'green' : 'gray' }}-100 flex items-center justify-center">
                            <span class="text-xl font-medium text-{{ $category->is_active ? 'green' : 'gray' }}-800">
                                {{ strtoupper(substr($category->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h2>
                        <p class="text-gray-600">{{ $category->slug }}</p>
                        @if($category->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mt-2">
                                ✅ Catégorie active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 mt-2">
                                ❌ Catégorie inactive
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                @if($category->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $category->description }}</p>
                    </div>
                @endif

                <!-- Informations détaillées -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Catégorie parente</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($category->parent)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $category->parent->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Catégorie principale</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ordre de tri</dt>
                                <dd class="text-sm text-gray-900">{{ $category->sort_order }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                                <dd class="text-sm text-gray-900">{{ $category->created_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                                <dd class="text-sm text-gray-900">{{ $category->updated_at->format('d/m/Y à H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Produits</dt>
                                <dd class="text-sm text-gray-900">{{ $category->products()->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sous-catégories</dt>
                                <dd class="text-sm text-gray-900">{{ $category->children()->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Niveau</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($category->parent)
                                        Sous-catégorie
                                    @else
                                        Catégorie principale
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Sous-catégories -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Sous-catégories</h3>
                    <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                        ➕ Ajouter une sous-catégorie
                    </a>
                </div>
                
                @if($category->children()->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($category->children as $child)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $child->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $child->products()->count() }} produits</p>
                                        <div class="mt-1">
                                            @if($child->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    ✅ Actif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    ❌ Inactif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2 ml-4">
                                        <a href="{{ route('admin.categories.show', $child) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-xs px-2 py-1 rounded hover:bg-blue-50">
                                            Voir
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $child) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-xs px-2 py-1 rounded hover:bg-indigo-50">
                                            Modifier
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-2">📁</div>
                        <h4 class="text-sm font-medium text-gray-900 mb-1">Aucune sous-catégorie</h4>
                        <p class="text-xs text-gray-500 mb-4">Créez votre première sous-catégorie pour organiser vos produits.</p>
                        <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" 
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                            Créer une sous-catégorie
                        </a>
                    </div>
                @endif
            </div>

            <!-- Produits récents -->
            @if($category->products()->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Produits récents</h3>
                    <div class="space-y-4">
                        @foreach($category->products()->latest()->take(5)->get() as $product)
                            <div class="flex items-center space-x-4 p-3 border border-gray-200 rounded-lg">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">
                                            {{ strtoupper(substr($product->name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ number_format($product->price / 100, 2) }}€</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($product->is_active) bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($category->products()->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                               class="text-blue-600 hover:text-blue-900 text-sm">
                                Voir tous les {{ $category->products()->count() }} produits →
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Actions sidebar -->
        <div class="space-y-6">
            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}">
                        @csrf
                        <button type="submit" 
                                class="w-full text-left px-4 py-2 text-sm text-{{ $category->is_active ? 'yellow' : 'green' }}-700 hover:bg-{{ $category->is_active ? 'yellow' : 'green' }}-50 rounded-lg transition-colors">
                            {{ $category->is_active ? '🔓 Désactiver la catégorie' : '🔐 Activer la catégorie' }}
                        </button>
                    </form>

                    @if(!$category->parent)
                        <a href="{{ route('admin.categories.create', ['parent' => $category->id]) }}" 
                           class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50 rounded-lg transition-colors">
                            ➕ Créer une sous-catégorie
                        </a>

                        <a href="{{ route('admin.categories.index', ['parent' => $category->id]) }}" 
                           class="block w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                            📁 Voir toutes les sous-catégories
                        </a>
                    @endif

                    <a href="{{ route('admin.products.create', ['category' => $category->id]) }}" 
                       class="block w-full text-left px-4 py-2 text-sm text-purple-700 hover:bg-purple-50 rounded-lg transition-colors">
                        🛍️ Ajouter un produit
                    </a>

                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action ne peut pas être annulée.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                            🗑️ Supprimer la catégorie
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
                        <dd class="text-gray-900 font-mono">{{ $category->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Slug</dt>
                        <dd class="text-gray-900 font-mono">{{ $category->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Chemin complet</dt>
                        <dd class="text-gray-900">
                            @if($category->parent)
                                {{ $category->parent->name }} → {{ $category->name }}
                            @else
                                {{ $category->name }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
