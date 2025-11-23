@extends('layouts.base')

@section('content')
    <div class="container mx-auto px-4 py-8">
        @if (session('success'))
            <div class="mb-6">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold text-text-gray-900 mb-4">REINE ESGIS Bénin {{ date('Y') }} </h1>
            <p class="text-lg text-text-gray-600 mb-6">
                Découvrez les candidates exceptionnelles et votez pour votre favorite
            </p>
            <div
                class="inline-block bg-accent-yellow text-text-yellow-900 font-bold py-3 px-6 rounded-full shadow-md text-lg">
                {{ number_format($totalVotes, 0, ',', ' ') }} votes au total
            </div>
        </div>

        @if ($topMiss)
            <section class="mb-12 border-2 md:border-4 border-primary-pink bg-gradient-pink-light">
                <h2 class="text-2xl font-bold text-text-gray-800 mb-6 flex items-center justify-center md:justify-start">
                    👑
                    Candidate en tête
                </h2>
                <div
                    class="rounded-xl shadow-lg p-6 flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-6 ">
                    <img src="{{ asset('storage/media/' . $topMiss->photo_principale) }}" alt="{{ $topMiss->prenom }} {{ $topMiss->nom }}"
                        class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover shadow-md" />
                    <div class="text-center md:text-left flex-grow">
                        <h3 class="text-2xl font-semibold text-text-gray-800">
                            {{ $topMiss->prenom }} {{ $topMiss->nom }}
                        </h3>
                        <p class="text-text-gray-600">{{ $topMiss->pays }}</p>
                        <p class="text-text-pink-600 font-bold text-lg mt-2">{{ $topMiss->total_votes }} votes</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <x-buttons.secondary-button class="flex-1"
                            onclick="window.location='{{ route('candidates.show', $topMiss->id) }}'">
                            Voir profil
                        </x-buttons.secondary-button>
                        <x-buttons.primary-button class="flex-1"
                            onclick="window.location='{{ route('vote.show', $topMiss->id) }}'">
                            Voter maintenant
                        </x-buttons.primary-button>
                    </div>
                </div>
            </section>
        @endif

        <section id="vote-section">
            <h2 class="text-2xl font-bold text-text-gray-800 mb-6 text-center md:text-left">Toutes les candidates</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($activeMisses as $candidate)
                    <x-cards.candidate-card :candidate="$candidate" />
                @endforeach
            </div>
        </section>
    </div>
@endsection
