@extends('layouts.admin')
@php
    $titre = 'Dashboard Admin - Reine ESGIS ' . date('Y');
@endphp

@section('title', $titre)

@section('content')
<div class="min-h-screen">
    <!-- Header avec menu hamburger -->
    <header class="bg-white shadow-md sticky top-0 z-[100]">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Menu Hamburger Admin -->
                    <button id="admin-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-6 h-6 flex flex-col justify-center space-y-1.5">
                            <span class="block w-full h-0.5 bg-gray-700 transition-all duration-300" id="admin-line-1"></span>
                            <span class="block w-full h-0.5 bg-gray-700 transition-all duration-300" id="admin-line-2"></span>
                            <span class="block w-full h-0.5 bg-gray-700 transition-all duration-300" id="admin-line-3"></span>
                        </div>
                    </button>
                    
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('/images/logoMissESGIS.png')}}" alt="Miss ESGIS" class="w-12 h-12 rounded-full" />
                        <div class="hidden md:block">
                            <h1 class="text-xl font-bold text-gray-800">Admin Panel</h1>
                            <p class="text-xs text-gray-600">Reine ESGIS {{ date('Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Desktop -->
                <nav class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Accueil
                    </a>
                    <button onclick="showTab('candidates')" class="px-4 py-2 rounded-lg hover:bg-pink-50 hover:text-pink-600 transition-colors text-sm font-medium">
                        En attente
                    </button>
                    <button onclick="showTab('approved')" class="px-4 py-2 rounded-lg hover:bg-green-50 hover:text-green-600 transition-colors text-sm font-medium">
                        Acceptées
                    </button>
                    <button onclick="showTab('rejected')" class="px-4 py-2 rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors text-sm font-medium">
                        Rejetées
                    </button>
                    <button onclick="showTab('restricted')" class="px-4 py-2 rounded-lg hover:bg-yellow-50 hover:text-yellow-600 transition-colors text-sm font-medium">
                        Restreintes
                    </button>
                    <button onclick="showTab('ranking')" class="px-4 py-2 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors text-sm font-medium">
                        Classement
                    </button>
                    <button onclick="showTab('transactions')" class="px-4 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors text-sm font-medium">
                        Transactions
                    </button>
                </nav>

                <div class="flex items-center space-x-2">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition-all flex items-center text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="hidden md:inline">Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Menu Mobile Overlay Admin -->
    <div id="admin-overlay" class="fixed inset-0 bg-black/60 opacity-0 pointer-events-none md:hidden z-[90] transition-opacity duration-300"></div>

    <!-- Menu Mobile Drawer Admin -->
    <div id="admin-drawer" class="fixed left-0 top-0 h-screen w-80 max-w-[85vw] bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 md:hidden z-[95] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Navigation</h2>
                <button id="admin-close-btn" class="p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('home') }}" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Accueil Site</span>
                </a>

                <button onclick="showTab('candidates'); closeAdminMenu();" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>En attente</span>
                </button>
                
                <button onclick="showTab('approved'); closeAdminMenu();" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-green-50 hover:bg-green-100 text-green-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Acceptées</span>
                </button>
                
                <button onclick="showTab('rejected'); closeAdminMenu();" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Rejetées</span>
                </button>
                
                <button onclick="showTab('restricted'); closeAdminMenu();" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Restreintes</span>
                </button>
                
                <button onclick="showTab('ranking'); closeAdminMenu();" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>Classement</span>
                </button>
                
                <button onclick="showTab('transactions'); closeAdminMenu();" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Transactions</span>
                </button>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-7xl">

        <!-- Alertes -->
        @if (session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/50 rounded-xl p-4 backdrop-blur-sm animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6 mb-8">
            <!-- Candidates totales -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-pink-500">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">Candidates Totales</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-pink-600 mt-2">{{ count($candidates) }}</h3>
                    </div>
                    <div class="bg-pink-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Votes totaux -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">Votes Totaux</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-purple-600 mt-2">{{ $candidates->sum('votes_count') }}</h3>
                    </div>
                    <div class="bg-purple-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Revenus -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">Revenus</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($transactions->sum('montant'), 0, ',', ' ') }} FCFA</h3>
                    </div>
                    <div class="bg-green-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- En attente -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">En Attente</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-orange-600 mt-2">{{ count($candidates->where('statut', 'pending')) }}</h3>
                    </div>
                    <div class="bg-orange-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Restreintes -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">Restreintes</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-yellow-600 mt-2">{{ count($candidates->where('statut', 'restricted')) }}</h3>
                    </div>
                    <div class="bg-yellow-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 md:p-6">
                <!-- Onglet En attente -->
                <div id="content-candidates" class="tab-content">
                    <h2 class="text-2xl font-bold text-orange-700 mb-4">Candidates en attente de validation</h2>
                    @include('admin.partials.candidates-table', ['candidates' => $candidates->where('statut', 'pending')])
                </div>

                <!-- Onglet Acceptées -->
                <div id="content-approved" class="tab-content hidden">
                    <h2 class="text-2xl font-bold text-green-700 mb-4">Candidates acceptées</h2>
                    @include('admin.partials.approved-table', ['candidates' => $candidatesaprouver])
                </div>

                <!-- Onglet Rejetées -->
                <div id="content-rejected" class="tab-content hidden">
                    <h2 class="text-2xl font-bold text-red-700 mb-4">Candidates rejetées</h2>
                    @include('admin.partials.rejected-table', ['candidates' => $candidates->where('statut', 'reject')])
                </div>

                <!-- Onglet Restreintes -->
                <div id="content-restricted" class="tab-content hidden">
                    <h2 class="text-2xl font-bold text-yellow-700 mb-4">Candidates restreintes</h2>
                    @include('admin.partials.restricted-table', ['candidates' => $candidates->where('statut', 'restricted')])
                </div>

                <!-- Onglet Classement -->
                <div id="content-ranking" class="tab-content hidden">
                    <h2 class="text-2xl font-bold text-purple-700 mb-4">Classement des candidates</h2>
                    @include('admin.partials.ranking-table', ['candidates' => $candidates])
                </div>

                <!-- Onglet Transactions -->
                <div id="content-transactions" class="tab-content hidden">
                    <h2 class="text-2xl font-bold text-blue-700 mb-4">Historique des transactions</h2>
                    @include('admin.partials.transactions-table', ['transactions' => $transactions])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Menu hamburger admin
    const adminMenuBtn = document.getElementById('admin-menu-btn');
    const adminCloseBtn = document.getElementById('admin-close-btn');
    const adminOverlay = document.getElementById('admin-overlay');
    const adminDrawer = document.getElementById('admin-drawer');
    const adminLine1 = document.getElementById('admin-line-1');
    const adminLine2 = document.getElementById('admin-line-2');
    const adminLine3 = document.getElementById('admin-line-3');
    let adminMenuOpen = false;

    function openAdminMenu() {
        adminMenuOpen = true;
        adminDrawer.classList.remove('-translate-x-full');
        adminOverlay.classList.remove('pointer-events-none', 'opacity-0');
        adminOverlay.classList.add('opacity-100');
        document.body.classList.add('overflow-hidden');
        
        // Animation hamburger to X
        adminLine1.style.transform = 'rotate(45deg) translateY(7px)';
        adminLine2.style.opacity = '0';
        adminLine3.style.transform = 'rotate(-45deg) translateY(-7px)';
    }

    function closeAdminMenu() {
        adminMenuOpen = false;
        adminDrawer.classList.add('-translate-x-full');
        adminOverlay.classList.add('pointer-events-none', 'opacity-0');
        adminOverlay.classList.remove('opacity-100');
        document.body.classList.remove('overflow-hidden');
        
        // Reset hamburger
        adminLine1.style.transform = 'none';
        adminLine2.style.opacity = '1';
        adminLine3.style.transform = 'none';
    }

    function toggleAdminMenu() {
        adminMenuOpen ? closeAdminMenu() : openAdminMenu();
    }

    adminMenuBtn?.addEventListener('click', toggleAdminMenu);
    adminCloseBtn?.addEventListener('click', closeAdminMenu);
    adminOverlay?.addEventListener('click', closeAdminMenu);

    // Fermer avec ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && adminMenuOpen) closeAdminMenu();
    });

    // Gestion des onglets
    function showTab(tabName) {
        // Masquer tous les contenus
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Afficher le contenu sélectionné
        const selectedContent = document.getElementById(`content-${tabName}`);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }
    }

    // Fonction pour afficher les détails d'une candidate
    function showCandidateDetails(candidateId) {
        // Récupérer les données de la candidate via AJAX
        fetch(`/api/candidate/${candidateId}`)
            .then(response => response.json())
            .then(data => {
                // Créer et afficher une modale avec les détails
                const modal = document.createElement('div');
                modal.id = 'candidate-modal';
                modal.className = 'fixed inset-0 z-[200] overflow-y-auto';
                modal.innerHTML = `
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="document.getElementById('candidate-modal').remove()"></div>
                        
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                            <div class="bg-gradient-to-r from-pink-500 to-purple-600 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-bold text-white">Détails de la Candidate</h3>
                                    <button onclick="document.getElementById('candidate-modal').remove()" class="text-white hover:text-gray-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-white px-6 py-6 max-h-[70vh] overflow-y-auto">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Photo -->
                                    <div class="text-center">
                                        <img src="/storage/media/${data.photo_principale}" alt="${data.prenom}" class="w-48 h-48 rounded-full mx-auto object-cover shadow-lg">
                                        <h4 class="mt-4 text-2xl font-bold text-gray-900">${data.prenom} ${data.nom}</h4>
                                        <p class="text-gray-600">Miss ESGIS ${new Date(data.date_inscription).getFullYear()}</p>
                                    </div>
                                    
                                    <!-- Informations -->
                                    <div class="space-y-3">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Email</label>
                                            <p class="text-gray-900">${data.email || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Téléphone</label>
                                            <p class="text-gray-900">${data.telephone || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Âge</label>
                                            <p class="text-gray-900">${data.age} ans</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Pays</label>
                                            <p class="text-gray-900">${data.pays || 'N/A'}</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Votes</label>
                                            <p class="text-gray-900 font-bold text-lg">${data.votes_count || 0} votes</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Statut</label>
                                            <p class="text-gray-900"><span class="px-2 py-1 rounded-full text-xs ${data.statut === 'active' ? 'bg-green-100 text-green-800' : data.statut === 'pending' ? 'bg-orange-100 text-orange-800' : data.statut === 'restricted' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800'}">${data.statut}</span></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Biographie -->
                                ${data.bio ? `
                                <div class="mt-6">
                                    <label class="text-sm font-medium text-gray-500">Biographie</label>
                                    <p class="text-gray-900 mt-2">${data.bio}</p>
                                </div>
                                ` : ''}
                                
                                <!-- Galerie -->
                                ${data.photo1 || data.photo2 || data.photo3 ? `
                                <div class="mt-6">
                                    <label class="text-sm font-medium text-gray-500 mb-2 block">Galerie</label>
                                    <div class="grid grid-cols-3 gap-4">
                                        ${data.photo1 ? `<img src="/storage/media/${data.photo1}" class="w-full h-32 object-cover rounded-lg">` : ''}
                                        ${data.photo2 ? `<img src="/storage/media/${data.photo2}" class="w-full h-32 object-cover rounded-lg">` : ''}
                                        ${data.photo3 ? `<img src="/storage/media/${data.photo3}" class="w-full h-32 object-cover rounded-lg">` : ''}
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                            
                            <div class="bg-gray-50 px-6 py-4">
                                <button onclick="document.getElementById('candidate-modal').remove()" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition-all">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement des détails');
            });
    }
</script>
@endpush
