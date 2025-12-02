@extends('layouts.admin')

@section('title', 'Dashboard Principal')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Principal</h1>
                    <p class="mt-1 text-sm text-gray-600">Vue d'ensemble du concours Miss ESGIS</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">{{ Auth::guard('admin')->user()->nom }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistiques générales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total votes avec pourcentage -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm">📊</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Votes</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($totalVotes) }}</dd>
                                <dd class="text-sm text-blue-600 font-medium">100% du système</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classement général -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm">🏆</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">En Tête</dt>
                                @if($candidates->first())
                                    <dd class="text-lg font-bold text-gray-900">{{ $candidates->first()->prenom }}</dd>
                                    <dd class="text-sm text-yellow-600 font-medium">{{ $candidates->first()->percentage }}% des votes</dd>
                                @else
                                    <dd class="text-lg font-bold text-gray-900">Aucune candidate</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Candidates actives -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm">👑</span>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Candidates Actives</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $acceptees->count() }}</dd>
                                <dd class="text-sm text-green-600 font-medium">En compétition</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation par onglets -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button class="tab-button border-b-2 border-blue-500 text-blue-600 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap" data-tab="acceptees">
                        Acceptées ({{ $acceptees->count() }})
                    </button>
                    <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap" data-tab="restreintes">
                        Restreintes ({{ $restreintes->count() }})
                    </button>
                    <button class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap" data-tab="rejetees">
                        Rejetées ({{ $rejetees->count() }})
                    </button>
                </nav>
            </div>

            <!-- Contenu des onglets -->
            <div class="p-6">
                <!-- Candidates Acceptées -->
                <div id="tab-acceptees" class="tab-content">
                    <h3 class="text-lg font-semibold text-green-800 mb-4">🟢 Candidates Acceptées</h3>
                    @if($acceptees->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-green-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Candidate</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Pourcentage</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-green-700 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($acceptees as $index => $candidate)
                                    <tr class="hover:bg-green-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-900">
                                            @if($index == 0)
                                                🥇 #{{ $index + 1 }}
                                            @elseif($index == 1)
                                                🥈 #{{ $index + 1 }}
                                            @elseif($index == 2)
                                                🥉 #{{ $index + 1 }}
                                            @else
                                                #{{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/media/' . $candidate->photo_principale) }}" alt="{{ $candidate->nom }}">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $candidate->prenom }} {{ $candidate->nom }}</div>
                                                    <div class="text-sm text-gray-500">{{ $candidate->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-green-600">{{ $candidate->percentage }}%</div>
                                            <div class="text-xs text-gray-500">{{ $candidate->votes_count }} votes</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Aucune candidate acceptée</p>
                        </div>
                    @endif
                </div>

                <!-- Candidates Restreintes -->
                <div id="tab-restreintes" class="tab-content hidden">
                    <h3 class="text-lg font-semibold text-orange-800 mb-4">🟡 Candidates Restreintes</h3>
                    @if($restreintes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-orange-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Candidate</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Pourcentage</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-orange-700 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($restreintes as $index => $candidate)
                                    <tr class="hover:bg-orange-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-orange-900">#{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/media/' . $candidate->photo_principale) }}" alt="{{ $candidate->nom }}">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $candidate->prenom }} {{ $candidate->nom }}</div>
                                                    <div class="text-sm text-gray-500">{{ $candidate->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-orange-600">{{ $candidate->percentage }}%</div>
                                            <div class="text-xs text-gray-500">{{ $candidate->votes_count }} votes</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                Restreinte
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Aucune candidate restreinte</p>
                        </div>
                    @endif
                </div>

                <!-- Candidates Rejetées -->
                <div id="tab-rejetees" class="tab-content hidden">
                    <h3 class="text-lg font-semibold text-red-800 mb-4">🔴 Candidates Rejetées</h3>
                    @if($rejetees->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-red-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Candidate</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Pourcentage</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rejetees as $index => $candidate)
                                    <tr class="hover:bg-red-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-900">#{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/media/' . $candidate->photo_principale) }}" alt="{{ $candidate->nom }}">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $candidate->prenom }} {{ $candidate->nom }}</div>
                                                    <div class="text-sm text-gray-500">{{ $candidate->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-red-600">{{ $candidate->percentage }}%</div>
                                            <div class="text-xs text-gray-500">{{ $candidate->votes_count }} votes</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Rejetée
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Aucune candidate rejetée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion des onglets
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Réinitialiser tous les boutons
            tabButtons.forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Activer le bouton cliqué
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-blue-600');
            
            // Masquer tous les contenus
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Afficher le contenu correspondant
            document.getElementById('tab-' + tabName).classList.remove('hidden');
        });
    });
});
</script>
@endsection