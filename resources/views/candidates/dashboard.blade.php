@extends('layouts.base')
@php
    $titre = 'Dashboard admin - Miss ESGIS ' . date('Y');
@endphp

@section('title', $titre)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-orange-50 to-pink-50 overflow-x-hidden">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- En-tête -->
        <section class="text-center mb-8 animate-fade-in">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4 break-words">
                Espace Miss - {{ $candidate->nom }} {{ $candidate->prenom }}
            </h1>
            <div class="flex justify-center gap-4 flex-wrap">
                <span class="bg-gradient-to-r from-pink-500 via-pink-400 to-orange-400 text-white px-6 py-2 rounded-full font-bold shadow-lg text-sm md:text-base">
                    #{{ $rang }} @if ($totalcandidates > 0) sur {{ $totalcandidates }} @endif
                </span>
                <span class="bg-orange-400 text-white px-6 py-2 rounded-full font-bold shadow-lg text-sm md:text-base">
                    {{ $candidate->votes_count }} {{ $candidate->votes_count > 1 ? 'votes' : 'vote' }}
                </span>
            </div>
        </section>

        <!-- Statistiques -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <!-- Votes -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">Total Votes</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-pink-600 mt-2 break-words">{{ $candidate->votes_count }}</h3>
                    </div>
                    <div class="bg-pink-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-pink-600 md:w-6 md:h-6">
                            <path d="m9 12 2 2 4-4"></path>
                            <path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z"></path>
                            <path d="M22 19H2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Classement -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">Classement</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-orange-600 mt-2 break-words">#{{ $rang }}</h3>
                    </div>
                    <div class="bg-orange-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-orange-600 md:w-6 md:h-6">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Photos -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">Photos</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-pink-600 mt-2 break-words">{{ $candidate->photos_count }}</h3>
                    </div>
                    <div class="bg-pink-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-pink-600 md:w-6 md:h-6">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Vidéos -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-600 text-xs md:text-sm font-medium truncate">Vidéos</p>
                        <h3 class="text-2xl md:text-3xl font-bold text-orange-600 mt-2 break-words">{{ $candidate->videos_count }}</h3>
                    </div>
                    <div class="bg-orange-100 p-3 md:p-4 rounded-full flex-shrink-0 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-orange-600 md:w-6 md:h-6">
                            <path d="m22 8-6 4 6 4V8Z"/>
                            <rect width="14" height="12" x="2" y="6" rx="2" ry="2"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Formulaires -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-8 w-full">
            @if ($candidate->photos_count < 4 || $candidate->videos_count == 0)
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 w-full">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6 break-words">Gérer vos médias</h2>
                <form action="/addmedia" method="post" enctype="multipart/form-data" class="space-y-4 md:space-y-6">
                    @csrf
                    
                    @if ($candidate->photos_count < 4)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Ajouter une photo</h3>
                        <label for="photo" class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-pink-500 to-orange-400 text-white rounded-lg cursor-pointer hover:from-pink-600 hover:to-orange-500 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span id="spanphoto">Sélectionner une photo</span>
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                        <div id="photo-preview" class="mt-4 hidden">
                            <img id="photo-preview-img" src="" alt="Aperçu photo" class="w-full max-w-md mx-auto rounded-lg shadow-md">
                        </div>
                        <p id="photoerror" class="text-red-500 text-sm mt-2">
                            @error('photo') {{ $message }} @enderror
                        </p>
                    </div>
                    @endif

                    @if ($candidate->videos_count == 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Ajouter une vidéo</h3>
                        <label for="video" class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-pink-500 to-orange-400 text-white rounded-lg cursor-pointer hover:from-pink-600 hover:to-orange-500 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span id="spanvideo">Sélectionner une vidéo</span>
                        </label>
                        <input type="file" name="video" id="video" accept="video/*" class="hidden">
                        <div id="video-preview" class="mt-4 hidden">
                            <video 
                                id="video-preview-player" 
                                controls 
                                preload="metadata"
                                class="w-full max-w-md mx-auto rounded-lg shadow-md"
                            >
                                <source id="video-preview-source" src="" type="">
                            </video>
                        </div>
                        <p id="videoerror" class="text-red-500 text-sm mt-2">
                            @error('video') {{ $message }} @enderror
                        </p>
                    </div>
                    @endif

                    <button type="submit" id="soumis" class="w-full bg-gradient-to-r from-pink-500 via-pink-400 to-orange-400 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                        Ajouter
                    </button>
                </form>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 w-full">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6 break-words">Informations du profil</h2>
                <form action="/updateinfo" method="post" class="space-y-4">
                    @csrf
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" id="nom" value="{{ @old('nom', $candidate->nom ?? '') }}" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm md:text-base">
                        @error('nom') <p class="text-red-500 text-xs md:text-sm mt-1 break-words">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                        <input type="text" name="prenom" id="prenom" value="{{ @old('prenom', $candidate->prenom ?? '') }}" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm md:text-base">
                        @error('prenom') <p class="text-red-500 text-xs md:text-sm mt-1 break-words">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" name="ville" id="ville" value="{{ @old('ville', $candidate->pays ?? '') }}" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm md:text-base">
                        @error('ville') <p class="text-red-500 text-xs md:text-sm mt-1 break-words">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Biographie</label>
                        <textarea name="bio" id="bio" rows="4" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm md:text-base">{{ @old('bio', $candidate->bio ?? '') }}</textarea>
                        @error('bio') <p class="text-red-500 text-xs md:text-sm mt-1 break-words">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-pink-500 via-pink-400 to-orange-400 text-white py-2 md:py-3 rounded-lg font-semibold hover:shadow-lg transition-all text-sm md:text-base">
                        Sauvegarder les modifications
                    </button>
                </form>
            </div>
        </div>

        <!-- Galeries médias -->
        <div class="space-y-4 md:space-y-6 w-full">
            <!-- Photo de profil -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 w-full">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6 break-words">Photo de profil</h2>
                <div class="flex flex-col items-center">
                    <div class="relative group w-full max-w-md">
                        <img id="photprofil" src="{{ asset('storage/media/' . $candidate->photo_principale) }}" alt="Photo de {{ $candidate->nom }} {{ $candidate->prenom }}" class="w-full h-auto rounded-lg shadow-md transition-transform duration-300 group-hover:scale-105 object-cover max-h-96">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-all duration-300"></div>
                    </div>
                    <button id="open-modalphoto" class="mt-4 md:mt-6 bg-gradient-to-r from-pink-500 to-orange-400 text-white px-6 md:px-8 py-2 md:py-3 rounded-lg font-semibold hover:shadow-lg transition-all text-sm md:text-base" data-miss-id="{{ $candidate->id }}">
                        Modifier la photo
                    </button>
                </div>
            </div>

            <!-- Galerie photos -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 w-full overflow-hidden">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6 break-words">Galerie photos</h2>
                @if (count($medias) != 0 && $medias->contains('type', 'photo'))
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 w-full">
                        @foreach ($medias as $media)
                            @if ($media->type === 'photo')
                                <div class="relative group w-full">
                                    <img src="{{ asset('storage/media/' . $media->url) }}" alt="Photo de {{ $candidate->nom }} {{ $candidate->prenom }}" class="w-full h-48 md:h-64 object-cover rounded-lg shadow-md transition-transform duration-300 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-all duration-300"></div>
                                    <button id="open-modalphoto" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-gradient-to-r from-pink-500 to-orange-400 text-white px-4 md:px-6 py-1.5 md:py-2 rounded-lg font-semibold opacity-0 group-hover:opacity-100 transition-all text-xs md:text-sm whitespace-nowrap" data-media-id="{{ $media->id }}">
                                        Modifier
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8 text-sm md:text-base">Vous n'avez pas encore de photos</p>
                @endif
            </div>

            <!-- Vidéo de présentation -->
            <div class="bg-white rounded-xl shadow-lg p-4 md:p-6 w-full overflow-hidden">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6 break-words">Vidéo de présentation</h2>
                @if ($medias->contains('type', 'video'))
                    @foreach ($medias as $media)
                        @if ($media->type === 'video')
                            <div class="flex flex-col items-center">
                                <video 
                                    controls 
                                    preload="metadata"
                                    class="w-full max-w-2xl h-auto rounded-lg shadow-md"
                                    poster="{{ asset('storage/media/' . pathinfo($media->url, PATHINFO_FILENAME) . '_thumb.jpg') }}"
                                    onloadstart="this.volume=0.5"
                                >
                                    <source src="{{ asset('storage/media/' . $media->url) }}" type="video/{{ pathinfo($media->url, PATHINFO_EXTENSION) }}">
                                    Votre navigateur ne prend pas en charge la lecture de vidéo
                                </video>
                                <button id="open-modal" class="mt-4 md:mt-6 bg-gradient-to-r from-pink-500 to-orange-400 text-white px-6 md:px-8 py-2 md:py-3 rounded-lg font-semibold hover:shadow-lg transition-all text-sm md:text-base" data-media-id="{{ $media->id }}">
                                    Modifier la vidéo
                                </button>
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="text-gray-500 text-center py-8 text-sm md:text-base">Vous n'avez pas encore de vidéo de présentation</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Vidéo -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 relative animate-fade-in">
        <button class="close absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Modifier la vidéo</h2>
        <form id="photo-form" action="/modifiermedia" method="post" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div id="infomedia"></div>
            <label for="videomod" class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-pink-500 to-orange-400 text-white rounded-lg cursor-pointer hover:from-pink-600 hover:to-orange-500 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span id="videomod-label">Choisir une vidéo</span>
            </label>
            <input type="file" id="videomod" name="video" accept="video/*" class="hidden">
            <div id="videomod-preview" class="hidden mt-4">
                <video 
                    id="videomod-preview-player" 
                    controls 
                    preload="metadata"
                    class="w-full rounded-lg shadow-md"
                >
                    <source id="videomod-preview-source" src="" type="">
                </video>
            </div>
            <div class="modalviderror text-red-500 text-sm"></div>
            <button type="submit" class="w-full bg-gradient-to-r from-pink-500 via-pink-400 to-orange-400 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                Envoyer
            </button>
        </form>
    </div>
</div>

<!-- Modal Photo -->
<div id="modalphoto" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 relative animate-fade-in">
        <button class="close absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Modifier la photo</h2>
        <form id="photo-form2" action="/modifiermedia" method="post" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div id="photoinfo"></div>
            <label for="modphoto" class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-pink-500 to-orange-400 text-white rounded-lg cursor-pointer hover:from-pink-600 hover:to-orange-500 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span id="modphoto-label">Choisir une photo</span>
            </label>
            <input type="file" id="modphoto" name="photo" accept="image/*" class="hidden">
            <div id="modphoto-preview" class="hidden mt-4">
                <img id="modphoto-preview-img" src="" alt="Aperçu" class="w-full rounded-lg shadow-md">
            </div>
            <div class="modalphterror text-red-500 text-sm"></div>
            <button type="submit" class="w-full bg-gradient-to-r from-pink-500 via-pink-400 to-orange-400 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                Envoyer
            </button>
        </form>
    </div>
</div>

<script>
    // Modal vidéo
    const modal = document.getElementById('modal');
    const openModalButton = document.getElementById("open-modal");
    const closeButtons = document.getElementsByClassName('close');
    const infomedia = document.getElementById("infomedia");

    openModalButton?.addEventListener("click", function() {
        modal.classList.remove('hidden');
        const mediaid = openModalButton.getAttribute("data-media-id");
        infomedia.innerHTML = `<input type="hidden" name="id" value="${mediaid}">`;
    });

    // Modal photo
    const modalphoto = document.getElementById('modalphoto');
    const openmodalphoto = document.querySelectorAll("#open-modalphoto");
    const photoinfo = document.getElementById("photoinfo");

    openmodalphoto.forEach(opmodalphoto => {
        opmodalphoto?.addEventListener("click", function() {
            modalphoto.classList.remove('hidden');
            let photoid = opmodalphoto.getAttribute("data-media-id");
            if (photoid != null) {
                photoinfo.innerHTML = `<input type="hidden" name="id" value="${photoid}">`;
            } else {
                photoid = opmodalphoto.getAttribute("data-miss-id");
                photoinfo.innerHTML = `<input type="hidden" name="idmiss" value="${photoid}">`;
            }
        });
    });

    // Fermeture des modals
    Array.from(closeButtons).forEach(button => {
        button?.addEventListener('click', () => {
            modal.classList.add('hidden');
            modalphoto.classList.add('hidden');
        });
    });

    window?.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
        if (event.target === modalphoto) {
            modalphoto.classList.add('hidden');
        }
    });

    // Validation formulaire vidéo
    const photoForm = document.getElementById('photo-form');
    photoForm?.addEventListener('submit', (event) => {
        const videoInput = document.getElementById('videomod');
        if (videoInput.files.length == 0) {
            event.preventDefault();
            document.querySelector(".modalviderror").innerText = "Sélectionnez une vidéo";
        }
    });

    // Validation formulaire photo
    const photoForm2 = document.getElementById('photo-form2');
    photoForm2?.addEventListener('submit', (event) => {
        const photoInput = document.getElementById('modphoto');
        if (photoInput.files.length == 0) {
            event.preventDefault();
            document.querySelector(".modalphterror").innerText = "Sélectionnez une photo";
        }
    });

    // Gestion upload médias
    let button = document.getElementById("soumis");
    let photo = document.getElementById("photo");
    let video = document.getElementById("video");
    let spanvideo = document.getElementById("spanvideo");
    let spanphoto = document.getElementById("spanphoto");
    let videoerror = document.getElementById("videoerror");
    let photoerror = document.getElementById("photoerror");

    if (button) {
        button?.addEventListener("click", function(e) {
            if ((photo && photo.files.length == 0) && (video && video.files.length == 0)) {
                e.preventDefault();
                if (photo && photo.files.length == 0) {
                    photoerror.innerText = "Sélectionnez une photo";
                }
                if (video && video.files.length == 0) {
                    videoerror.innerText = "Sélectionnez une vidéo";
                }
            }
        });
    }

    if (photo) {
        photo?.addEventListener('change', () => {
            if (photo && photo.files.length > 0) {
                spanphoto.innerText = "Photo sélectionnée ✓";
                spanphoto.classList.add('text-green-600', 'font-semibold');
                photoerror.innerText = "";
                
                // Afficher l'aperçu de l'image
                const file = photo.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewContainer = document.getElementById('photo-preview');
                    const previewImg = document.getElementById('photo-preview-img');
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                spanphoto.innerText = "Sélectionner une photo";
                spanphoto.classList.remove('text-green-600', 'font-semibold');
                document.getElementById('photo-preview').classList.add('hidden');
            }
        });
    }

    if (video) {
        video?.addEventListener('change', () => {
            if (video && video.files.length > 0) {
                spanvideo.innerText = "Vidéo sélectionnée ✓";
                spanvideo.classList.add('text-green-600', 'font-semibold');
                videoerror.innerText = "";
                
                // Afficher l'aperçu de la vidéo
                const file = video.files[0];
                const previewContainer = document.getElementById('video-preview');
                const previewPlayer = document.getElementById('video-preview-player');
                const previewSource = document.getElementById('video-preview-source');
                
                const fileURL = URL.createObjectURL(file);
                previewSource.src = fileURL;
                previewSource.type = file.type;
                previewPlayer.load();
                previewContainer.classList.remove('hidden');
            } else {
                spanvideo.innerText = "Sélectionner une vidéo";
                spanvideo.classList.remove('text-green-600', 'font-semibold');
                document.getElementById('video-preview').classList.add('hidden');
            }
        });
    }

    // Animation upload modal
    const videomodInput = document.getElementById('videomod');
    const modphotoInput = document.getElementById('modphoto');
    
    videomodInput?.addEventListener('change', function() {
        const label = document.getElementById('videomod-label');
        if(this.files.length > 0) {
            label.innerHTML = `Vidéo sélectionnée ✓`;
            label.parentElement.classList.add('bg-green-500');
            label.parentElement.classList.remove('bg-gradient-to-r', 'from-pink-500', 'to-orange-400');
            
            // Afficher aperçu vidéo
            const file = this.files[0];
            const previewContainer = document.getElementById('videomod-preview');
            const previewPlayer = document.getElementById('videomod-preview-player');
            const previewSource = document.getElementById('videomod-preview-source');
            
            const fileURL = URL.createObjectURL(file);
            previewSource.src = fileURL;
            previewSource.type = file.type;
            previewPlayer.load();
            previewContainer.classList.remove('hidden');
        }
    });

    modphotoInput?.addEventListener('change', function() {
        const label = document.getElementById('modphoto-label');
        if(this.files.length > 0) {
            label.innerHTML = `Photo sélectionnée ✓`;
            label.parentElement.classList.add('bg-green-500');
            label.parentElement.classList.remove('bg-gradient-to-r', 'from-pink-500', 'to-orange-400');
            
            // Afficher aperçu photo
            const file = this.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.getElementById('modphoto-preview');
                const previewImg = document.getElementById('modphoto-preview-img');
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>

@endsection
