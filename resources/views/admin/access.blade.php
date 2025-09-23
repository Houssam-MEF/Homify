<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Homify') }} - Accès Administrateur</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/theme.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#405F80] via-[#3075BF] to-[#007BFF] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo et titre -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 flex items-center justify-center">
                <img src="{{ asset('images/logos/logo-final.svg') }}" alt="Homify" class="h-16 w-16">
            </div>
            <h2 class="mt-6 text-3xl font-bold text-white">Accès Administrateur</h2>
            <p class="mt-2 text-sm text-blue-100">Accès sécurisé au système d'administration</p>
        </div>

        <!-- Formulaire d'accès -->
        <div class="bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl border border-white/20 p-8">
            <form method="POST" action="{{ route('admin.verify-access') }}" class="space-y-6">
                @csrf

                <!-- Token d'accès -->
                <div>
                    <label for="token" class="block text-sm font-medium text-gray-700 mb-2">
                        Token d'accès
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="token" 
                               id="token" 
                               value="{{ old('token') }}"
                               placeholder="Entrez le token d'accès"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF] @error('token') border-red-500 @enderror"
                               required>
                        <button type="button" 
                                onclick="toggleTokenVisibility()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg id="eye-icon" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('token')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse e-mail
                    </label>
                    <div class="relative">
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               placeholder="admin@homify.fr"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-10 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF] @error('email') border-red-500 @enderror"
                               required>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               placeholder="Votre mot de passe"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 pl-10 focus:ring-2 focus:ring-[#3075BF] focus:border-[#3075BF] @error('password') border-red-500 @enderror"
                               required>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bouton de connexion -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#3075BF] hover:bg-[#405F80] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3075BF] transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Accéder à l'administration
                    </button>
                </div>

                <!-- Lien de retour -->
                <div class="text-center">
                    <a href="{{ route('catalog.home') }}" 
                       class="text-sm text-[#3075BF] hover:text-[#405F80] transition-colors">
                        ← Retour à l'accueil
                    </a>
                </div>
            </form>
        </div>

        <!-- Informations de sécurité -->
        <div class="text-center">
            <p class="text-xs text-blue-100">
                🔒 Accès sécurisé - Seuls les administrateurs autorisés peuvent accéder à cette section
            </p>
        </div>
    </div>
</div>

<script>
function toggleTokenVisibility() {
    const tokenInput = document.getElementById('token');
    const eyeIcon = document.getElementById('eye-icon');
    
    if (tokenInput.type === 'password') {
        tokenInput.type = 'text';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
        `;
    } else {
        tokenInput.type = 'password';
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        `;
    }
}
</script>
</body>
</html>
