@if($transactions->count() > 0)
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-blue-600 text-sm font-medium">Total des transactions</p>
            <p class="text-2xl font-bold text-blue-900 mt-1">{{ $transactions->count() }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <p class="text-green-600 text-sm font-medium">Revenus totaux</p>
            <p class="text-2xl font-bold text-green-900 mt-1">{{ number_format($transactions->sum('montant'), 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <p class="text-purple-600 text-sm font-medium">Montant moyen</p>
            <p class="text-2xl font-bold text-purple-900 mt-1">{{ number_format($transactions->avg('montant'), 0, ',', ' ') }} FCFA</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">
                        ID Transaction
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">
                        Candidate
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">
                        Montant
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">
                        Téléphone
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">
                        Méthode
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">
                        Statut
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-900 uppercase tracking-wider">
                        Date
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">
                                #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->miss)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($transaction->miss->photo_principale)
                                            <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/media/' . $transaction->miss->photo_principale) }}" alt="{{ $transaction->miss->prenom }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-blue-200 flex items-center justify-center">
                                                <span class="text-blue-700 text-xs font-semibold">{{ substr($transaction->miss->prenom, 0, 1) }}{{ substr($transaction->miss->nom, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $transaction->miss->prenom }} {{ $transaction->miss->nom }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: {{ $transaction->miss_id }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-green-600">
                                {{ number_format($transaction->montant, 0, ',', ' ') }} FCFA
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $transaction->numero_telephone ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $transaction->methode_paiement ?? 'Mobile Money' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->statut === 'success' || $transaction->statut === 'completed')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Succès
                                </span>
                            @elseif($transaction->statut === 'pending')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    En attente
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Échoué
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $transaction->created_at ? $transaction->created_at->format('d/m/Y') : 'N/A' }}</div>
                            <div class="text-xs text-gray-400">{{ $transaction->created_at ? $transaction->created_at->format('H:i') : '' }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune transaction</h3>
        <p class="mt-1 text-sm text-gray-500">Aucune transaction n'a été enregistrée pour le moment.</p>
    </div>
@endif

