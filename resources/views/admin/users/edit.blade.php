@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')
@section('page-title', 'Modifier l\'utilisateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Modifier l'utilisateur</h1>
            <p class="text-gray-600 mt-2">Mettre à jour les informations et permissions de l'utilisateur</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Admin Status -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_admin" 
                               name="is_admin" 
                               value="1"
                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_admin" class="ml-2 block text-sm text-gray-900">
                            Privilèges d'administrateur
                        </label>
                    </div>
                    <p class="text-sm text-gray-500">
                        Les administrateurs ont un accès complet au panneau d'administration et peuvent gérer d'autres utilisateurs.
                    </p>

                    <!-- User Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Informations du compte</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Créé le :</span>
                                <span class="text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Dernière mise à jour :</span>
                                <span class="text-gray-900">{{ $user->updated_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Total commandes :</span>
                                <span class="text-gray-900">{{ $user->orders()->count() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Statut :</span>
                                <span class="text-gray-900">{{ $user->is_admin ? 'Administrateur' : 'Utilisateur normal' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.show', $user) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
