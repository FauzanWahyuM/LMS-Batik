@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 relative">

        <form method="POST" action="{{ route('dashboard.participant.profile.update') }}" class="space-y-4">
            @csrf

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-800">Jenis Peserta</p>
                <input type="hidden" name="participant_type"
                    value="{{ old('participant_type', $profile['participant_type']) }}">
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    <label
                        class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 opacity-80">
                        <input type="radio" name="participant_type" value="individual" @checked(old('participant_type', $profile['participant_type']) === 'individual')>
                        Peserta Individu
                    </label>
                    <label
                        class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 opacity-80">
                        <input type="radio" name="participant_type" value="group" @checked(old('participant_type', $profile['participant_type']) === 'group')>
                        Peserta Kelompok
                    </label>
                </div>
                <p class="mt-2 text-xs text-slate-500">Jenis peserta mengikuti data akun yang terdaftar di sistem.</p>
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
                        <input id="participant-profile-password" type="password" name="password" minlength="4"
                            maxlength="4" autocomplete="new-password"
                            value="{{ old('password', $profile['password'] ?? '') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-10 text-sm">

                        <button type="button" data-password-toggle data-password-target="participant-profile-password"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-500"
                            aria-label="Toggle password">

                            <!-- ICON MATA TERTUTUP (FIXED) -->
                            <svg data-icon="closed" class="hidden h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.73 5.08A9.94 9.94 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-1.258 2.593M6.1 6.1A9.956 9.956 0 002.458 12c1.274 4.057 5.064 7 9.542 7 1.358 0 2.65-.27 3.83-.756" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.88 9.88a3 3 0 104.24 4.24" />
                            </svg>

                            <!-- ICON MATA TERBUKA -->
                            <svg data-icon="open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Konfirmasi Password</label>
                    <div class="relative">
                        <input id="participant-profile-password-confirmation" type="password" name="password_confirmation"
                            minlength="4" maxlength="4" autocomplete="new-password"
                            value="{{ old('password_confirmation', $profile['password'] ?? '') }}" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-10 text-sm">

                        <button type="button" data-password-toggle
                            data-password-target="participant-profile-password-confirmation"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-500"
                            aria-label="Toggle password">

                            <!-- ICON CLOSED FIX -->
                            <svg data-icon="closed" class="hidden h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.73 5.08A9.94 9.94 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.956 9.956 0 01-1.258 2.593M6.1 6.1A9.956 9.956 0 002.458 12c1.274 4.057 5.064 7 9.542 7 1.358 0 2.65-.27 3.83-.756" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.88 9.88a3 3 0 104.24 4.24" />
                            </svg>

                            <!-- ICON OPEN -->
                            <svg data-icon="open" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                        @readonly(($profile['participant_type'] ?? 'individual') === 'group')
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm {{ ($profile['participant_type'] ?? 'individual') === 'group' ? 'bg-slate-100' : '' }}">
                    @if (($profile['participant_type'] ?? 'individual') === 'group')
                        <p class="mt-1 text-xs text-slate-500">Nomor ini mengikuti nomor PIC kelompok pada login pertama.
                        </p>
                    @endif
                </div>

                @if (($profile['participant_type'] ?? 'individual') === 'group')
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">No. Handphone Pribadi</label>
                        <input type="text" name="personal_phone"
                            value="{{ old('personal_phone', $profile['personal_phone'] ?? '') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            placeholder="Wajib diisi untuk reset password via WhatsApp" required>
                        <p class="mt-1 text-xs text-slate-500">Kode reset password akan dikirim ke nomor pribadi ini.</p>
                    </div>
                @endif

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Alamat</label>
                    <textarea name="address" rows="3" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('address', $profile['address']) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Program</label>
                    <input type="text" value="{{ old('program', $profile['program'] ?? 'Program Belum Dipilih') }}"
                        disabled class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm">
                </div>

                <div id="individual-fields" class="sm:col-span-2 space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Motivasi Singkat Anda Mengikuti
                            Program!</label>
                        <textarea name="motivation" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('motivation', $profile['motivation']) }}</textarea>
                    </div>
                </div>

                <div id="group-fields" class="sm:col-span-2 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Institusi</label>
                        <input type="text" name="group_name"
                            value="{{ old('group_name', $profile['group_name'] ?? '-') }}" readonly
                            class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Nama PIC</label>
                        <input type="text" name="pic_name" value="{{ old('pic_name', $profile['pic_name']) }}"
                            readonly class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Role</label>
                    <input type="text" name="role_label" value="{{ old('role_label', $profile['role_label']) }}"
                        required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
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

    <!-- Modal for password change notification -->
    @if (session('force_password_change'))
        <div id="password-change-modal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-slate-900">Perubahan Password Diperlukan</h3>
                    <p class="text-sm text-slate-600">Harap ganti password Anda terlebih dahulu sebelum melanjutkan ke
                        halaman lain.</p>
                    <button onclick="closeModal()"
                        class="mt-4 w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        (function() {
            const participantTypeInput = document.querySelector('input[type="hidden"][name="participant_type"]');
            const toggles = document.querySelectorAll('[data-password-toggle]');
            toggles.forEach(function(button) {
                button.addEventListener('click', function() {
                    const target = document.getElementById(button.getAttribute('data-password-target'));
                    if (!target) return;
                    target.type = target.type === 'text' ? 'password' : 'text';
                    const closedIcon = button.querySelector('[data-icon="closed"]');
                    const openIcon = button.querySelector('[data-icon="open"]');
                    if (closedIcon && openIcon) {
                        closedIcon.classList.toggle('hidden', target.type === 'password');
                        openIcon.classList.toggle('hidden', target.type !== 'password');
                    }
                });
            });

            const radios = document.querySelectorAll('input[name="participant_type"]');
            const individualFields = document.getElementById('individual-fields');
            const groupFields = document.getElementById('group-fields');
            const visibleRadios = Array.from(radios).filter(function(radio) {
                return radio.type !== 'hidden';
            });

            visibleRadios.forEach(function(radio) {
                radio.disabled = true;
            });

            const syncTypeFields = function() {
                let type = participantTypeInput ? participantTypeInput.value : 'individual';

                if (!type) {
                    const checkedVisible = visibleRadios.find(function(radio) {
                        return radio.checked;
                    });
                    type = checkedVisible ? checkedVisible.value : 'individual';
                }

                visibleRadios.forEach(function(radio) {
                    radio.checked = radio.value === type;
                });

                if (individualFields) {
                    individualFields.classList.toggle('hidden', type !== 'individual');
                }

                if (groupFields) {
                    groupFields.classList.toggle('hidden', type !== 'group');
                }
            };

            visibleRadios.forEach(function(radio) {
                radio.addEventListener('change', syncTypeFields);
            });

            syncTypeFields();
        })();

        function closeModal() {
            const modal = document.getElementById('password-change-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
    </script>
@endsection
