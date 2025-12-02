@props(['candidate', 'isTopMiss' => false])

<div class="bg-white rounded-xl shadow-lg overflow-hidden relative {{ $isTopMiss ? 'border-4 border-yellow-400' : '' }} transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 group candidate-card">
    @if($isTopMiss)
        <div class="absolute top-0 left-0 right-0 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 text-gray-900 px-4 py-2 flex items-center justify-center space-x-2 z-10 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 animate-pulse" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            <span class="font-bold text-sm sm:text-base md:text-lg">👑 Candidate en tête</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 animate-pulse" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        </div>
    @endif

    <div class="candidate-image-container {{ $isTopMiss ? 'mt-12 sm:mt-14' : '' }}">
        <img src="{{ asset('storage/media/' . $candidate->photo_principale) }}" 
             alt="{{ $candidate->prenom }} {{ $candidate->nom }}" 
             class="candidate-image" 
             style="position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; object-fit: cover !important; object-position: center top !important;" />
    </div>
    <div class="p-4 candidate-card-content">
        <div>
            <h3 class="text-xl font-semibold text-text-gray-800 transition-colors duration-300 group-hover:text-primary-pink">{{ $candidate->prenom }} {{ $candidate->nom }}</h3>
            <p class="text-text-gray-600 text-sm">{{ $candidate->pays }}</p>
            <p class="text-text-pink-600 font-bold mt-2">{{ $candidate->percentage ?? 0 }}% des votes</p>
        </div>
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
