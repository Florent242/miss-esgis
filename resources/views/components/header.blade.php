<header class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between h-16">
        <div class="flex items-center space-x-2">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('/images/logoMissESGIS.png')}}" alt="Reine ESGIS Logo" class="h-16 w-16 md:h-20 md:w-20 hover:scale-110 transition-transform duration-300" />
            </a>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-6">
            <a href="{{ route('candidates.create') }}"
                class="group flex items-center space-x-2 px-4 py-2 rounded-lg text-gray-700 hover:text-pink-600 hover:bg-pink-50 transition-all duration-300">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 0.6 0.6" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M0.175 0.4h0.25a0.125 0.125 0 0 1 0.125 0.125 0.025 0.025 0 0 1 -0.05 0.003l0 -0.007a0.075 0.075 0 0 0 -0.07 -0.07L0.425 0.45H0.175a0.075 0.075 0 0 0 -0.075 0.075 0.025 0.025 0 0 1 -0.05 0 0.125 0.125 0 0 1 0.12 -0.125zm0.125 -0.35q0.015 0 0.03 0.003a0.025 0.025 0 1 1 -0.01 0.049A0.1 0.1 0 1 0 0.38 0.26a0.025 0.025 0 0 1 0.04 0.03A0.15 0.15 0 1 1 0.3 0.05m0.175 0a0.025 0.025 0 0 1 0.025 0.025v0.025h0.025a0.025 0.025 0 0 1 0 0.05h-0.025v0.025a0.025 0.025 0 0 1 -0.05 0V0.15h-0.025a0.025 0.025 0 0 1 0 -0.05h0.025V0.075a0.025 0.025 0 0 1 0.025 -0.025" />
                </svg>
                <span class="font-medium">S'inscrire</span>
            </a>
        </nav>

        <!-- Mobile Hamburger Button - Animated -->
        <div class="md:hidden">
            <button id="mobile-menu-button" class="relative w-10 h-10 text-gray-600 hover:text-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 rounded-lg transition-colors" aria-controls="mobile-drawer" aria-expanded="false">
                <span class="sr-only">Ouvrir le menu</span>
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
                    <span id="hamburger-line-1" class="block w-6 h-0.5 bg-current mb-1.5 transition-all duration-300 ease-out"></span>
                    <span id="hamburger-line-2" class="block w-6 h-0.5 bg-current mb-1.5 transition-all duration-300 ease-out"></span>
                    <span id="hamburger-line-3" class="block w-6 h-0.5 bg-current transition-all duration-300 ease-out"></span>
                </div>
            </button>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none md:hidden z-[100] transition-opacity duration-300"></div>

    <!-- Mobile Drawer - Amélioré -->
    <div id="mobile-drawer" class="fixed right-0 top-0 h-screen w-80 max-w-[85vw] bg-white shadow-2xl transform translate-x-full transition-transform duration-300 md:hidden z-[101] overflow-hidden">
        <!-- Header du drawer -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center">
                    <img src="{{ asset('/images/logoMissESGIS.png')}}" alt="Miss ESGIS" class="w-10 h-10 rounded-full object-cover" />
                </div>
                <span class="text-lg font-bold bg-gradient-to-r from-pink-600 to-orange-600 bg-clip-text text-transparent">Menu</span>
            </div>
            <button id="mobile-close" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" aria-label="Fermer le menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex flex-col gap-2 p-6">
            <a href="{{ route('candidates.index') }}"
                class="group flex items-center space-x-3 px-4 py-3 rounded-xl bg-purple-50 hover:bg-purple-100 border border-purple-200 hover:border-purple-300 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 0.6 0.6">
                        <path d="M0.525 0.5a0.05 0.05 0 0 1 -0.05 0.05H0.125a0.05 0.05 0 0 1 -0.05 -0.05 0.15 0.15 0 0 1 0.15 -0.15h0.15a0.15 0.15 0 0 1 0.15 0.15m-0.225 -0.2a0.125 0.125 0 1 0 -0.125 -0.125 0.125 0.125 0 0 0 0.125 0.125" />
                    </svg>
                </div>
                <span class="font-semibold text-gray-800 group-hover:text-pink-600 transition-colors">Candidates</span>
            </a>

            <a href="{{ route('candidates.create') }}"
                class="group flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-pink-500 to-orange-500 hover:from-pink-600 hover:to-orange-600 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 0.6 0.6">
                        <path fill-rule="evenodd" d="M0.175 0.4h0.25a0.125 0.125 0 0 1 0.125 0.125 0.025 0.025 0 0 1 -0.05 0.003l0 -0.007a0.075 0.075 0 0 0 -0.07 -0.07L0.425 0.45H0.175a0.075 0.075 0 0 0 -0.075 0.075 0.025 0.025 0 0 1 -0.05 0 0.125 0.125 0 0 1 0.12 -0.125zm0.125 -0.35q0.015 0 0.03 0.003a0.025 0.025 0 1 1 -0.01 0.049A0.1 0.1 0 1 0 0.38 0.26a0.025 0.025 0 0 1 0.04 0.03A0.15 0.15 0 1 1 0.3 0.05m0.175 0a0.025 0.025 0 0 1 0.025 0.025v0.025h0.025a0.025 0.025 0 0 1 0 0.05h-0.025v0.025a0.025 0.025 0 0 1 -0.05 0V0.15h-0.025a0.025 0.025 0 0 1 0 -0.05h0.025V0.075a0.025 0.025 0 0 1 0.025 -0.025" />
                    </svg>
                </div>
                <span class="font-bold text-white">S'inscrire</span>
            </a>

            <a href="{{ route('home') }}#vote-section"
                class="group flex items-center space-x-3 px-4 py-3 rounded-xl bg-green-50 hover:bg-green-100 border border-green-200 hover:border-green-300 hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Voter</span>
            </a>
        </nav>

        <!-- Footer du drawer -->
        <div class="absolute bottom-0 left-0 right-0 p-6 bg-white border-t border-gray-200">
            <div class="text-center text-sm text-gray-600">
                <p class="font-semibold">Reine ESGIS {{ date('Y') }}</p>
                <p class="text-xs mt-1">Élection de beauté et d'élégance</p>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const openBtn = document.getElementById('mobile-menu-button');
            const closeBtn = document.getElementById('mobile-close');
            const overlay = document.getElementById('mobile-overlay');
            const drawer = document.getElementById('mobile-drawer');
            const line1 = document.getElementById('hamburger-line-1');
            const line2 = document.getElementById('hamburger-line-2');
            const line3 = document.getElementById('hamburger-line-3');
            let isOpen = false;

            function openDrawer() {
                isOpen = true;
                drawer.classList.remove('translate-x-full');
                overlay.classList.remove('pointer-events-none', 'opacity-0');
                overlay.classList.add('opacity-100');
                openBtn.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');

                // Animate hamburger to X
                line1.style.transform = 'rotate(45deg) translateY(7px)';
                line2.style.opacity = '0';
                line3.style.transform = 'rotate(-45deg) translateY(-7px)';
            }

            function closeDrawer() {
                isOpen = false;
                drawer.classList.add('translate-x-full');
                overlay.classList.add('pointer-events-none', 'opacity-0');
                overlay.classList.remove('opacity-100');
                openBtn.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');

                // Reset hamburger
                line1.style.transform = 'none';
                line2.style.opacity = '1';
                line3.style.transform = 'none';
            }

            function toggleDrawer() {
                isOpen ? closeDrawer() : openDrawer();
            }

            openBtn && openBtn.addEventListener('click', toggleDrawer);
            closeBtn && closeBtn.addEventListener('click', closeDrawer);
            overlay && overlay.addEventListener('click', closeDrawer);

            // Close on ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && isOpen) closeDrawer();
            });

            // Close when clicking any link in drawer
            drawer && drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', closeDrawer));
        })();
    </script>
</header>
