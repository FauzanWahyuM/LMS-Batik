<aside id="dashboard-sidebar"
    class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 ease-out lg:sticky lg:top-0 lg:min-h-screen lg:translate-x-0">
    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-5">
        <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3">
            <img src="{{ asset('img/komunitasbatik.png') }}" alt="Logo" class="h-10 w-10 rounded-md object-cover">
            <div>
                <p class="font-bold text-slate-800">LMS Batik</p>
                <p class="text-xs text-slate-500">LPK Kama Praja Madiun</p>
            </div>
        </a>

        <button type="button" id="sidebar-close-button"
            class="rounded-md border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 lg:hidden"
            aria-label="Tutup menu navigasi">
            Tutup
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto p-4">
        @foreach ($dashboard['menuItems'] as $item)
            <a href="{{ $item['url'] }}"
                class="dashboard-nav-link flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $item['active'] ? $dashboard['activeMenuClasses'] ?? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                <span class="mr-3 inline-flex h-5 w-5 items-center justify-center text-current">
                    @switch($item['icon'] ?? '')
                        @case('home')
                        @case('dashboard')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10" />
                            </svg>
                        @break

                        @case('module')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7zm4 0v10m4-10v10m4-10v10" />
                            </svg>
                        @break

                        @case('participant-individual')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m12-10a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        @break

                        @case('participant-group')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-2.13a4 4 0 10-8 0m8 0a4 4 0 01-8 0" />
                            </svg>
                        @break

                        @case('instructor-manage')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.42A12.08 12.08 0 0119 17.5c0 .95-.6 1.8-1.5 2.12A20.89 20.89 0 0112 21a20.89 20.89 0 01-5.5-1.38A2.25 2.25 0 015 17.5c0-2.42.3-4.78.84-6.92L12 14z" />
                            </svg>
                        @break

                        @case('program-manage')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5A4.5 4.5 0 003 9.5V19a4 4 0 014-4h5m0-8.747C13.168 5.477 14.754 5 16.5 5A4.5 4.5 0 0121 9.5V19a4 4 0 00-4-4h-5" />
                            </svg>
                        @break

                        @case('reports')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        @break

                        @case('testimonials')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h8m-8 4h5m-9 7l3.5-3H18a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h1.5L4 21z" />
                            </svg>
                        @break

                        @case('facilities')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 10h.01M15 10h.01" />
                            </svg>
                        @break

                        @case('partners')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c1.657-2.667 4-4 6-4a3 3 0 013 3c0 2-1.333 4.343-4 6 2.667 1.657 4 4 4 6a3 3 0 01-3 3c-2 0-4.343-1.333-6-4-1.657 2.667-4 4-6 4a3 3 0 01-3-3c0-2 1.333-4.343 4-6-2.667-1.657-4-4-4-6a3 3 0 013-3c2 0 4.343 1.333 6 4z" />
                            </svg>
                        @break

                        @case('settings')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.591 1.066c1.527-.94 3.31.843 2.37 2.37a1.724 1.724 0 001.065 2.591c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.591c.94 1.527-.843 3.31-2.37 2.37a1.724 1.724 0 00-2.591 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.591-1.066c-1.527.94-3.31-.843-2.37-2.37a1.724 1.724 0 00-1.065-2.591c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.591c-.94-1.527.843-3.31 2.37-2.37.996.611 2.296.07 2.591-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        @break

                        @case('participants')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a4 4 0 00-4-4h-1m-4 6H4v-2a4 4 0 014-4h5m1-4a4 4 0 100-8 4 4 0 000 8zm-7 1a3 3 0 100-6 3 3 0 000 6z" />
                            </svg>
                        @break

                        @case('forum')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h8m-8 4h5m-9 7l3.5-3H18a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h1.5L4 21z" />
                            </svg>
                        @break

                        @case('assessment')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m1-5H8a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2zM9 3h6" />
                            </svg>
                        @break

                        @case('book')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5A4.5 4.5 0 003 9.5V19a4 4 0 014-4h5m0-8.747C13.168 5.477 14.754 5 16.5 5A4.5 4.5 0 0121 9.5V19a4 4 0 00-4-4h-5" />
                            </svg>
                        @break

                        @case('chat')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h8m-8 4h5m7 7l-4-4H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        @break

                        @case('gallery')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-9-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @break

                        @case('achievement')
                        @case('achievements')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 21h8m-4-4v4M8 4h8a1 1 0 011 1v4a5 5 0 01-10 0V5a1 1 0 011-1zm-3 1h2v3a6.97 6.97 0 01-1 3.6A4 4 0 013 8V6a1 1 0 011-1zm14 0h2a1 1 0 011 1v2a4 4 0 01-3 3.87A6.97 6.97 0 0019 8.99V6a1 1 0 011-1z" />
                            </svg>
                        @break

                        @default
                            <span class="text-xs font-semibold">•</span>
                    @endswitch
                </span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-slate-200 p-4">
        <a href="{{ $dashboard['profileUrl'] ?? '#' }}"
            class="mb-4 block rounded-xl bg-slate-100 p-3 transition hover:bg-slate-200/80 focus:outline-none focus:ring-2 focus:ring-slate-400/60">
            <p class="text-xs text-slate-500">Login sebagai</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user['name'] ?? 'User' }}</p>
            <p class="text-xs text-slate-500">{{ $user['sidebar_email'] ?? ($user['email'] ?? '-') }}</p>
            <span
                class="mt-2 inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $dashboard['roleBadgeClasses'] }}">{{ $user['sidebar_role_label'] ?? ucfirst($user['role'] ?? 'participant') }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 transition">
                Logout
            </button>
        </form>
    </div>
</aside>
