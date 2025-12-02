@extends('layouts.base')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête de la page -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-extrabold text-text-gray-900 mb-4">Toutes les Candidates</h1>
        <p class="text-lg text-text-gray-600 mb-4">
            Découvrez toutes nos candidates exceptionnelles et votez pour votre favorite
        </p>
        <div class="inline-block bg-accent-yellow text-text-yellow-900 font-bold py-2 px-4 rounded-full shadow-md">
            {{ $candidates->count() }} candidate{{ $candidates->count() > 1 ? 's' : '' }} active{{ $candidates->count() > 1 ? 's' : '' }}
        </div>
    </div>

    <!-- Grille des candidates avec images uniformisées -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 candidates-grid">
        @forelse($candidates as $index => $candidate)
            <x-cards.candidate-card :candidate="$candidate" :isTopMiss="$index === 0 && $candidate->votes_count > 0" />
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-6xl text-gray-300 mb-4">👑</div>
                <p class="text-center text-text-gray-600 text-lg mb-2">Aucune candidate active pour le moment.</p>
                <p class="text-center text-text-gray-500 text-sm">Revenez bientôt pour découvrir nos futures candidates !</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
