<header class="bg-white/80 backdrop-blur-sm shadow-sm sticky top-0 z-50" style="background:transparent">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between h-16">
        <div class="flex items-center space-x-2">
            <a href="{{ route('home') }}">
            <img src="{{ asset('/images/logoMissESGIS.png')}}" alt="Miss ESGIS Logo" class="h-16 w-16 md:h-20 md:w-20" />
               {{-- <span class="text-lg font-semibold text-gray-800">Miss ESGIS</span> --}}
            </a>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-6">
            {{-- <a href="{{ route('home') }}"
                class="text-gray-600 hover:text-primary-pink flex items-center space-x-1 transition-colors duration-200">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10" enable-background="new 0 0 32 32"
                    xml:space="preserve" width="25" height="25">
                    <path fill="none" stroke="#000000" stroke-width="0.625" stroke-miterlimit="10"
                        points="3,17 16,4 29,17 " d="M0.938 5.313L5 1.25L9.063 5.313" />
                    <path fill="none" stroke="#000000" stroke-width="0.625" stroke-miterlimit="10"
                        points="6,14 6,27 13,27 13,17 19,17 19,27 26,27
	26,14 "
                        d="M1.875 4.375L1.875 8.438L4.063 8.438L4.063 5.313L5.938 5.313L5.938 8.438L8.125 8.438L8.125 4.375" />
                </svg>
                <span style="border:1em ">Accueil</span>
            </a> --}}
            {{-- <a href="{{ route('candidates.index') }}"
                class="text-gray-600 hover:text-primary-pink flex items-center space-x-1 transition-colors duration-200">
                <svg fill="#000000" width="px" height="20px" viewBox="0 0 0.24 0.24" id="user"
                    data-name="Flat Color" xmlns="http://www.w3.org/2000/svg" class="icon flat-color">
                    <path id="primary"
                        d="M0.21 0.2a0.02 0.02 0 0 1 -0.02 0.02H0.05a0.02 0.02 0 0 1 -0.02 -0.02 0.06 0.06 0 0 1 0.06 -0.06h0.06a0.06 0.06 0 0 1 0.06 0.06m-0.09 -0.08a0.05 0.05 0 1 0 -0.05 -0.05 0.05 0.05 0 0 0 0.05 0.05"
                        style="fill: rgb(0, 0, 0);" />
                </svg>
                <span>Candidates</span>
            </a> --}}
            <a href="{{ route('candidates.create') }}"
                class="text-gray-600 hover:text-primary-pink flex items-center space-x-1 transition-colors duration-200">
                <svg fill="#000000" width="20px" height="20px" viewBox="0 0 0.6 0.6"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M0.175 0.4h0.25a0.125 0.125 0 0 1 0.125 0.125 0.025 0.025 0 0 1 -0.05 0.003l0 -0.007a0.075 0.075 0 0 0 -0.07 -0.07L0.425 0.45H0.175a0.075 0.075 0 0 0 -0.075 0.075 0.025 0.025 0 0 1 -0.05 0 0.125 0.125 0 0 1 0.12 -0.125zm0.125 -0.35q0.015 0 0.03 0.003a0.025 0.025 0 1 1 -0.01 0.049A0.1 0.1 0 1 0 0.38 0.26a0.025 0.025 0 0 1 0.04 0.03A0.15 0.15 0 1 1 0.3 0.05m0.175 0a0.025 0.025 0 0 1 0.025 0.025v0.025h0.025a0.025 0.025 0 0 1 0 0.05h-0.025v0.025a0.025 0.025 0 0 1 -0.05 0V0.15h-0.025a0.025 0.025 0 0 1 0 -0.05h0.025V0.075a0.025 0.025 0 0 1 0.025 -0.025" />
                </svg>
                <span>S'inscrire</span>
            </a>
            {{-- <a href="{{ route('home') }}#vote-section"
                class="text-gray-600 hover:text-primary-pink flex items-center space-x-1 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Voter</span>
            </a> --}}
        </nav>

        <!-- Mobile Hamburger Menu -->
        <div class="md:hidden">
            <button id="mobile-menu-button" class="text-gray-600 hover:text-primary-pink focus:outline-none" aria-controls="mobile-drawer" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none md:hidden z-[100] transition-opacity duration-300"></div>

    <div id="mobile-drawer" class="fixed right-0 top-0 h-screen w-72 max-w-[85%] bg-white backdrop-blur-sm shadow-xl transform translate-x-full transition-transform duration-300 md:hidden z-[100]">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <span class="text-base font-semibold">Navigation</span>
            <button id="mobile-close" class="p-2 rounded hover:bg-gray-100" aria-label="Fermer le menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <nav class="flex flex-col gap-2 p-4">
           {{--  <a href="{{ route('home') }}"
                class="gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 flex items-center space-x-2 justify-start">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10" enable-background="new 0 0 32 32"
                    xml:space="preserve" width="25" height="25">
                    <path fill="none" stroke="#000000" stroke-width="0.625" stroke-miterlimit="10"
                        points="3,17 16,4 29,17 " d="M0.938 5.313L5 1.25L9.063 5.313" />
                    <path fill="none" stroke="#000000" stroke-width="0.625" stroke-miterlimit="10"
                        points="6,14 6,27 13,27 13,17 19,17 19,27 26,27
	26,14 "
                        d="M1.875 4.375L1.875 8.438L4.063 8.438L4.063 5.313L5.938 5.313L5.938 8.438L8.125 8.438L8.125 4.375" />
                </svg>
                <span>Accueil</span>
            </a> --}}
            <a href="{{ route('candidates.index') }}"
                class="gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 flex items-center space-x-2 justify-start">
                <svg fill="#000000" width="20px" height="20px" viewBox="0 0 0.6 0.6" id="user"
                    data-name="Flat Color" xmlns="http://www.w3.org/2000/svg" class="icon flat-color">
                    <path id="primary"
                        d="M0.525 0.5a0.05 0.05 0 0 1 -0.05 0.05H0.125a0.05 0.05 0 0 1 -0.05 -0.05 0.15 0.15 0 0 1 0.15 -0.15h0.15a0.15 0.15 0 0 1 0.15 0.15m-0.225 -0.2a0.125 0.125 0 1 0 -0.125 -0.125 0.125 0.125 0 0 0 0.125 0.125"
                        style="fill: rgb(0, 0, 0);" />
                </svg>
                <span>Candidates</span>
            </a>
            <a href="{{ route('candidates.create') }}"
                class="gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 flex items-center space-x-2 justify-start">
                <svg fill="#000000" width="20px" height="20px" viewBox="0 0 0.6 0.6"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M0.175 0.4h0.25a0.125 0.125 0 0 1 0.125 0.125 0.025 0.025 0 0 1 -0.05 0.003l0 -0.007a0.075 0.075 0 0 0 -0.07 -0.07L0.425 0.45H0.175a0.075 0.075 0 0 0 -0.075 0.075 0.025 0.025 0 0 1 -0.05 0 0.125 0.125 0 0 1 0.12 -0.125zm0.125 -0.35q0.015 0 0.03 0.003a0.025 0.025 0 1 1 -0.01 0.049A0.1 0.1 0 1 0 0.38 0.26a0.025 0.025 0 0 1 0.04 0.03A0.15 0.15 0 1 1 0.3 0.05m0.175 0a0.025 0.025 0 0 1 0.025 0.025v0.025h0.025a0.025 0.025 0 0 1 0 0.05h-0.025v0.025a0.025 0.025 0 0 1 -0.05 0V0.15h-0.025a0.025 0.025 0 0 1 0 -0.05h0.025V0.075a0.025 0.025 0 0 1 0.025 -0.025" />
                </svg>
                <span>S'inscrire</span>
            </a>
            <a href="{{ route('home') }}#vote-section"
                class="gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 flex items-center space-x-2 justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Voter</span>
            </a>
        </nav>
    </div>

    <script>
        (function() {
            const openBtn = document.getElementById('mobile-menu-button');
            const closeBtn = document.getElementById('mobile-close');
            const overlay = document.getElementById('mobile-overlay');
            const drawer = document.getElementById('mobile-drawer');

            function openDrawer() {
                drawer.classList.remove('translate-x-full');
                overlay.classList.remove('pointer-events-none');
                overlay.classList.add('opacity-100');
                openBtn.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');
            }

            function closeDrawer() {
                drawer.classList.add('translate-x-full');
                overlay.classList.add('pointer-events-none');
                overlay.classList.remove('opacity-100');
                openBtn.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');
            }

            openBtn && openBtn.addEventListener('click', openDrawer);
            closeBtn && closeBtn.addEventListener('click', closeDrawer);
            overlay && overlay.addEventListener('click', closeDrawer);

            // Close on ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeDrawer();
            });

            // Close when clicking any link in drawer
            drawer && drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', closeDrawer));
        })();
    </script>
</header>
