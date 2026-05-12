<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $dashboard['title'] ?? 'Dashboard' }} - LMS Batik</title>
    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --surface: #f4f7fb;
            --surface-strong: #ffffff;
            --ink: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 10%, rgba(16, 132, 170, 0.10), transparent 38%),
                radial-gradient(circle at 88% 16%, rgba(15, 76, 129, 0.08), transparent 34%),
                var(--surface);
        }
    </style>
</head>

<body class="min-h-screen">
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

        @php
            $dashboard = $dashboard ?? [];
            $dashboard = array_merge(
                [
                    'title' => 'Dashboard',
                    'subtitle' => 'Selamat datang di portal peserta. Pantau pembaruan dan aktivitas belajar Anda.',
                    'headerGradient' => 'from-[#050847] to-blue-900',
                    'showNotification' => false,
                    'roleBadgeClasses' => 'bg-blue-100 text-blue-700',
                    'activeMenuClasses' => 'bg-blue-100 text-blue-800',
                    'profileUrl' => route('dashboard.participant.profile'),
                    'menuItems' => [
                        [
                            'label' => 'Dashboard',
                            'icon' => 'home',
                            'url' => route('dashboard.participant.home'),
                            'active' => false,
                        ],
                        [
                            'label' => 'Modul Pembelajaran',
                            'icon' => 'book',
                            'url' => route('dashboard.participant.modules'),
                            'active' => false,
                        ],
                        [
                            'label' => 'Forum Diskusi',
                            'icon' => 'chat',
                            'url' => route('dashboard.participant.forum'),
                            'active' => false,
                        ],
                        [
                            'label' => 'Galeri Karya',
                            'icon' => 'gallery',
                            'url' => route('dashboard.participant.gallery'),
                            'active' => false,
                        ],
                    ],
                ],
                $dashboard,
            );

            $user =
                $user ??
                (auth()->check()
                    ? [
                        'name' => auth()->user()->name,
                        'email' => auth()->user()->email,
                        'role' => auth()->user()->role ?? 'participant',
                    ]
                    : [
                        'name' => 'Peserta',
                        'email' => '',
                        'role' => 'participant',
                    ]);
        @endphp

        @include('dashboard.partials.sidebar', ['dashboard' => $dashboard, 'user' => $user])

        <main class="flex-1 px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8">
            <div class="mx-auto w-full max-w-7xl">
                @if (session('status'))
                    <div
                        class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 sm:mb-5">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700 sm:mb-5">
                        {{ $errors->first() }}
                    </div>
                @endif

                <header
                    class="mb-5 rounded-2xl bg-gradient-to-r {{ $dashboard['headerGradient'] ?? 'from-[#050847] to-blue-900' }} p-4 text-white shadow-lg ring-1 ring-white/10 sm:mb-6 sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-white/80 sm:text-xs">LMS Batik</p>
                            <h1 class="mt-2 text-xl font-bold sm:text-2xl">{{ $dashboard['title'] ?? 'Dashboard' }}</h1>
                            <p class="mt-1 text-xs text-white/90 sm:text-sm">
                                {{ $dashboard['subtitle'] ?? 'Selamat datang di dashboard.' }}</p>
                        </div>

                        <div class="flex items-start gap-3">
                            <button type="button"
                                class="hidden rounded-full bg-white/15 p-2.5 text-white shadow-sm transition hover:bg-white/25 lg:inline-flex"
                                aria-label="Notifikasi">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </button>

                            <div class="relative">
                                <button id="user-dropdown-toggle" type="button" aria-haspopup="true" aria-expanded="false"
                                    class="flex items-center gap-3 rounded-full bg-white/10 p-2.5 transition hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-white/30">
                                    <span class="hidden text-sm font-medium text-white sm:inline">{{ $user['name'] ?? 'User' }}</span>
                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div id="user-dropdown-menu"
                                    class="invisible absolute right-0 z-20 mt-2 w-64 origin-top-right rounded-2xl bg-white p-2 text-slate-700 opacity-0 shadow-xl ring-1 ring-black/5 transition duration-150">
                                    <div class="px-3 py-3">
                                        <p class="text-sm font-semibold text-slate-900">{{ $user['name'] ?? 'User' }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ ucfirst($user['role'] ?? 'participant') }}</p>
                                    </div>

                                    <div class="h-px bg-slate-100"></div>

                                    <a href="{{ $dashboard['profileUrl'] ?? '#' }}"
                                        class="mt-1 flex items-center rounded-xl px-3 py-2 text-sm font-medium transition hover:bg-slate-50">
                                        Profile
                                    </a>

                                    <button id="header-logout-button" type="button"
                                        class="mt-1 flex w-full items-center rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
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
            const dropdownToggle = document.getElementById('user-dropdown-toggle');
            const dropdownMenu = document.getElementById('user-dropdown-menu');
            const logoutButton = document.getElementById('header-logout-button');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

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

            if (!dropdownToggle || !dropdownMenu) {
                return;
            }

            const openMenu = function() {
                dropdownMenu.classList.remove('invisible', 'opacity-0');
                dropdownMenu.classList.add('opacity-100');
                dropdownToggle.setAttribute('aria-expanded', 'true');
            };

            const closeMenu = function() {
                dropdownMenu.classList.add('invisible', 'opacity-0');
                dropdownMenu.classList.remove('opacity-100');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            };

            dropdownToggle.addEventListener('click', function(event) {
                event.stopPropagation();
                if (dropdownMenu.classList.contains('invisible')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            });

            document.addEventListener('click', function() {
                closeMenu();
            });

            if (logoutButton) {
                logoutButton.addEventListener('click', async function() {
                    try {
                        const response = await fetch('/api/v1/auth/logout', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });

                        const result = await response.json().catch(function() {
                            return {
                                success: false,
                                message: 'Logout gagal diproses.',
                            };
                        });

                        if (!response.ok || result.success !== true) {
                            throw new Error(result.message || 'Logout gagal diproses.');
                        }

                        window.location.assign(result.data?.redirect_url || '/login');
                    } catch (error) {
                        alert(error.message || 'Logout gagal diproses.');
                    }
                });
            }
        })();
    </script>
</body>

</html>
