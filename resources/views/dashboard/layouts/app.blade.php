<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $dashboard['title'] ?? 'Dashboard' }} - LMS Batik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100">
    <div class="min-h-screen lg:flex">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur lg:hidden">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2">
                    <img src="{{ asset('img/komunitasbatik.png') }}" alt="Logo"
                        class="h-8 w-8 rounded-md object-cover">
                    <span class="text-sm font-bold text-slate-800">LMS Batik</span>
                </a>

                <button type="button" id="sidebar-open-button"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                    aria-controls="dashboard-sidebar" aria-expanded="false" aria-label="Buka menu navigasi">
                    Menu
                </button>
            </div>
        </header>

        <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-900/40 lg:hidden"></div>

        @include('dashboard.partials.sidebar', ['dashboard' => $dashboard, 'user' => $user])

        <main class="flex-1 px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8">
            <div class="mx-auto w-full max-w-7xl">
                <header
                    class="mb-5 rounded-2xl bg-gradient-to-r {{ $dashboard['headerGradient'] ?? 'from-[#050847] to-blue-900' }} p-4 text-white shadow-lg ring-1 ring-white/10 sm:mb-6 sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.2em] text-white/80 sm:text-xs">LMS Batik</p>
                            <h1 class="mt-2 text-xl font-bold sm:text-2xl">
                                {{ $dashboard['title'] ?? 'Home - Dashboard Peserta' }}</h1>
                            <p class="mt-1 text-xs text-white/90 sm:text-sm">
                                {{ $dashboard['subtitle'] ?? 'Selamat datang di portal peserta. Pantau pembaruan dan aktivitas belajar Anda.' }}
                            </p>
                        </div>

                        @if (($dashboard['showNotification'] ?? false) === true)
                            <button type="button"
                                class="rounded-full bg-white/15 p-2.5 text-white shadow-sm transition hover:bg-white/25"
                                aria-label="Notifikasi">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </header>

                @yield('dashboard-content')
            </div>
        </main>
    </div>

    <script>
        (function() {
            const openButton = document.getElementById('sidebar-open-button');
            const closeButton = document.getElementById('sidebar-close-button');
            const sidebar = document.getElementById('dashboard-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (!sidebar || !overlay) {
                return;
            }

            const openSidebar = function() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                if (openButton) {
                    openButton.setAttribute('aria-expanded', 'true');
                }
            };

            const closeSidebar = function() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                if (openButton) {
                    openButton.setAttribute('aria-expanded', 'false');
                }
            };

            if (openButton) {
                openButton.addEventListener('click', openSidebar);
            }

            if (closeButton) {
                closeButton.addEventListener('click', closeSidebar);
            }

            overlay.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function(event) {
                if ((event.key === 'Escape') && window.innerWidth < 1024) {
                    closeSidebar();
                }
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    overlay.classList.add('hidden');
                    sidebar.classList.remove('-translate-x-full');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        })();
    </script>
</body>

</html>
