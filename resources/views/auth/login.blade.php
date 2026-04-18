<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - LMS Batik</title>
    <link rel="icon" type="image/png" href="{{ asset('img/Logo.png') }}">
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

            @php
                $showForgotPasswordSection =
                    $errors->has('forgot_password') ||
                    session('forgot_password_wa_url') ||
                    !empty(old('forgot_username')) ||
                    !empty(session('forgot_password_username'));
            @endphp

            <div id="header-section" class="{{ $showForgotPasswordSection ? 'hidden' : '' }}">
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
                        <img src="{{ asset('img/Logo.png') }}" alt="Logo LPK" class="h-full w-full object-cover"
                            onerror="this.style.display='none'">
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Selamat Datang</h1>
                    <p class="text-sm text-slate-500 mt-2">
                        Silakan masukkan username dan kata sandi Anda untuk melanjutkan.
                    </p>
                </div>
            </div>

            <div id="api-feedback" class="mb-6 hidden rounded-lg px-4 py-3 text-sm"></div>

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

            <section id="login-form-section" class="{{ $showForgotPasswordSection ? 'hidden' : '' }}">
                <form id="login-form" class="space-y-5">
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

                    <div class="pt-1 text-right">
                        <button id="forgot-password-toggle" type="button"
                            data-expanded="{{ $showForgotPasswordSection ? 'true' : 'false' }}"
                            class="text-xs font-semibold text-amber-700 transition hover:text-amber-800 hover:underline">Lupa
                            Password?</button>
                    </div>
                </form>

            </section>

            <section id="forgot-password-section"
                class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 {{ $showForgotPasswordSection ? '' : 'hidden' }}">
                <div class="mb-3 text-right">
                    <button id="back-to-login-toggle" type="button"
                        class="text-xs font-semibold text-slate-600 transition hover:text-slate-800 hover:underline">Kembali
                        ke Form Login</button>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Reset Password via WhatsApp</h3>
                <p class="mt-1 text-xs text-slate-500">Masukkan username peserta untuk membuat kode
                    verifikasi internal.</p>

                <form id="forgot-request-form" class="mt-3 space-y-3">
                    @csrf
                    <div>
                        <label for="forgot_username_request"
                            class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Username Peserta
                        </label>
                        <input id="forgot_username_request" name="forgot_username" type="text"
                            value="{{ old('forgot_username', session('forgot_password_username')) }}" required
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs transition-all focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10"
                            placeholder="contoh: pesertalmb01011">
                    </div>

                    <button type="submit"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                        Buat Kode Verifikasi
                    </button>
                </form>

                <div id="forgot-password-wa-container"
                    class="{{ session('forgot_password_wa_url') ? '' : 'hidden' }} mt-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                    <p id="forgot-password-wa-target">Kirim kode ke WhatsApp tujuan:
                        {{ session('forgot_password_wa_target') }}</p>
                    <a id="forgot-password-wa-link" href="{{ session('forgot_password_wa_url') }}" target="_blank"
                        rel="noopener"
                        class="mt-2 inline-flex rounded-md bg-emerald-600 px-3 py-1.5 font-semibold text-white hover:bg-emerald-700">Kirim
                        Kode via WhatsApp</a>
                </div>

                <form id="forgot-reset-form" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label for="forgot_username_reset" class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Username Peserta
                        </label>
                        <input id="forgot_username_reset" name="forgot_username" type="text"
                            value="{{ old('forgot_username', session('forgot_password_username')) }}" required
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs transition-all focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10"
                            placeholder="Harus sama seperti saat membuat kode">
                    </div>

                    <div>
                        <label for="verification_code" class="mb-1.5 block text-xs font-semibold text-slate-700">Kode
                            Verifikasi</label>
                        <input id="verification_code" name="verification_code" type="text" minlength="6"
                            maxlength="6" required
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs tracking-[0.25em] transition-all focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10"
                            placeholder="6 digit kode">
                    </div>

                    <div>
                        <label for="reset_password" class="mb-1.5 block text-xs font-semibold text-slate-700">Password
                            Baru</label>
                        <input id="reset_password" name="password" type="password" required
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs transition-all focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10"
                            placeholder="Minimal 6 karakter">
                    </div>

                    <div>
                        <label for="reset_password_confirmation"
                            class="mb-1.5 block text-xs font-semibold text-slate-700">Konfirmasi Password Baru</label>
                        <input id="reset_password_confirmation" name="password_confirmation" type="password" required
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs transition-all focus:border-amber-500 focus:outline-none focus:ring-4 focus:ring-amber-500/10"
                            placeholder="Ulangi password baru">
                    </div>

                    <button type="submit"
                        class="w-full rounded-lg bg-slate-800 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700">
                        Verifikasi Kode dan Reset Password
                    </button>
                </form>
            </section>

        </section>
    </div>
    <script>
        (function() {
            const toggleButtons = document.querySelectorAll('[data-password-toggle]');
            const forgotPasswordToggle = document.getElementById('forgot-password-toggle');
            const backToLoginToggle = document.getElementById('back-to-login-toggle');
            const loginFormSection = document.getElementById('login-form-section');
            const forgotPasswordSection = document.getElementById('forgot-password-section');
            const apiFeedback = document.getElementById('api-feedback');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const loginForm = document.getElementById('login-form');
            const forgotRequestForm = document.getElementById('forgot-request-form');
            const forgotResetForm = document.getElementById('forgot-reset-form');
            const forgotWaContainer = document.getElementById('forgot-password-wa-container');
            const forgotWaTarget = document.getElementById('forgot-password-wa-target');
            const forgotWaLink = document.getElementById('forgot-password-wa-link');

            const headerSection = document.getElementById('header-section');

            const showForgotPasswordForm = function() {
                if (loginFormSection) {
                    loginFormSection.classList.add('hidden');
                }
                if (forgotPasswordSection) {
                    forgotPasswordSection.classList.remove('hidden');
                }
                if (headerSection) {
                    headerSection.classList.add('hidden');
                }
                if (forgotPasswordToggle) {
                    forgotPasswordToggle.setAttribute('data-expanded', 'true');
                }
            };

            const showLoginForm = function() {
                if (forgotPasswordSection) {
                    forgotPasswordSection.classList.add('hidden');
                }
                if (loginFormSection) {
                    loginFormSection.classList.remove('hidden');
                }
                if (headerSection) {
                    headerSection.classList.remove('hidden');
                }
                if (forgotPasswordToggle) {
                    forgotPasswordToggle.setAttribute('data-expanded', 'false');
                }
            };

            const showFeedback = function(type, message) {
                if (!apiFeedback) {
                    return;
                }

                apiFeedback.classList.remove('hidden', 'border-emerald-100', 'bg-emerald-50', 'text-emerald-700',
                    'border-rose-100', 'bg-rose-50', 'text-rose-700');

                if (type === 'success') {
                    apiFeedback.classList.add('border', 'border-emerald-100', 'bg-emerald-50', 'text-emerald-700');
                } else {
                    apiFeedback.classList.add('border', 'border-rose-100', 'bg-rose-50', 'text-rose-700');
                }

                apiFeedback.textContent = message;
            };

            const hideFeedback = function() {
                if (!apiFeedback) {
                    return;
                }

                apiFeedback.classList.add('hidden');
            };

            const postJson = async function(url, payload) {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(function() {
                    return {
                        success: false,
                        message: 'Terjadi kesalahan saat memproses permintaan.',
                        data: null,
                    };
                });

                if (!response.ok || data.success !== true) {
                    throw new Error(data.message || 'Permintaan gagal diproses.');
                }

                return data;
            };

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

            if (forgotPasswordToggle) {
                forgotPasswordToggle.addEventListener('click', showForgotPasswordForm);
            }

            if (backToLoginToggle) {
                backToLoginToggle.addEventListener('click', showLoginForm);
            }

            if (loginForm) {
                loginForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    hideFeedback();

                    const username = document.getElementById('username')?.value || '';
                    const password = document.getElementById('password')?.value || '';

                    try {
                        const result = await postJson('/api/v1/auth/login', {
                            username,
                            password,
                        });

                        showFeedback('success', result.message || 'Login berhasil.');

                        const redirectUrl = result.data?.redirect_url || '/dashboard';
                        window.location.assign(redirectUrl);
                    } catch (error) {
                        showFeedback('error', error.message || 'Username atau password tidak valid.');
                    }
                });
            }

            if (forgotRequestForm) {
                forgotRequestForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    hideFeedback();

                    const forgotUsername = document.getElementById('forgot_username_request')?.value || '';

                    try {
                        const result = await postJson('/api/v1/auth/forgot-password/request', {
                            forgot_username: forgotUsername,
                        });

                        const waTarget = result.data?.forgot_password_wa_target || '-';
                        const waUrl = result.data?.forgot_password_wa_url || '#';

                        if (forgotWaTarget) {
                            forgotWaTarget.textContent = 'Kirim kode ke WhatsApp tujuan: ' + waTarget;
                        }

                        if (forgotWaLink) {
                            forgotWaLink.setAttribute('href', waUrl);
                        }

                        if (forgotWaContainer) {
                            forgotWaContainer.classList.remove('hidden');
                        }

                        const forgotResetUsername = document.getElementById('forgot_username_reset');
                        if (forgotResetUsername) {
                            forgotResetUsername.value = forgotUsername;
                        }

                        showFeedback('success', result.message || 'Kode verifikasi berhasil dibuat.');
                    } catch (error) {
                        showFeedback('error', error.message || 'Gagal membuat kode verifikasi.');
                    }
                });
            }

            if (forgotResetForm) {
                forgotResetForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    hideFeedback();

                    const forgotUsername = document.getElementById('forgot_username_reset')?.value || '';
                    const verificationCode = document.getElementById('verification_code')?.value || '';
                    const password = document.getElementById('reset_password')?.value || '';
                    const passwordConfirmation = document.getElementById('reset_password_confirmation')
                        ?.value || '';

                    try {
                        const result = await postJson('/api/v1/auth/forgot-password/reset', {
                            forgot_username: forgotUsername,
                            verification_code: verificationCode,
                            password,
                            password_confirmation: passwordConfirmation,
                        });

                        showFeedback('success', result.message ||
                            'Password berhasil direset. Silakan login menggunakan password baru.');
                        showLoginForm();
                    } catch (error) {
                        showFeedback('error', error.message || 'Gagal reset password.');
                    }
                });
            }
        })();
    </script>
</body>

</html>
