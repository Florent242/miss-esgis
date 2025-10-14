@extends('layouts.base')

@section('content')
<div class="container mx-auto px-4 py-8 text-center">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-md mx-auto">
        <div class="text-green-500 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-text-gray-900 mb-4">Vote enregistré avec succès !</h1>
        <p class="text-text-gray-700 text-lg mb-6">
            Merci d'avoir voté pour <span class="font-semibold">{{ $miss->prenom }} {{ $miss->nom }}</span>. Votre soutien est précieux !
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <x-buttons.primary-button onclick="window.location='{{ route('home') }}'">
                Retour à l'accueil
            </x-buttons.primary-button>
            <x-buttons.secondary-button onclick="window.location='{{ route('candidates.show', $miss->id) }}'">
                Voir le profil de {{ $miss->prenom }}
            </x-buttons.secondary-button>
        </div>
    </div>
</div>
@endsection
