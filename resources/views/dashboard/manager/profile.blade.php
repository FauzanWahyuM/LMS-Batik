@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="POST" action="{{ route('dashboard.manager.profile.update') }}" enctype="multipart/form-data"
            class="space-y-4">
            @csrf

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-800">Foto Profil</p>
                <div class="mt-3 flex flex-col items-center gap-3 sm:flex-row">
                    <img src="{{ !empty($profile['photo']) ? route('public-file', ['path' => 'profiles/' . $profile['photo']]) : asset('img/komunitasbatik.png') }}"
                        alt="Foto profil" class="h-20 w-20 rounded-full border border-slate-300 object-cover">
                    <input type="file" name="photo" accept="image/*"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $profile['full_name']) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Username</label>
                    <input type="text" name="username" value="{{ old('username', $profile['username']) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Password</label>
                    <div class="relative">
                        <input id="manager-profile-password" type="password" name="password"
                            value="{{ old('password', $profile['password']) }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-10 text-sm">
                        <button type="button" data-password-toggle data-password-target="manager-profile-password"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-500"
                            aria-label="Tampilkan kata sandi">
                            <span data-icon="show">&#128065;</span>
                            <span data-icon="hide" class="hidden">&#128584;</span>
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
                    <input type="text" name="role_label" value="{{ old('role_label', $profile['role_label']) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
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
