@extends('layouts.base')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('home') }}" class="inline-flex items-center text-text-gray-600 hover:text-primary-pink mb-6 transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Retour
    </a>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="relative h-96 md:h-[32rem] overflow-hidden">
            <img src="{{ asset('storage/media/' . $miss->photo_principale) }}" alt="{{ $miss->prenom }} {{ $miss->nom }}" class="w-full h-full object-cover" style="object-position: center 15%;" />
        </div>
        <div class="p-6">
            <h1 class="text-3xl md:text-4xl font-bold text-text-gray-900">{{ $miss->prenom }} {{ $miss->nom }}</h1>
            <p class="text-text-gray-600 text-lg mt-1">{{ $miss->ville }} {{ $miss->pays }} • {{ $miss->age }} ans</p>
            <div class="flex gap-2 mt-3 flex-wrap">
                <div class="inline-block bg-bg-pink-100 text-text-pink-700 font-bold py-1 px-3 rounded-full text-sm">
                    {{ $pourcentageCandidate }}% des votes
                </div>
                <div class="inline-block bg-blue-100 text-blue-700 font-bold py-1 px-3 rounded-full text-sm">
                    {{ $nombreVotes }} vote{{ $nombreVotes > 1 ? 's' : '' }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-text-gray-800 mb-4">À propos</h2>
            <p class="text-text-gray-700 leading-relaxed whitespace-pre-line">{{ $miss->bio ?: $miss->presentation_courte ?: 'Aucune biographie disponible pour le moment.' }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-text-gray-800 mb-4">Galerie photos</h2>
            @if($photos->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($photos as $photo)
                        <div class="aspect-square overflow-hidden rounded-lg shadow-sm relative cursor-pointer" onclick="openMediaModal('{{ asset('storage/media/' . $photo->url) }}', 'image')">
                            <img src="{{ asset('storage/media/' . $photo->url) }}" alt="{{ $miss->prenom }} {{ $miss->nom }} photo" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300" style="object-position: center 15%;"/>
                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 opacity-0 hover:opacity-100 transition-opacity pointer-events-none">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-text-gray-600">Aucune photo disponible pour le moment.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mt-8">
        <h2 class="text-2xl font-bold text-text-gray-800 mb-4">Vidéo de présentation</h2>
        @if($video)
            <div class="aspect-w-16 aspect-h-9 relative cursor-pointer" onclick="openMediaModal('{{ asset('storage/media/' . $video->url) }}', 'video')">
                <video class="w-full h-full rounded-lg object-cover" muted preload="metadata">
                    <source src="{{ asset('storage/media/' . $video->url) }}" type="video/{{ pathinfo($video->url, PATHINFO_EXTENSION) }}">
                    Votre navigateur ne supporte pas la vidéo.
                </video>
                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 hover:bg-opacity-50 transition-all pointer-events-none">
                    <div class="text-center text-white">
                        <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <p class="text-sm font-medium">Cliquez pour voir la vidéo</p>
                    </div>
                </div>
            </div>
        @else
            <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center text-text-gray-500">
                Lecteur vidéo (démo)
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mt-8 text-center">
        <h2 class="text-2xl font-bold text-text-gray-800 mb-4">Votez pour {{ $miss->prenom }} {{ $miss->nom }}</h2>
        <p class="text-text-gray-600 mb-6">Soutenez votre candidate favorite</p>
        <x-buttons.primary-button onclick="window.location='{{ route('vote.show', $miss->id) }}'">
            Voter pour elle maintenant
        </x-buttons.primary-button>
    </div>
</div>
@endsection
