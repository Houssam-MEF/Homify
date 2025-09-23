<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Homify') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/theme.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-[#405F80] via-[#3075BF] to-[#007BFF]">
            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <a href="{{ route('catalog.home') }}" class="inline-block">
                    <img src="{{ asset('images/logos/logo-final.svg') }}" alt="Homify" class="w-24 h-24 mx-auto mb-4">
                </a>
                <h1 class="text-3xl font-bold text-white mb-2">Bienvenue sur Homify</h1>
                <p class="text-white/80">Votre destination pour tous vos projets d'aménagement</p>
            </div>

            <!-- Carte de connexion -->
            <div class="w-full sm:max-w-md px-6 py-8 bg-white/95 backdrop-blur-md shadow-2xl rounded-2xl border border-white/20">
                {{ $slot }}
            </div>

            <!-- Liens supplémentaires -->
            <div class="mt-8 text-center">
                <p class="text-white/80 text-sm">
                    Pas encore de compte ? 
                    <a href="{{ route('register') }}" class="text-[#FFA200] hover:text-[#AB8138] font-semibold transition-colors duration-200">
                        Créer un compte
                    </a>
                </p>
            </div>
        </div>
    </body>
</html>
