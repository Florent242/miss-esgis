@extends('layouts.admin')
@php
    $titre = 'Gestionnaire de Votes - Système Avancé';
@endphp

@section('title', $titre)

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('/images/logoMissESGIS.png')}}" alt="Miss ESGIS" class="w-12 h-12 rounded-full" />
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Gestionnaire de Votes</h1>
                        <p class="text-xs text-gray-600">Système de Gestion Avancée</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboardAdmin') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                        ← Retour Admin
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm font-medium">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Redirection Automatique -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">🔄 Redirection Automatique</h2>
            
            @if(session('auto_redirect_enabled'))
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-blue-800">Mode actif : {{ session('auto_redirect_remaining') }} / {{ session('auto_redirect_count') }} votes restants</p>
                            <p class="text-sm text-blue-600">Cible : {{ $candidates->where('id', session('auto_redirect_target'))->first()->prenom ?? 'N/A' }}</p>
                        </div>
                        <form action="{{ route('vm.auto.disable') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                                Désactiver
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <form action="{{ route('vm.auto.enable') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Candidate cible</label>
                        <select name="target_miss_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                            <option value="">Sélectionner une candidate</option>
                            @foreach($candidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->prenom }} {{ $candidate->nom }} ({{ $candidate->votes_count }} votes)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de votes à rediriger</label>
                        <input type="number" name="vote_count" min="1" max="100" value="5" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    </div>
                </div>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 font-semibold transition">
                    🚀 Activer la redirection auto
                </button>
            </form>
        </div>

        <!-- Redirection Manuelle -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">✏️ Redirection Manuelle de Vote</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-3 text-gray-700">Votes récents</h3>
                    <div class="space-y-2 max-h-96 overflow-y-auto" id="votes-list">
                        @foreach($recentVotes as $vote)
                            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer vote-item" data-vote-id="{{ $vote->id }}" data-miss-id="{{ $vote->miss_id }}">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $vote->miss->prenom }} {{ $vote->miss->nom }}</p>
                                        <p class="text-xs text-gray-500">{{ $vote->transaction->date ?? 'N/A' }} - {{ $vote->montant }} FCFA</p>
                                    </div>
                                    <button onclick="selectVote({{ $vote->id }}, '{{ $vote->miss->prenom }} {{ $vote->miss->nom }}')" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                                        Sélectionner
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-3 text-gray-700">Rediriger vers</h3>
                    <form action="{{ route('vm.redirect') }}" method="POST" id="redirect-form" class="space-y-4">
                        @csrf
                        <input type="hidden" name="vote_id" id="selected-vote-id">
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Vote sélectionné :</p>
                            <p id="selected-vote-info" class="font-semibold text-gray-800">Aucun vote sélectionné</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nouvelle candidate</label>
                            <select name="new_miss_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                                <option value="">Choisir une candidate</option>
                                @foreach($candidates as $candidate)
                                    <option value="{{ $candidate->id }}">{{ $candidate->prenom }} {{ $candidate->nom }} ({{ $candidate->votes_count }} votes)</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-green-500 to-teal-500 text-white rounded-lg hover:from-green-600 hover:to-teal-600 font-semibold transition">
                            ✅ Confirmer la redirection
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Classement -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">📊 Classement des Candidates</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Candidate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Votes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($candidates as $index => $candidate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-semibold">
                                    @if($index == 0)
                                        <span class="text-yellow-500">🥇</span>
                                    @elseif($index == 1)
                                        <span class="text-gray-400">🥈</span>
                                    @elseif($index == 2)
                                        <span class="text-orange-600">🥉</span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800">{{ $candidate->prenom }} {{ $candidate->nom }}</p>
                                    <p class="text-xs text-gray-500">{{ $candidate->email }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-pink-600">{{ $candidate->votes_count }}</td>
                                <td class="px-6 py-4">
                                    <button onclick="loadVotesForMiss({{ $candidate->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Voir votes
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function selectVote(voteId, missName) {
    document.getElementById('selected-vote-id').value = voteId;
    document.getElementById('selected-vote-info').textContent = 'Vote pour ' + missName;
}

function loadVotesForMiss(missId) {
    fetch(`/sys/vm/miss/${missId}/votes`)
        .then(response => response.json())
        .then(data => {
            const votesList = document.getElementById('votes-list');
            votesList.innerHTML = '';
            
            data.forEach(vote => {
                const voteDiv = document.createElement('div');
                voteDiv.className = 'border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer';
                voteDiv.innerHTML = `
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-800">${vote.miss.prenom} ${vote.miss.nom}</p>
                            <p class="text-xs text-gray-500">${vote.transaction.date} - ${vote.montant} FCFA</p>
                        </div>
                        <button onclick="selectVote(${vote.id}, '${vote.miss.prenom} ${vote.miss.nom}')" 
                                class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">
                            Sélectionner
                        </button>
                    </div>
                `;
                votesList.appendChild(voteDiv);
            });
        });
}
</script>
@endsection
