<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Reine ESGIS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-custom-pink-50 to-custom-purple-50 min-h-screen flex flex-col">
    <div class="min-h-screen flex flex-col">
        @include('components.header')

        <!-- Page Content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        @include('components.footer')
    </div>

    <!-- Modale pour photos et vidéos -->
    <div id="media-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-90 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full w-full">
            <!-- Bouton fermer -->
            <button onclick="closeMediaModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Contenu média -->
            <div id="media-content" class="w-full h-full flex items-center justify-center">
                <!-- Le contenu sera injecté ici -->
            </div>
        </div>
    </div>

    <script>
        function openMediaModal(src, type) {
            const modal = document.getElementById('media-modal');
            const content = document.getElementById('media-content');
            
            if (type === 'image') {
                content.innerHTML = `
                    <img src="${src}" alt="Image agrandie" 
                         class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
                `;
            } else if (type === 'video') {
                content.innerHTML = `
                    <video controls autoplay class="max-w-full max-h-full rounded-lg shadow-2xl">
                        <source src="${src}" type="video/mp4">
                        <source src="${src}" type="video/webm">
                        <source src="${src}" type="video/ogg">
                        Votre navigateur ne supporte pas la vidéo.
                    </video>
                `;
            }
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeMediaModal() {
            const modal = document.getElementById('media-modal');
            const content = document.getElementById('media-content');
            
            modal.classList.add('hidden');
            content.innerHTML = '';
            document.body.style.overflow = 'auto';
            
            // Arrêter toutes les vidéos
            const videos = content.querySelectorAll('video');
            videos.forEach(video => {
                video.pause();
                video.currentTime = 0;
            });
        }
        
        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMediaModal();
            }
        });
        
        // Fermer en cliquant à l'extérieur
        document.getElementById('media-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMediaModal();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
