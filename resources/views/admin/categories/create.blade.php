@extends('layouts.admin')

@section('title', 'Créer une catégorie')
@section('page-title', 'Créer une catégorie')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            @if(request('parent'))
                Créer une sous-catégorie
            @else
                Créer une catégorie principale
            @endif
        </h1>
        <p class="text-gray-600 mt-2">
            @if(request('parent'))
                Ajouter une nouvelle sous-catégorie à la catégorie parente
            @else
                Ajouter une nouvelle catégorie principale au système
            @endif
        </p>
        @if(request('parent'))
            @php
                $parentCategory = \App\Models\Category::find(request('parent'));
            @endphp
            @if($parentCategory)
                <div class="mt-3 flex items-center space-x-2">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        📁 Catégorie parente : {{ $parentCategory->name }}
                    </div>
                    <a href="{{ route('admin.categories.show', $parentCategory) }}" 
                       class="text-sm text-blue-600 hover:text-blue-900">
                        Voir la catégorie parente →
                    </a>
                </div>
            @endif
        @else
            <div class="mt-3 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-green-400 text-xl">ℹ️</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Création d'une catégorie principale</h3>
                        <p class="text-sm text-green-700 mt-1">
                            Vous créez une nouvelle catégorie principale. Vous pourrez ajouter des sous-catégories après la création.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            
            <div class="space-y-6">
                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la catégorie <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug (URL) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror"
                           required>
                    <p class="mt-1 text-sm text-gray-500">URL-friendly version du nom (ex: "electronique", "maison-jardin")</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catégorie parente -->
                @if(request('parent'))
                    {{-- Champ caché pour les sous-catégories --}}
                    <input type="hidden" name="parent_id" value="{{ request('parent') }}">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="text-blue-400 text-xl">📁</span>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Catégorie parente</h3>
                                <p class="text-sm text-blue-700 mt-1">
                                    Cette sous-catégorie sera ajoutée à : <strong>{{ $parentCategory->name ?? 'Catégorie inconnue' }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Champ visible pour les catégories principales --}}
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Catégorie parente
                        </label>
                        <select id="parent_id" 
                                name="parent_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('parent_id') border-red-500 @enderror">
                            <option value="">Catégorie principale (sans parent)</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Laissez vide pour créer une catégorie principale</p>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Statut -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Catégorie active
                    </label>
                </div>
                <p class="text-sm text-gray-500">
                    Les catégories inactives ne seront pas visibles sur le site public.
                </p>

                <!-- Ordre de tri -->
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Ordre de tri
                    </label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           value="{{ old('sort_order', 0) }}"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sort_order') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Ordre d'affichage (0 = premier)</p>
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                @if(request('parent'))
                    <a href="{{ route('admin.categories.show', request('parent')) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                        ➕ Créer la sous-catégorie
                    </button>
                @else
                    <a href="{{ route('admin.categories.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        📁 Créer la catégorie principale
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
// Auto-génération du slug à partir du nom
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // Supprimer les accents
        .replace(/[^a-z0-9\s-]/g, '') // Garder seulement lettres, chiffres, espaces et tirets
        .replace(/\s+/g, '-') // Remplacer espaces par tirets
        .replace(/-+/g, '-') // Remplacer tirets multiples par un seul
        .trim('-'); // Supprimer tirets en début/fin
    
    document.getElementById('slug').value = slug;
});
</script>
@endsection
