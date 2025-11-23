@if($candidates->count() > 0)
    <div class="overflow-x-auto -mx-4 md:mx-0">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow-md rounded-lg border border-yellow-200">
                <table class="min-w-full divide-y divide-yellow-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-bold text-yellow-800 uppercase tracking-wider">
                                Photo
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-bold text-yellow-800 uppercase tracking-wider">
                                Nom & Prénom
                            </th>
                            <th scope="col" class="hidden md:table-cell px-6 py-3 text-left text-xs font-bold text-yellow-800 uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="hidden sm:table-cell px-3 md:px-6 py-3 text-left text-xs font-bold text-yellow-800 uppercase tracking-wider">
                                Téléphone
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-left text-xs font-bold text-yellow-800 uppercase tracking-wider">
                                Votes
                            </th>
                            <th scope="col" class="px-3 md:px-6 py-3 text-center text-xs font-bold text-yellow-800 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-yellow-100">
                        @foreach($candidates as $candidate)
                            <tr class="hover:bg-yellow-50 transition-colors">
                                <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                                    <img src="{{ asset('storage/media/' . basename($candidate->photo_principale)) }}" 
                                         alt="{{ $candidate->prenom }}" 
                                         class="w-12 h-12 md:w-16 md:h-16 rounded-full object-cover border-2 border-yellow-300">
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $candidate->nom }}</div>
                                    <div class="text-sm text-gray-600">{{ $candidate->prenom }}</div>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4">
                                    <div class="text-sm text-gray-900 break-all">{{ $candidate->email }}</div>
                                </td>
                                <td class="hidden sm:table-cell px-3 md:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $candidate->telephone }}</div>
                                </td>
                                <td class="px-3 md:px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 md:px-3 py-1 rounded-full text-xs md:text-sm font-semibold bg-purple-100 text-purple-800">
                                        <svg class="w-3 h-3 md:w-4 md:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        {{ $candidate->votes_count }}
                                    </span>
                                </td>
                                <td class="px-3 md:px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                                        <!-- Bouton Voir détails -->
                                        <button onclick="showCandidateDetails({{ $candidate->id }})" 
                                                class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                                            <svg class="w-3 h-3 md:w-4 md:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="hidden sm:inline">Détails</span>
                                        </button>

                                        <!-- Bouton Réactiver -->
                                        <form action="{{ route('admin.activate', $candidate->id) }}" method="POST" class="w-full sm:w-auto">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Voulez-vous réactiver {{ $candidate->prenom }} {{ $candidate->nom }} ?')"
                                                    class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                                                <svg class="w-3 h-3 md:w-4 md:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="hidden sm:inline">Réactiver</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="text-center py-12 bg-yellow-50 rounded-xl border-2 border-dashed border-yellow-300">
        <svg class="mx-auto h-16 w-16 text-yellow-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Aucune candidate restreinte</h3>
        <p class="text-yellow-600">Toutes les candidates actives sont disponibles !</p>
    </div>
@endif
