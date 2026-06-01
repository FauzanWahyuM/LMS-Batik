<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LMS Batik - Platform Pembelajaran Batik')</title>
    <meta name="description" content="@yield('meta_description', 'LMS Batik - platform pembelajaran batik, program pelatihan, galeri karya, dan pendaftaran peserta.')">
    <meta name="robots" content="index,follow">
    <meta name="theme-color" content="#1e3a8a">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'LMS Batik - Platform Pembelajaran Batik')">
    <meta property="og:description" content="@yield('meta_description', 'LMS Batik - platform pembelajaran batik, program pelatihan, galeri karya, dan pendaftaran peserta.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <link rel="icon" type="image/png" href="{{ asset('img/Logo1.jpeg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .bg-batik {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80"><path d="M14 16H9v-2h5V9.87a4 4 0 1 1 2 0V14h5v2h-5v15.95A10 10 0 0 0 23.66 27l-3.46-2 8.2-2.2-2.9 5a12 12 0 0 1-21 0l-2.89-5 8.2 2.2-3.47 2A10 10 0 0 0 14 31.95V16zm40 40h-5v-2h5v-4.13a4 4 0 1 1 2 0V54h5v2h-5v15.95A10 10 0 0 0 63.66 67l-3.47-2 8.2-2.2-2.88 5a12 12 0 0 1-21.02 0l-2.88-5 8.2 2.2-3.47 2A10 10 0 0 0 54 71.95V56zm-39 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm40-40a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM15 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm40 40a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" fill="%23f3f4f6" fill-opacity="0.4"/></svg>');
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50">
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-blue-900 focus:shadow-lg">
        Lewati ke konten utama
    </a>

    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-800 to-blue-950 shadow-lg fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('landing.index') }}" class="flex items-center">
                        <div class="w-10 h-10 bg-cyan-400 rounded-lg flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('img/Logo1.jpeg') }}" alt="Logo" width="762" height="754"
                                class="w-full h-full object-cover">
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex md:items-center md:space-x-1">
                    <a href="{{ route('landing.index') }}"
                        class="text-white hover:text-amber-500 px-4 py-2 text-sm font-medium transition {{ request()->routeIs('landing.index') ? 'text-amber-300' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('landing.about') }}"
                        class="text-white hover:text-amber-500 px-4 py-2 text-sm font-medium transition {{ request()->routeIs('landing.about') ? 'text-amber-300' : '' }}">
                        Tentang
                    </a>
                    <a href="{{ route('landing.programs') }}"
                        class="text-white hover:text-amber-500 px-4 py-2 text-sm font-medium transition {{ request()->routeIs('landing.programs') ? 'text-amber-300' : '' }}">
                        Program
                    </a>
                    <a href="{{ route('landing.gallery') }}"
                        class="text-white hover:text-amber-500 px-4 py-2 text-sm font-medium transition {{ request()->routeIs('landing.gallery') ? 'text-amber-300' : '' }}">
                        Galeri
                    </a>
                    <a href="{{ route('landing.registration') }}"
                        class="text-white hover:text-amber-500 px-4 py-2 text-sm font-medium transition {{ request()->routeIs('landing.registration') ? 'text-amber-300' : '' }}">
                        Pendaftaran
                    </a>
                    <a href="/login"
                        class="bg-amber-900 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-amber-800 transition ml-4">
                        Login
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" type="button" aria-label="Buka menu navigasi"
                        aria-controls="mobile-menu" aria-expanded="false"
                        class="text-white hover:text-amber-300 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-blue-900 border-t border-blue-700">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('landing.index') }}"
                    class="block px-3 py-2 text-white hover:bg-blue-800 hover:text-amber-300 rounded-md {{ request()->routeIs('landing.index') ? 'bg-blue-800 text-amber-300' : '' }}">Beranda</a>
                <a href="{{ route('landing.about') }}"
                    class="block px-3 py-2 text-white hover:bg-blue-800 hover:text-amber-300 rounded-md {{ request()->routeIs('landing.about') ? 'bg-blue-800 text-amber-300' : '' }}">Tentang</a>
                <a href="{{ route('landing.programs') }}"
                    class="block px-3 py-2 text-white hover:bg-blue-800 hover:text-amber-300 rounded-md {{ request()->routeIs('landing.programs') ? 'bg-blue-800 text-amber-300' : '' }}">Program</a>
                <a href="{{ route('landing.gallery') }}"
                    class="block px-3 py-2 text-white hover:bg-blue-800 hover:text-amber-300 rounded-md {{ request()->routeIs('landing.gallery') ? 'bg-blue-800 text-amber-300' : '' }}">Galeri</a>
                <a href="{{ route('landing.registration') }}"
                    class="block px-3 py-2 text-white hover:bg-blue-800 hover:text-amber-300 rounded-md {{ request()->routeIs('landing.registration') ? 'bg-blue-800 text-amber-300' : '' }}">Pendaftaran</a>
                <a href="/login"
                    class="block px-3 py-2 bg-amber-900 text-white rounded-md text-center font-medium hover:bg-amber-800">Login</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-blue-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- About Section -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-amber-400" style="font-family: 'Georgia', serif;">LMS Batik
                    </h3>
                    <p class="text-gray-300 text-sm leading-relaxed mb-4">
                        Platform pembelajaran batik terbaik untuk melestarikan dan mengembangkan seni batik Indonesia.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" aria-label="Facebook LPK Kama Praja Madiun"
                            class="text-gray-400 hover:text-amber-400 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#" aria-label="Instagram LPK Kama Praja Madiun"
                            class="text-gray-400 hover:text-amber-400 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                            </svg>
                        </a>
                        <a href="#" aria-label="X LPK Kama Praja Madiun"
                            class="text-gray-400 hover:text-amber-400 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-amber-400" style="font-family: 'Georgia', serif;">Menu
                    </h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('landing.index') }}"
                                class="text-gray-300 hover:text-amber-400 transition text-sm">Beranda</a></li>
                        <li><a href="{{ route('landing.about') }}"
                                class="text-gray-300 hover:text-amber-400 transition text-sm">Tentang</a></li>
                        <li><a href="{{ route('landing.programs') }}"
                                class="text-gray-300 hover:text-amber-400 transition text-sm">Program</a></li>
                        <li><a href="{{ route('landing.gallery') }}"
                                class="text-gray-300 hover:text-amber-400 transition text-sm">Galeri</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-bold mb-4 text-amber-400" style="font-family: 'Georgia', serif;">Hubungi
                        Kami</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-amber-400 mr-3 flex-shrink-0 mt-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-300 text-sm">Jl. Raya, Madiun<br />Jawa Timur, Indonesia</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-amber-400 mr-3 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            <span class="text-gray-300 text-sm">+62 8123456789</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-amber-400 mr-3 flex-shrink-0 mt-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-gray-300 text-sm">lpkkpraja@prajapilot.co.id</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-blue-900 mt-12 pt-8 text-center">
                <p class="text-gray-200 text-sm">&copy; 2026 LPK Kama Praja Madiun. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            mobileMenuButton.setAttribute('aria-expanded', mobileMenu.classList.contains('hidden') ? 'false' :
                'true');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
