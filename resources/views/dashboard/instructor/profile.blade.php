@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="POST" action="{{ route('dashboard.instructor.profile.update') }}" class="space-y-4">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $profile['full_name']) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Username</label>
                    <input type="text" name="username" value="{{ old('username', $profile['username']) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Password</label>
                    <div class="relative">
                        <input id="instructor-profile-password" type="password" name="password" minlength="4"
                            maxlength="4" autocomplete="new-password"
                            value="{{ old('password', $profile['password'] ?? '') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-10 text-sm">
                        <button type="button" data-password-toggle data-password-target="instructor-profile-password"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-500"
                            aria-label="Tampilkan kata sandi">
                            <svg data-icon="show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ old('email', $profile['email']) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">No. Handphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $profile['phone']) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Alamat</label>
                    <textarea name="address" rows="3" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('address', $profile['address']) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                    <input type="text" value="{{ old('role_label', $profile['role_label']) }}" disabled
                        class="w-full cursor-not-allowed rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-600">
                </div>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                <button type="submit"
                    class="w-full sm:w-auto rounded-lg bg-slate-900 px-4 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-700">
                    Simpan Profil
                </button>
            </div>
        </form>
    </section>

    <script>
        (function() {
            const toggles = document.querySelectorAll('[data-password-toggle]');
            toggles.forEach(function(button) {
                button.addEventListener('click', function() {
                    const target = document.getElementById(button.getAttribute('data-password-target'));
                    if (!target) return;
                    const show = target.type === 'password';
                    target.type = show ? 'text' : 'password';
                    const showIcon = button.querySelector('[data-icon="show"]');
                    const hideIcon = button.querySelector('[data-icon="hide"]');
                    if (showIcon && hideIcon) {
                        showIcon.classList.toggle('hidden', show);
                        hideIcon.classList.toggle('hidden', !show);
                    }
                });
            });
        })();
    </script>
@endsection
