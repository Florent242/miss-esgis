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
        <img src="{{ asset('storage/media/' . $miss->photo_principale) }}" alt="{{ $miss->prenom }} {{ $miss->nom }}" class="w-full h-64 md:h-96 object-cover" />
        <div class="p-6">
            <h1 class="text-3xl md:text-4xl font-bold text-text-gray-900">{{ $miss->prenom }} {{ $miss->nom }}</h1>
            <p class="text-text-gray-600 text-lg mt-1">{{ $miss->ville }}, {{ $miss->pays }} • {{ $miss->age }} ans</p>
            <div class="inline-block bg-bg-pink-100 text-text-pink-700 font-bold py-1 px-3 rounded-full text-sm mt-3">
                {{ $miss->total_votes }} votes
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-text-gray-800 mb-4">À propos</h2>
            <p class="text-text-gray-700 leading-relaxed">{{ $miss->presentation_courte }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-text-gray-800 mb-4">Galerie photos</h2>
            @if($photos->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($photos as $photo)
                        <img src="{{ asset('storage/media/' . $photo->url) }}" alt="{{ $miss->prenom }} {{ $miss->nom }} photo" class="w-full h-full object-cover rounded-lg shadow-sm"/>
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
            <div class="aspect-w-16 aspect-h-9">
                <iframe
                    class="w-full h-full rounded-lg object-cover"
                    src="{{asset('storage/media/' . $video->url)}}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
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
