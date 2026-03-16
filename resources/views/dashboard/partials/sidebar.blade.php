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
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l9-9 9 9M4 10v10h5v-6h6v6h5V10" />
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

                        @default
                            <span class="text-xs font-semibold">•</span>
                    @endswitch
                </span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-slate-200 p-4">
        <div class="mb-4 rounded-xl bg-slate-100 p-3">
            <p class="text-xs text-slate-500">Login sebagai</p>
            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $user['name'] ?? 'User' }}</p>
            <p class="text-xs text-slate-500">{{ $user['email'] ?? '-' }}</p>
            <span
                class="mt-2 inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $dashboard['roleBadgeClasses'] }}">{{ ucfirst($user['role'] ?? 'participant') }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 transition">
                Logout
            </button>
        </form>
    </div>
</aside>
