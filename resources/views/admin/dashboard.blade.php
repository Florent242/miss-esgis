@extends('layouts.base')
@php
    $titre = 'Dashboard Admin - Miss ESGIS ' . date('Y');
@endphp

@section('title', $titre)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-pink-50 to-orange-50 overflow-x-hidden">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 break-words">
                        Interface Administrateur
                    </h1>
                    <p class="text-gray-600 mt-2">Gestion du concours Miss ESGIS {{ date('Y') }}</p>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg font-semibold transition-all flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>

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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
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
        </div>

        <!-- Onglets -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex flex-wrap -mb-px">
                    <button onclick="showTab('candidates')" id="tab-candidates" class="tab-button w-1/2 md:w-auto py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors border-pink-500 text-pink-600">
                        Candidates
                    </button>
                    <button onclick="showTab('approved')" id="tab-approved" class="tab-button w-1/2 md:w-auto py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Acceptées
                    </button>
                    <button onclick="showTab('ranking')" id="tab-ranking" class="tab-button w-1/2 md:w-auto py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Classement
                    </button>
                    <button onclick="showTab('transactions')" id="tab-transactions" class="tab-button w-1/2 md:w-auto py-4 px-6 text-center border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Transactions
                    </button>
                </nav>
            </div>

            <div class="p-4 md:p-6">
                <!-- Onglet Candidates -->
                <div id="content-candidates" class="tab-content">
                    @include('admin.partials.candidates-table', ['candidates' => $candidates->where('statut', 'pending')])
                </div>

                <!-- Onglet Acceptées -->
                <div id="content-approved" class="tab-content hidden">
                    @include('admin.partials.approved-table', ['candidates' => $candidatesaprouver])
                </div>

                <!-- Onglet Classement -->
                <div id="content-ranking" class="tab-content hidden">
                    @include('admin.partials.ranking-table', ['candidates' => $candidates])
                </div>

                <!-- Onglet Transactions -->
                <div id="content-transactions" class="tab-content hidden">
                    @include('admin.partials.transactions-table', ['transactions' => $transactions])
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        // Masquer tous les contenus
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Réinitialiser tous les boutons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-pink-500', 'text-pink-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Afficher le contenu sélectionné
        document.getElementById('content-' + tabName).classList.remove('hidden');
        
        // Activer le bouton sélectionné
        const activeButton = document.getElementById('tab-' + tabName);
        activeButton.classList.remove('border-transparent', 'text-gray-500');
        activeButton.classList.add('border-pink-500', 'text-pink-600');
    }
</script>
@endsection
