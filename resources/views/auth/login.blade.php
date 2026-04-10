<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - LMS Batik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Background utama yang elegan dengan overlay gelap transparan */
        .kp-bg-pattern {
            background-color: #473c38;
            background-image: linear-gradient(rgba(45, 36, 33, 0.75), rgba(45, 36, 33, 0.85)), url('{{ asset('img/Batik6.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>

<body class="min-h-screen kp-bg-pattern flex items-center justify-center font-sans text-slate-800 p-4 sm:p-6 lg:p-8">

    <div class="w-full max-w-[950px] rounded-3xl bg-white shadow-2xl overflow-hidden grid lg:grid-cols-[1fr_1.2fr]">

        <aside
            class="p-10 flex flex-col justify-between text-white relative min-h-[300px] lg:min-h-[600px] bg-cover bg-center overflow-hidden"
            style="background-image: url('{{ asset('img/Portal.jpg') }}');">

            <div class="absolute inset-0 bg-slate-900/40 z-0"></div>

            <div class="relative z-10 mt-auto">
                <h2 class="text-3xl font-semibold mb-3 tracking-tight">Portal Belajar Terpadu</h2>
                <p class="text-sm text-slate-100/90 leading-relaxed max-w-sm">
                    Akses materi, jadwal, dan perkembangan belajar Anda dalam satu ekosistem yang terintegrasi dan mudah
                    digunakan.
                </p>
            </div>

            <div
                class="absolute bottom-0 left-0 right-0 h-1/2 bg-linear-to-t from-slate-900/80 to-transparent pointer-events-none z-0">
            </div>
        </aside>

        <section class="p-8 md:p-12 lg:p-14 bg-white flex flex-col justify-center relative">

            <a href="{{ route('landing.index') }}"
                class="absolute top-6 right-6 md:top-8 md:right-8 group flex items-center gap-2 text-sm font-medium text-slate-400 hover:text-slate-800 transition-colors duration-300">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>

            <div class="mb-10 mt-4 md:mt-0">
                <div
                    class="h-12 w-12 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center mb-5 overflow-hidden">
                    <img src="{{ asset('img/komunitasbatik.png') }}" alt="Logo LPK" class="h-full w-full object-cover"
                        onerror="this.style.display='none'">
                </div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Selamat Datang</h1>
                <p class="text-sm text-slate-500 mt-2">
                    Silakan masukkan username dan kata sandi Anda untuk melanjutkan.
                </p>
            </div>

            @if (session('status'))
                <div
                    class="mb-6 rounded-lg border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="mb-6 rounded-lg border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700 flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="username" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Username
                    </label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required
                        class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm transition-all focus:bg-white focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10 placeholder-slate-400"
                        placeholder="Masukkan username Anda">
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                            class="w-full rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3 pr-11 text-sm transition-all focus:bg-white focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10 placeholder-slate-400"
                            placeholder="Masukkan kata sandi Anda">
                        <button type="button" data-password-toggle data-password-target="password"
                            class="absolute inset-y-0 right-0 flex items-center justify-center px-3 text-slate-500 transition hover:text-slate-700"
                            aria-label="Tampilkan kata sandi">
                            <svg data-icon="show" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg data-icon="hide" class="hidden h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.31-3.95M9.88 9.88a3 3 0 104.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6.1 6.1A9.955 9.955 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.132 5.411M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full rounded-lg bg-slate-800 px-6 py-3.5 text-sm font-semibold text-white transition-all hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-800 focus:ring-offset-2">
                        Masuk
                    </button>
                </div>
            </form>

            <div class="mt-10 rounded-lg bg-slate-50 border border-slate-100 p-4 text-xs text-slate-500">
                <p class="font-semibold text-slate-700 mb-2">Informasi Akun Demo</p>
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center border-b border-slate-200/60 pb-1.5">
                        <span>Peserta (Murid)</span>
                        <span class="font-medium text-slate-700">participant01 <span
                                class="text-slate-400 font-normal">/</span> participant123</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-slate-200/60 pb-1.5">
                        <span>Pengajar</span>
                        <span class="font-medium text-slate-700">instructor01 <span
                                class="text-slate-400 font-normal">/</span> instructor123</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Pengelola</span>
                        <span class="font-medium text-slate-700">manager01 <span
                                class="text-slate-400 font-normal">/</span> manager123</span>
                    </div>
                </div>
            </div>

        </section>
    </div>
    <script>
        (function() {
            const toggleButtons = document.querySelectorAll('[data-password-toggle]');

            toggleButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const targetId = button.getAttribute('data-password-target');
                    const input = document.getElementById(targetId);

                    if (!input) {
                        return;
                    }

                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';

                    const showIcon = button.querySelector('[data-icon="show"]');
                    const hideIcon = button.querySelector('[data-icon="hide"]');

                    if (showIcon && hideIcon) {
                        showIcon.classList.toggle('hidden', isHidden);
                        hideIcon.classList.toggle('hidden', !isHidden);
                    }

                    button.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' :
                        'Tampilkan kata sandi');
                });
            });
        })();
    </script>
</body>

</html>
