@extends('layouts.app')

@section('title', '- Home')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Slider -->
    <div class="relative mb-12" x-data="heroSlider()">
        <div class="relative overflow-hidden rounded-lg shadow-2xl">
            <!-- Slider Container -->
            <div class="relative h-96 md:h-[400px] lg:h-[400px]">
                <!-- Slide 1: Décoration & Aménagement -->
                <div x-show="currentSlide === 0" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform scale-110"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-90"
                     class="absolute inset-0 bg-gradient-to-r from-[#405F80] to-[#3075BF]">
                    <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                    <div class="relative h-full flex items-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                                <div class="text-white">
                                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                                        Transformez votre <span class="text-[#FFA200]">intérieur</span>
                                    </h1>
                                    <p class="text-xl md:text-2xl mb-8 text-gray-200">
                                        Découvrez notre collection exclusive de meubles et décorations pour créer l'intérieur de vos rêves
                                    </p>
                                    <div class="flex flex-col sm:flex-row gap-4">
                                        <a href="{{ route('catalog.search') }}" 
                                           class="bg-[#FFA200] hover:bg-[#AB8138] text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200 inline-flex items-center justify-center">
                                            Découvrir la collection
                                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                        <a href="#categories" 
                                           class="border-2 border-white text-white hover:bg-white hover:text-[#405F80] px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200">
                                            Voir les catégories
                                        </a>
                                    </div>
                                </div>
                                <div class="hidden lg:block">
                                    <div class="relative">
                                        <div class="w-full h-96 bg-gradient-to-br from-[#FFA200] to-[#AB8138] rounded-lg transform rotate-3"></div>
                                        <div class="absolute inset-0 bg-gradient-to-br from-[#405F80] to-[#007BFF] rounded-lg transform -rotate-3 -translate-y-4 -translate-x-4"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Nouveautés -->
                <div x-show="currentSlide === 1" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform scale-110"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-90"
                     class="absolute inset-0 bg-gradient-to-r from-[#544A38] to-[#AB8138]">
                    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                    <div class="relative h-full flex items-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="text-center text-white">
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                                    <span class="text-[#FFA200]">Nouveautés</span> 2024
                                </h1>
                                <p class="text-xl md:text-2xl mb-8 text-gray-200 max-w-3xl mx-auto">
                                    Découvrez nos dernières créations et tendances déco pour cette année
                                </p>
                                <a href="{{ route('catalog.search') }}?new=true" 
                                   class="bg-[#FFA200] hover:bg-[#AB8138] text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200 inline-flex items-center">
                                    Voir les nouveautés
                                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
            </a>
        </div>
    </div>
                    </div>
                </div>

                <!-- Slide 3: Promotions -->
                <div x-show="currentSlide === 2" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform scale-110"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-90"
                     class="absolute inset-0 bg-gradient-to-r from-[#007BFF] to-[#405F80]">
                    <div class="absolute inset-0 bg-black bg-opacity-25"></div>
                    <div class="relative h-full flex items-center">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                                <div class="text-white">
                                    <div class="inline-block bg-[#FFA200] text-[#405F80] px-4 py-2 rounded-full text-sm font-bold mb-4">
                                        JUSQU'À -50%
                                    </div>
                                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                                        <span class="text-[#FFA200]">Promotions</span> exceptionnelles
                                    </h1>
                                    <p class="text-xl md:text-2xl mb-8 text-gray-200">
                                        Profitez de nos offres spéciales sur une sélection de produits soigneusement choisis
                                    </p>
                                    <a href="{{ route('catalog.search') }}?sale=true" 
                                       class="bg-[#FFA200] hover:bg-[#AB8138] text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors duration-200 inline-flex items-center">
                                        Voir les promotions
                                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                    </a>
                                </div>
                                <div class="hidden lg:block">
                                    <div class="relative">
                                        <div class="text-6xl font-bold text-[#FFA200] opacity-20">-50%</div>
                                        <div class="absolute top-4 left-4 text-2xl font-bold text-white">SOLDES</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Arrows -->
            <button @click="previousSlide()" 
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-3 rounded-full transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button @click="nextSlide()" 
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-3 rounded-full transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Dots Indicator -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <button @click="currentSlide = 0" 
                        :class="currentSlide === 0 ? 'bg-[#FFA200]' : 'bg-white bg-opacity-50'"
                        class="w-3 h-3 rounded-full transition-all duration-200"></button>
                <button @click="currentSlide = 1" 
                        :class="currentSlide === 1 ? 'bg-[#FFA200]' : 'bg-white bg-opacity-50'"
                        class="w-3 h-3 rounded-full transition-all duration-200"></button>
                <button @click="currentSlide = 2" 
                        :class="currentSlide === 2 ? 'bg-[#FFA200]' : 'bg-white bg-opacity-50'"
                        class="w-3 h-3 rounded-full transition-all duration-200"></button>
            </div>
        </div>
    </div>

    <script>
        function heroSlider() {
            return {
                currentSlide: 0,
                totalSlides: 3,
                autoplayInterval: null,

                init() {
                    this.startAutoplay();
                },

                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                    this.resetAutoplay();
                },

                previousSlide() {
                    this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
                    this.resetAutoplay();
                },

                startAutoplay() {
                    this.autoplayInterval = setInterval(() => {
                        this.nextSlide();
                    }, 5000); // Change slide every 5 seconds
                },

                stopAutoplay() {
                    if (this.autoplayInterval) {
                        clearInterval(this.autoplayInterval);
                        this.autoplayInterval = null;
                    }
                },

                resetAutoplay() {
                    this.stopAutoplay();
                    this.startAutoplay();
                }
            }
        }
    </script>

    <!-- Categories Grid Section -->
    <section class="mb-16 bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">
                Des milliers de produits pour tous vos chantiers
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 md:gap-6">
                @foreach($featuredCategories as $category)
                    <div class="group relative bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 hover:border-[#3075BF]">
                        <div class="aspect-square relative overflow-hidden bg-white flex flex-col items-center justify-center p-4">
                            <!-- Icône avec couleur thème Homify -->
                            <div class="w-16 h-16 mb-3 flex items-center justify-center">
                                @if($loop->index == 0)
                                    <!-- Matériaux & Construction -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @elseif($loop->index == 1)
                                    <!-- Couverture & Bardage -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                @elseif($loop->index == 2)
                                    <!-- Isolation & Cloison -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                @elseif($loop->index == 3)
                                    <!-- Bois & Panneaux -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @elseif($loop->index == 4)
                                    <!-- Menuiserie & Aménagement -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                    </svg>
                                @elseif($loop->index == 5)
                                    <!-- Salle de Bain & Sanitaire -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                @elseif($loop->index == 6)
                                    <!-- Cuisine -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                @elseif($loop->index == 7)
                                    <!-- Plomberie -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                @elseif($loop->index == 8)
                                    <!-- Chauffage & Traitement de l'air -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                @elseif($loop->index == 9)
                                    <!-- Electricité & Eclairage -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                @elseif($loop->index == 10)
                                    <!-- Peinture & Droguerie -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                    </svg>
                                @elseif($loop->index == 11)
                                    <!-- Revêtement Sols & Murs -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @elseif($loop->index == 12)
                                    <!-- Plein air & Loisirs -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                @elseif($loop->index == 13)
                                    <!-- Aménagements extérieurs -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                @elseif($loop->index == 14)
                                    <!-- Outillage -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                @elseif($loop->index == 15)
                                    <!-- Quincaillerie -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                @else
                                    <!-- Icône générique pour les autres catégories -->
                                    <svg class="w-12 h-12 text-[#3075BF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                @endif
                            </div>
                            <!-- Nom de la catégorie -->
                            <span class="text-[#405F80] font-semibold text-xs text-center leading-tight">{{ $category->name }}</span>
                        </div>
                        <a href="{{ route('catalog.category', $category->slug) }}" class="absolute inset-0"></a>
                    </div>
                @endforeach

                <!-- Voir toutes les catégories -->
                <div class="group relative bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-2 border-dashed border-gray-300 hover:border-[#3075BF]">
                    <div class="aspect-square relative overflow-hidden flex items-center justify-center p-4">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-[#3075BF] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="text-[#3075BF] font-semibold text-xs">Voir toutes les catégories</span>
                        </div>
                    </div>
                    <a href="{{ route('catalog.search') }}" class="absolute inset-0"></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    @if($featuredCategories->isNotEmpty())
        <section id="categories" class="mb-12">
            <h2 class="text-2xl font-bold text-[#405F80] mb-6">Nos catégories</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredCategories as $category)
                    <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-[#FFA200]">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-[#405F80] mb-2">
                                <a href="{{ route('catalog.category', $category->slug) }}" class="hover:text-[#FFA200] transition-colors duration-200">
                                    {{ $category->name }}
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-4">
                                {{ $category->activeProducts->count() }} produits
                            </p>
                            
                            @if($category->activeChildren->isNotEmpty())
                                <div class="space-y-1">
                                    @foreach($category->activeChildren->take(3) as $child)
                                        <a href="{{ route('catalog.category', $child->slug) }}" 
                                           class="block text-sm text-[#007BFF] hover:text-[#FFA200] transition-colors duration-200">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                    @if($category->activeChildren->count() > 3)
                                        <span class="text-sm text-gray-500">
                                            +{{ $category->activeChildren->count() - 3 }} autres
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Featured Products -->
    @if($featuredProducts->isNotEmpty())
        <section>
            <h2 class="text-2xl font-bold text-[#405F80] mb-6">Produits vedettes</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-[#FFA200] group">
                        <a href="{{ route('catalog.product', $product->slug) }}">
                            @if($product->main_image)
                                <img src="{{ $product->main_image->url }}" 
                                     alt="{{ $product->main_image->alt_text }}"
                                     class="w-full h-48 object-cover rounded-t-lg group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-[#405F80] to-[#007BFF] rounded-t-lg flex items-center justify-center">
                                    <span class="text-white font-semibold">Image à venir</span>
                                </div>
                            @endif
                        </a>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-[#405F80] mb-2">
                                <a href="{{ route('catalog.product', $product->slug) }}" class="hover:text-[#FFA200] transition-colors duration-200">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $product->category->name }}</p>
                            <p class="text-lg font-bold text-[#3075BF]">{{ $product->price }}€</p>
                            
                            @if($product->stock > 0)
                                <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="w-full bg-[#3075BF] hover:bg-[#405F80] text-white py-2 px-4 rounded-lg font-semibold transition-colors duration-200">
                                        Ajouter au panier
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg mt-3">
                                    Rupture de stock
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

