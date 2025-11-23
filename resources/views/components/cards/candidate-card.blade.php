@props(['candidate', 'isTopMiss' => false])

<div class="bg-white rounded-xl shadow-lg overflow-hidden relative {{ $isTopMiss ? 'border-4 border-primary-pink' : '' }} transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 group">
    @if($isTopMiss)
        <div class="absolute top-0 left-0 bg-accent-yellow text-text-yellow-900 text-xs font-bold px-3 py-1 rounded-br-lg flex items-center space-x-1 z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>Candidate en tête</span>
        </div>
    @elseif($candidate->statut === 'active')
        <div class="absolute top-0 right-0 bg-primary-pink text-white text-xs font-bold px-3 py-1 rounded-bl-lg flex items-center space-x-1 z-10">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>Top Miss</span>
        </div>
    @endif

    <div class="overflow-hidden">
        <img src="{{ asset('storage/media/' . $candidate->photo_principale) }}" alt="{{ $candidate->prenom }} {{ $candidate->nom }}" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110" />
    </div>
    <div class="p-4">
        <h3 class="text-xl font-semibold text-text-gray-800 transition-colors duration-300 group-hover:text-primary-pink">{{ $candidate->prenom }} {{ $candidate->nom }}</h3>
        <p class="text-text-gray-600 text-sm">{{ $candidate->pays }}</p>
        <p class="text-text-pink-600 font-bold mt-2">{{ $candidate->total_votes }} votes</p>
        <div class="mt-4 flex flex-col sm:flex-row gap-3">
            <x-buttons.secondary-button class="flex-1" onclick="window.location='{{ route('candidates.show', $candidate->id) }}'">
                Voir profil
            </x-buttons.secondary-button>
            <x-buttons.primary-button class="flex-1" onclick="window.location='{{ route('vote.show', $candidate->id) }}'">
                Voter maintenant
            </x-buttons.primary-button>
        </div>
    </div>
</div>
