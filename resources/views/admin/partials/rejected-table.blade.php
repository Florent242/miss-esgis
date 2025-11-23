@if($candidates->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-red-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider">
                        Candidate
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider">
                        Téléphone
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider">
                        Date d'inscription
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-red-900 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($candidates as $candidate)
                    <tr class="hover:bg-red-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($candidate->photo_principale)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/media/' . $candidate->photo_principale) }}" alt="{{ $candidate->prenom }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-red-200 flex items-center justify-center">
                                            <span class="text-red-700 font-semibold">{{ substr($candidate->prenom, 0, 1) }}{{ substr($candidate->nom, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $candidate->prenom }} {{ $candidate->nom }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Âge: {{ \Carbon\Carbon::parse($candidate->date_naissance)->age }} ans
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $candidate->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $candidate->telephone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $candidate->date_inscription ? $candidate->date_inscription->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center items-center space-x-2">
                                <!-- Bouton Réapprouver -->
                                <form action="{{ route('admin.approve', $candidate->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-semibold transition-all flex items-center" title="Réapprouver">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Réapprouver
                                    </button>
                                </form>

                                <!-- Bouton Voir détails -->
                                <button onclick="showCandidateDetails({{ $candidate->id }})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-semibold transition-all" title="Voir les détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune candidate rejetée</h3>
        <p class="mt-1 text-sm text-gray-500">Toutes les candidates sont soit en attente soit approuvées.</p>
    </div>
@endif
