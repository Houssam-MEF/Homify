@extends('layouts.admin')

@section('title', 'Gestion des catégories')
@section('page-title', 'Gestion des catégories')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Gestion des catégories</h1>
        <p class="text-gray-600 mt-2">Organiser et gérer les catégories de produits de construction</p>
        @if(request('parent'))
            @php
                $parentCategory = \App\Models\Category::find(request('parent'));
            @endphp
            @if($parentCategory)
                <div class="mt-3 flex items-center space-x-2">
                    <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:text-blue-900">
                        ← Retour aux catégories principales
                    </a>
                    <span class="text-gray-400">|</span>
                    <span class="text-sm text-gray-600">
                        Sous-catégories de : <strong>{{ $parentCategory->name }}</strong>
                    </span>
                </div>
            @endif
        @endif
    </div>
    <div class="flex space-x-4">
        <a href="{{ route('admin.categories.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            ➕ Ajouter une catégorie principale
        </a>
    </div>
</div>

<!-- Instructions -->
@if(!request('parent'))
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <span class="text-blue-400 text-xl">💡</span>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Comment naviguer ?</h3>
            <p class="text-sm text-blue-700 mt-1">
                Cliquez sur une catégorie principale pour voir ses sous-catégories, ou utilisez le menu de navigation ci-dessous.
            </p>
        </div>
    </div>
</div>
@endif

<!-- Filtres et recherche -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-64">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
            <input type="text" 
                   id="search" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Rechercher par nom..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div class="min-w-48">
            <label for="parent" class="block text-sm font-medium text-gray-700 mb-2">Navigation</label>
            <select id="parent" 
                    name="parent" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @if(request('parent'))
                    <option value="">📁 Toutes les catégories principales</option>
                    @foreach($parentCategories as $category)
                        <option value="{{ $category->id }}" {{ request('parent') == $category->id ? 'selected' : '' }}>
                            ↳ Sous-catégories de "{{ $category->name }}"
                        </option>
                    @endforeach
                @else
                    <option value="">📁 Catégories principales</option>
                    @foreach($parentCategories as $category)
                        <option value="{{ $category->id }}">
                            ↳ Voir les sous-catégories de "{{ $category->name }}"
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                🔍 Rechercher
            </button>
            @if(request()->hasAny(['search', 'parent']))
                <a href="{{ route('admin.categories.index') }}" 
                   class="ml-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Effacer
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tableau des catégories -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($categories->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Catégorie
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hiérarchie
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produits
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Créé le
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categories as $category)
                        <tr class="hover:bg-gray-50 {{ !request('parent') ? 'cursor-pointer' : '' }}" 
                            @if(!request('parent'))
                                onclick="window.location.href='{{ route('admin.categories.index', ['parent' => $category->id]) }}'"
                            @endif>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-lg bg-{{ $category->is_active ? 'green' : 'gray' }}-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-{{ $category->is_active ? 'green' : 'gray' }}-800">
                                                {{ strtoupper(substr($category->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $category->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($category->parent)
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-400">↳</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $category->parent->name }}
                                        </span>
                                        <span class="text-xs text-gray-500">sous-catégorie</span>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-400">📁</span>
                                        <span class="text-gray-600 font-medium">Catégorie principale</span>
                                        @if($category->children()->count() > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $category->children()->count() }} sous-cat.
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $category->products_count ?? $category->products()->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ❌ Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if(request('parent'))
                                    {{-- Actions pour les sous-catégories --}}
                                    <a href="{{ route('admin.categories.show', $category) }}" 
                                       class="text-blue-600 hover:text-blue-900">Voir</a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                @else
                                    {{-- Actions pour les catégories principales --}}
                                    <a href="{{ route('admin.categories.show', $category) }}" 
                                       class="text-gray-600 hover:text-gray-900">Détails</a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                @endif
                                
                                <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-{{ $category->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $category->is_active ? 'yellow' : 'green' }}-900">
                                        {{ $category->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                      class="inline"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action ne peut pas être annulée.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $categories->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📁</div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune catégorie trouvée</h3>
            <p class="text-gray-500 mb-6">Commencez par créer votre première catégorie.</p>
            <a href="{{ route('admin.categories.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                Ajouter une catégorie
            </a>
        </div>
    @endif
</div>
@endsection
