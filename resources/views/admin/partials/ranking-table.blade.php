<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-medium uppercase tracking-wider">Rang</th>
                    <th class="px-6 py-4 text-left text-sm font-medium uppercase tracking-wider">Photo</th>
                    <th class="px-6 py-4 text-left text-sm font-medium uppercase tracking-wider">Candidate</th>
                    <th class="px-6 py-4 text-left text-sm font-medium uppercase tracking-wider">Pays</th>
                    <th class="px-6 py-4 text-left text-sm font-medium uppercase tracking-wider">Total Votes</th>
                    <th class="px-6 py-4 text-left text-sm font-medium uppercase tracking-wider">Revenus (XOF)</th>
                    <th class="px-6 py-4 text-left text-sm font-medium uppercase tracking-wider">Statut</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $rankedCandidates = $candidates->sortByDesc('total_votes');
                @endphp
                
                @forelse($rankedCandidates as $index => $candidate)
                    @php
                        $rank = $index + 1;
                        $revenus = $candidate->total_votes * 100; // 100 XOF par vote
                        $rankClass = match($rank) {
                            1 => 'bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-400',
                            2 => 'bg-gradient-to-r from-gray-50 to-gray-100 border-l-4 border-gray-400',  
                            3 => 'bg-gradient-to-r from-orange-50 to-orange-100 border-l-4 border-orange-400',
                            default => 'hover:bg-gray-50'
                        };
                    @endphp
                    
                    <tr class="{{ $rankClass }} transition-colors duration-200">
                        <!-- Rang -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($rank === 1)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-400 text-yellow-900">
                                        👑 #{{ $rank }}
                                    </span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-gray-400 text-gray-900">
                                        🥈 #{{ $rank }}
                                    </span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-orange-400 text-orange-900">
                                        🥉 #{{ $rank }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        #{{ $rank }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Photo -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200">
                                @if($candidate->photo_principale)
                                    <img src="{{ asset('storage/media/' . $candidate->photo_principale) }}" 
                                         alt="{{ $candidate->prenom }} {{ $candidate->nom }}"
                                         class="w-full h-full object-cover"
                                         style="object-position: center 20%;">
                                @else
                                    <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Nom -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $candidate->prenom }} {{ $candidate->nom }}</div>
                            <div class="text-sm text-gray-500">{{ $candidate->age }} ans</div>
                        </td>
                        
                        <!-- Pays -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $candidate->pays }}
                        </td>
                        
                        <!-- Total Votes -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-purple-600">{{ number_format($candidate->total_votes) }}</div>
                        </td>
                        
                        <!-- Revenus -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-green-600">{{ number_format($revenus) }} XOF</div>
                        </td>
                        
                        <!-- Statut -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($candidate->statut === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @elseif($candidate->statut === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    En attente
                                </span>
                            @elseif($candidate->statut === 'restricted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    Restreinte
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Rejetée
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center space-y-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <p>Aucune candidate pour le moment</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
