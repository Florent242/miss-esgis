<header class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between h-16">
        <div class="flex items-center space-x-2">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('/images/logoMissESGIS.png')}}" alt="Reine ESGIS Logo" class="h-16 w-16 md:h-20 md:w-20 hover:scale-110 transition-transform duration-300" />
            </a>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-6">
            <!-- Navigation vide - seul le logo reste -->
        </nav>

        <!-- Plus de menu mobile nécessaire -->
        <div class="md:hidden">
            <!-- Espace vide - pas de menu mobile -->
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
            <!-- Navigation mobile vide -->
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
