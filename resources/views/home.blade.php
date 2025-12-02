@extends('layouts.base')

@section('content')
    <div class="container mx-auto px-4 py-8">
        
        <!-- Message de succès après vote -->
        @if(session('success'))
        <div class="mb-8 bg-green-50 border border-green-200 rounded-xl p-6 shadow-lg max-w-2xl mx-auto">
            <div class="flex items-center">
                <div class="text-green-500 mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-green-800 mb-1">🎉 Vote enregistré avec succès !</h3>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none'" class="text-green-400 hover:text-green-600 ml-4">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif
        @if (session('success'))
            <div class="mb-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold text-text-gray-900 mb-4">
                <span class="animated-title">REINE ESGIS</span> <span class="text-gradient">Bénin {{ date('Y') }}</span>
            </h1>
            <p class="text-lg text-text-gray-600 mb-6">
                Découvrez les candidates exceptionnelles et votez pour votre favorite
            </p>
            <div
                class="inline-block bg-accent-yellow text-text-yellow-900 font-bold py-3 px-6 rounded-full shadow-md text-lg">
                {{ number_format($totalVotes, 0, ',', ' ') }} votes au total
            </div>
        </div>


        <!-- Podium Top 3 des candidates les plus votées -->
        @if($activeMisses->where('votes_count', '>', 0)->count() >= 3)
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    🏆 PODIUM DES REINES 👑
                </h2>
                <p class="text-lg text-gray-600 mb-4">Les candidates les plus soutenues par vos votes</p>
                
                <!-- Statistiques globales avec suspense -->
                <div class="flex flex-col md:flex-row justify-center items-center gap-4 mb-6">
                    <div class="bg-gradient-to-r from-purple-100 to-pink-100 px-6 py-3 rounded-full">
                        <span class="text-purple-700 font-semibold">🔥 {{ number_format($totalVotes) }} votes au total</span>
                    </div>
                    <div class="bg-gradient-to-r from-blue-100 to-indigo-100 px-6 py-3 rounded-full">
                        <span class="text-blue-700 font-semibold">⚡ TOP 3 : {{ number_format($top3Total) }} votes</span>
                    </div>
                </div>
                
            </div>
            
            <div class="flex flex-col lg:flex-row items-end justify-center gap-6 max-w-7xl mx-auto">
                @php
                    $topCandidates = $activeMisses->take(3);
                    $first = $topCandidates->get(0);
                    $second = $topCandidates->get(1);
                    $third = $topCandidates->get(2);
                @endphp

                <!-- 2ème Place -->
                @if($second && $second->votes_count > 0)
                <div class="w-full lg:w-80 order-2 lg:order-1">
                    <div class="podium-card silver-card group relative bg-gradient-to-br from-gray-100 to-gray-300 rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-500 animate-float-delayed">
                        <div class="absolute top-4 right-4 z-10">
                            <div class="bg-gray-400 text-gray-900 px-3 py-1 rounded-full font-bold text-sm flex items-center">
                                🥈 2ème
                            </div>
                        </div>
                        <div class="aspect-[3/4] relative overflow-hidden">
                            <img src="{{ asset('storage/media/' . $second->photo_principale) }}" 
                                 alt="{{ $second->prenom }} {{ $second->nom }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                 style="object-position: center 20%;">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-900">{{ $second->prenom }} {{ $second->nom }}</h3>
                            <p class="text-gray-600">{{ $second->pays }}</p>
                            
                            <!-- Barre de progression animée pour 2ème place -->
                            <div class="mt-4">
                                <div class="flex justify-center items-center mb-3">
                                    <span class="text-gray-900 font-bold text-xl">{{ $second->percentage ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200/50 rounded-full h-5 shadow-inner border border-gray-300">
                                    <div class="bg-gradient-to-r from-gray-400 via-gray-500 to-gray-600 h-full rounded-full progress-bar-silver shadow-lg relative overflow-hidden" 
                                         style="width: 0%" 
                                         data-percentage="{{ $second->percentage ?? 0 }}">
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12"></div>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <span class="text-gray-600 text-sm font-medium">🥈 CHALLENGER</span>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button onclick="window.location='{{ route('candidates.show', $second->id) }}'" 
                                        class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                    Voir
                                </button>
                                <button onclick="window.location='{{ route('vote.show', $second->id) }}'" 
                                        class="flex-1 px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all">
                                    Voter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- 1ère Place -->
                @if($first && $first->votes_count > 0)
                <div class="w-full lg:w-96 order-1 lg:order-2">
                    <div class="podium-card gold-card group relative bg-gradient-to-br from-yellow-200 via-yellow-300 to-yellow-400 rounded-3xl shadow-3xl overflow-hidden transform hover:scale-105 transition-all duration-500 animate-bounce-slow">
                        <div class="absolute top-4 right-4 z-10">
                            <div class="bg-yellow-400 text-yellow-900 px-4 py-2 rounded-full font-bold flex items-center shadow-lg">
                                👑 1ère
                            </div>
                        </div>
                        <div class="absolute top-4 left-4 z-10">
                            <div class="bg-yellow-500 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                LEADER
                            </div>
                        </div>
                        <div class="aspect-[3/4] relative overflow-hidden">
                            <img src="{{ asset('storage/media/' . $first->photo_principale) }}" 
                                 alt="{{ $first->prenom }} {{ $first->nom }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 winner-glow"
                                 style="object-position: center 20%;">
                            <div class="absolute inset-0 bg-gradient-to-t from-yellow-400/30 to-transparent"></div>
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-2xl font-bold text-yellow-900">{{ $first->prenom }} {{ $first->nom }}</h3>
                            <p class="text-yellow-800">{{ $first->pays }}</p>
                            
                            <!-- Barre de progression animée pour 1ère place -->
                            <div class="mt-4">
                                <div class="flex justify-center items-center mb-3">
                                    <span class="text-yellow-900 font-bold text-2xl animate-pulse">{{ $first->percentage ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-yellow-200/50 rounded-full h-6 shadow-inner border border-yellow-300">
                                    <div class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 h-full rounded-full progress-bar-gold shadow-lg relative overflow-hidden" 
                                         style="width: 0%" 
                                         data-percentage="{{ $first->percentage ?? 0 }}">
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 animate-slide"></div>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <span class="text-yellow-800 text-sm font-medium">🥇 LEADER</span>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button onclick="window.location='{{ route('candidates.show', $first->id) }}'" 
                                        class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                    Voir
                                </button>
                                <button onclick="window.location='{{ route('vote.show', $first->id) }}'" 
                                        class="flex-1 px-4 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all font-bold">
                                    Voter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- 3ème Place -->
                @if($third && $third->votes_count > 0)
                <div class="w-full lg:w-80 order-3 lg:order-3">
                    <div class="podium-card bronze-card group relative bg-gradient-to-br from-orange-200 to-orange-400 rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-500 animate-float">
                        <div class="absolute top-4 right-4 z-10">
                            <div class="bg-orange-400 text-orange-900 px-3 py-1 rounded-full font-bold text-sm flex items-center">
                                🥉 3ème
                            </div>
                        </div>
                        <div class="aspect-[3/4] relative overflow-hidden">
                            <img src="{{ asset('storage/media/' . $third->photo_principale) }}" 
                                 alt="{{ $third->prenom }} {{ $third->nom }}" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                 style="object-position: center 20%;">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        </div>
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-orange-900">{{ $third->prenom }} {{ $third->nom }}</h3>
                            <p class="text-orange-700">{{ $third->pays }}</p>
                            
                            <!-- Barre de progression animée pour 3ème place -->
                            <div class="mt-4">
                                <div class="flex justify-center items-center mb-3">
                                    <span class="text-orange-900 font-bold text-xl">{{ $third->percentage ?? 0 }}%</span>
                                </div>
                                <div class="w-full bg-orange-200/50 rounded-full h-5 shadow-inner border border-orange-300">
                                    <div class="bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 h-full rounded-full progress-bar-bronze shadow-lg relative overflow-hidden" 
                                         style="width: 0%" 
                                         data-percentage="{{ $third->percentage ?? 0 }}">
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent transform -skew-x-12"></div>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <span class="text-orange-700 text-sm font-medium">🥉 OUTSIDER</span>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button onclick="window.location='{{ route('candidates.show', $third->id) }}'" 
                                        class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                    Voir
                                </button>
                                <button onclick="window.location='{{ route('vote.show', $third->id) }}'" 
                                        class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-lg hover:from-orange-700 hover:to-orange-800 transition-all">
                                    Voter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- Section Toutes les candidates -->
        <section id="vote-section">
            <h2 class="text-2xl md:text-3xl font-bold text-text-gray-800 mb-8 text-center">
                @if($activeMisses->where('votes_count', '>', 0)->count() >= 3)
                    Découvrez toutes nos candidates
                @else
                    Toutes les candidates
                @endif
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($activeMisses as $index => $candidate)
                    <x-cards.candidate-card :candidate="$candidate" :isTopMiss="false" />
                @endforeach
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des barres de progression du podium
            const progressBars = document.querySelectorAll('.progress-bar-gold, .progress-bar-silver, .progress-bar-bronze');
            
            // Observer pour déclencher les animations quand visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const progressBar = entry.target;
                        const percentage = progressBar.getAttribute('data-percentage');
                        
                        // Définir la variable CSS pour l'animation
                        progressBar.style.setProperty('--target-percentage', percentage + '%');
                        
                        // Démarrer l'animation
                        setTimeout(() => {
                            progressBar.style.width = percentage + '%';
                        }, 100);
                        
                        // Animation du nombre qui compte
                        const percentageText = progressBar.parentElement.parentElement.querySelector('span');
                        if (percentageText && percentageText.textContent.includes('%')) {
                            animateNumber(percentageText, 0, parseFloat(percentage), 2000, '%');
                        }
                        
                        observer.unobserve(progressBar);
                    }
                });
            }, { threshold: 0.5 });
            
            progressBars.forEach(bar => observer.observe(bar));
            
            // Fonction d'animation des nombres
            function animateNumber(element, start, end, duration, suffix = '') {
                const startTime = Date.now();
                const originalText = element.textContent;
                
                function updateNumber() {
                    const elapsed = Date.now() - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Fonction d'easing pour une animation plus fluide
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    const currentValue = start + (end - start) * easeOut;
                    
                    element.textContent = currentValue.toFixed(1) + suffix;
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateNumber);
                    } else {
                        element.textContent = end.toFixed(1) + suffix;
                    }
                }
                
                requestAnimationFrame(updateNumber);
            }
        });
    </script>
@endsection
