<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/theme.css', 'resources/js/app.js'])
    
    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 pt-20">
        @include('layouts.navigation')

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 mx-4 mt-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 mx-4 mt-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 mx-4 mt-4 rounded">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 mx-4 mt-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-[#405F80] text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Main Footer Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Company Info -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('images/logos/logo-final.svg') }}" alt="Homify" class="h-8 w-auto">
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            Homify est votre destination pour tous vos besoins en décoration et aménagement de maison. 
                            Découvrez notre sélection de produits de qualité pour transformer votre intérieur.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FFA200]">Liens rapides</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('catalog.home') }}" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Accueil</a></li>
                            <li><a href="{{ route('catalog.search') }}" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Tous les produits</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Nouveautés</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Meilleures ventes</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Promotions</a></li>
                        </ul>
                    </div>

                    <!-- Customer Service -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FFA200]">Service client</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">À propos de nous</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Contact</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Livraison</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Retours</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">FAQ</a></li>
                        </ul>
                    </div>

                    <!-- Newsletter -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FFA200]">Newsletter</h3>
                        <p class="text-gray-300 text-sm">Restez informé de nos dernières offres et nouveautés</p>
                        <form class="space-y-3">
                            <input type="email" placeholder="Votre adresse email" 
                                   class="w-full px-3 py-2 bg-[#544A38] border border-[#AB8138] rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFA200] focus:border-[#FFA200] transition-colors duration-200">
                            <button type="submit" 
                                    class="w-full bg-[#FFA200] hover:bg-[#AB8138] text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                S'abonner
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="border-t border-[#AB8138] mt-12 pt-8">
                    <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                        <div class="text-gray-400 text-sm">
                            &copy; {{ date('Y') }} Homify. Tous droits réservés.
                        </div>
                        <div class="flex space-x-6 text-sm">
                            <a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Mentions légales</a>
                            <a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Politique de confidentialité</a>
                            <a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">CGV</a>
                            <a href="#" class="text-gray-300 hover:text-[#FFA200] transition-colors duration-200">Cookies</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
    
    <script>
        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });

        function updateCartCount() {
            fetch('{{ route("cart.count") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count;
                })
                .catch(error => console.error('Error updating cart count:', error));
        }
    </script>
</body>
</html>
