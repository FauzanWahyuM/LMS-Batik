@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-bold text-slate-900">Kelola Pengajar</h2>
            <div class="flex items-center gap-2">
                <button type="button" id="open-create-instructor"
                    class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-700 sm:text-sm">Tambah
                    Pengajar</button>
                <a href="{{ route('dashboard.manager.home') }}"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 sm:text-sm">Kembali</a>
            </div>
        </div>

        @if ($errors->has('instructors'))
            <div
                class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 sm:text-sm">
                {{ $errors->first('instructors') }}
            </div>
        @endif

        <div id="create-instructor-form" class="mt-4 hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="text-base font-bold text-slate-900">Tambah Pengajar</h3>
            <form method="POST" action="{{ route('dashboard.manager.instructors.store') }}" enctype="multipart/form-data"
                class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Nama Lengkap</label>
                    <input name="name" type="text" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Masukkan nama pengajar">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Username (Random)</label>
                    <input name="username" id="create-username" type="text"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Klik generate random">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Password (Random)</label>
                    <div class="relative">
                        <input name="password" id="create-password" type="password" minlength="4" maxlength="4"
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-10 text-sm"
                            placeholder="Klik generate random">
                        <button type="button" data-password-toggle data-password-target="create-password"
                            class="absolute inset-y-0 right-0 flex items-center justify-center px-3 text-slate-500 transition hover:text-slate-700"
                            aria-label="Tampilkan kata sandi">
                            <svg data-icon="show" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg data-icon="hide" class="hidden h-4 w-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.31-3.95M9.88 9.88a3 3 0 104.24 4.24" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6.1 6.1A9.955 9.955 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.132 5.411M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <button type="button" id="generate-random-credential"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Generate
                        Username & Password</button>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Email</label>
                    <input name="email" type="email" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Masukkan email">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">No. Handphone</label>
                    <input name="phone" type="text" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Masukkan no. handphone">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Alamat</label>
                    <textarea name="address" required rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Masukkan alamat"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Pendidikan Terakhir</label>
                    <input name="education" type="text" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Masukkan pendidikan terakhir">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Gaji (Rp)</label>
                    <input name="salary" type="number" min="0" step="1" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Masukkan nominal gaji">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Upload Sertifikat (opsional)</label>
                    <input name="certificate" type="file" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm">
                </div>
                <div class="sm:col-span-2 flex flex-col sm:flex-row sm:justify-end gap-2">
                    <button type="button" id="cancel-create-instructor"
                        class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 sm:text-sm">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700 sm:text-sm">Tambahkan</button>
                </div>
        </div>
        </form>
        </div>

        <div class="mt-5 space-y-4 max-w-7xl mx-auto">
            @forelse ($instructors as $instructor)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $instructor['name'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Status:
                                <span
                                    class="rounded-full bg-white px-2 py-0.5 font-semibold text-slate-700">{{ $instructor['status'] }}</span>
                            </p>
                            <button type="button" data-toggle="detail-{{ $instructor['id'] }}"
                                class="mt-2 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Lihat
                                Detail</button>
                        </div>
                        <div class="flex w-full flex-col gap-2 sm:w-32">
                            <button type="button" data-toggle="edit-{{ $instructor['id'] }}"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Edit</button>
                            <button type="button" data-delete-instructor="{{ $instructor['id'] }}"
                                data-delete-name="{{ $instructor['name'] }}"
                                class="delete-instructor-btn w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                        </div>
                    </div>

                    <div id="detail-{{ $instructor['id'] }}"
                        class="mt-4 hidden rounded-lg border border-slate-200 bg-white p-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <th class="w-40 bg-slate-50 px-3 py-2 font-semibold text-slate-600">Nama Lengkap
                                        </th>
                                        <td class="px-3 py-2 text-slate-800">{{ $instructor['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Username</th>
                                        <td class="px-3 py-2 text-slate-800">{{ $instructor['username'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Password</th>
                                        <td class="px-3 py-2 text-slate-800">{{ $instructor['password'] ?? 'Tersimpan' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Email</th>
                                        <td class="px-3 py-2 text-slate-800">{{ $instructor['email'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">No. Handphone</th>
                                        <td class="px-3 py-2 text-slate-800">{{ $instructor['phone'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Alamat</th>
                                        <td class="px-3 py-2 text-slate-800">{{ $instructor['address'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Pendidikan Terakhir
                                        </th>
                                        <td class="px-3 py-2 text-slate-800">{{ $instructor['education'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Gaji</th>
                                        <td class="px-3 py-2 text-slate-800">Rp
                                            {{ number_format((int) ($instructor['salary'] ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Sertifikat</th>
                                        <td class="px-3 py-2 text-slate-800">
                                            {{ $instructor['certificate'] ?? 'Tidak ada' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="edit-{{ $instructor['id'] }}"
                        class="mt-4 hidden rounded-lg border border-slate-200 bg-white p-4">
                        <h4 class="mb-3 text-sm font-semibold text-slate-900">Edit Data Pengajar</h4>
                        <form method="POST"
                            action="{{ route('dashboard.manager.instructors.edit', ['instructor' => $instructor['id']]) }}"
                            enctype="multipart/form-data" class="grid gap-3 sm:grid-cols-2">
                            @csrf
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ $instructor['name'] }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Username</label>
                                <input type="text" name="username" value="{{ $instructor['username'] }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Password</label>
                                <div class="relative">
                                    <input id="edit-password-{{ $instructor['id'] }}" type="password" name="password"
                                        minlength="4" maxlength="4" autocomplete="new-password"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2 pr-10 text-sm"
                                        placeholder="Kosongkan jika tidak ingin mengubah password">
                                    <button type="button" data-password-toggle
                                        data-password-target="edit-password-{{ $instructor['id'] }}"
                                        class="absolute inset-y-0 right-0 flex items-center justify-center px-3 text-slate-500 transition hover:text-slate-700"
                                        aria-label="Tampilkan kata sandi">
                                        <svg data-icon="show" class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg data-icon="hide" class="hidden h-4 w-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.31-3.95M9.88 9.88a3 3 0 104.24 4.24" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6.1 6.1A9.955 9.955 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.132 5.411M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">Kosongkan jika tidak ingin mengganti password.</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Email</label>
                                <input type="email" name="email" value="{{ $instructor['email'] }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">No. Handphone</label>
                                <input type="text" name="phone" value="{{ $instructor['phone'] }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Pendidikan Terakhir</label>
                                <input type="text" name="education" value="{{ $instructor['education'] }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Gaji (Rp)</label>
                                <input type="number" min="0" step="1" name="salary"
                                    value="{{ (int) ($instructor['salary'] ?? 0) }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Alamat</label>
                                <textarea name="address" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>{{ $instructor['address'] }}</textarea>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Upload Sertifikat
                                    (opsional)
                                </label>
                                <input name="certificate" type="file" accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm">
                                <p class="mt-1 text-xs text-slate-500">Sertifikat saat ini:
                                    {{ $instructor['certificate'] ?? 'Tidak ada' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                                <select name="status"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    <option value="Aktif" @selected($instructor['status'] === 'Aktif')>Aktif</option>
                                    <option value="Nonaktif" @selected($instructor['status'] === 'Nonaktif')>Nonaktif</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2 flex justify-end">
                                <button type="submit"
                                    class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan
                                    Perubahan</button>
                            </div>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    Belum ada data pengajar.
                </div>
            @endforelse
        </div>
    </section>

    <script>
        (function() {
            const openButton = document.getElementById('open-create-instructor');
            const cancelButton = document.getElementById('cancel-create-instructor');
            const createForm = document.getElementById('create-instructor-form');
            const generateButton = document.getElementById('generate-random-credential');
            const usernameInput = document.getElementById('create-username');
            const passwordInput = document.getElementById('create-password');

            if (openButton && createForm) {
                openButton.addEventListener('click', function() {
                    createForm.classList.remove('hidden');
                });
            }

            if (cancelButton && createForm) {
                cancelButton.addEventListener('click', function() {
                    createForm.classList.add('hidden');
                });
            }

            if (generateButton && usernameInput && passwordInput) {
                generateButton.addEventListener('click', function() {
                    const suffix = Math.floor(Math.random() * 90) + 10;
                    const seed = 'instr' + suffix;
                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
                    let password = '';

                    for (let i = 0; i < 7; i++) {
                        password += chars.charAt(Math.floor(Math.random() * chars.length));
                    }

                    usernameInput.value = seed;
                    passwordInput.value = password + '!';
                });
            }

            const toggles = document.querySelectorAll('[data-toggle]');
            toggles.forEach(function(button) {
                button.addEventListener('click', function() {
                    const targetId = button.getAttribute('data-toggle');
                    const target = document.getElementById(targetId);

                    if (target) {
                        target.classList.toggle('hidden');
                    }
                });
            });

            const passwordToggles = document.querySelectorAll('[data-password-toggle]');
            passwordToggles.forEach(function(button) {
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

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" id="modal-backdrop"></div>

            <!-- Modal panel -->
            <div
                class="inline-block transform overflow-hidden rounded-2xl bg-white px-4 pt-5 pb-4 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title">
                            Hapus Pengajar
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500" id="modal-message">
                                Apakah Anda yakin ingin menghapus data pengajar ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" id="confirm-delete-btn"
                        class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Hapus
                    </button>
                    <button type="button" id="cancel-delete-btn"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('delete-modal');
            const backdrop = document.getElementById('modal-backdrop');
            const confirmBtn = document.getElementById('confirm-delete-btn');
            const cancelBtn = document.getElementById('cancel-delete-btn');
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');

            let deleteForm = null;
            let instructorName = '';

            // Show modal
            function showModal(name, message, form) {
                instructorName = name;
                modalTitle.textContent = `Hapus Pengajar: ${name}`;
                modalMessage.textContent = message;
                deleteForm = form;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            // Hide modal
            function hideModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                deleteForm = null;
                instructorName = '';
            }

            // Handle delete button clicks
            document.querySelectorAll('.delete-instructor-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const instructorId = this.getAttribute('data-delete-instructor');
                    const name = this.getAttribute('data-delete-name');

                    // Create form dynamically
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/dashboard/pengelola/kelola-pengajar/${instructorId}/delete`;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.getAttribute('content');
                        form.appendChild(csrfInput);
                    }

                    showModal(name,
                        `Apakah Anda yakin ingin menghapus data pengajar "${name}"? Tindakan ini tidak dapat dibatalkan.`,
                        form);
                });
            });

            // Confirm delete
            confirmBtn.addEventListener('click', function() {
                if (deleteForm) {
                    document.body.appendChild(deleteForm);
                    deleteForm.submit();
                }
            });

            // Cancel delete
            cancelBtn.addEventListener('click', function() {
                hideModal();
            });

            // Close modal on backdrop click
            backdrop.addEventListener('click', function() {
                hideModal();
            });

            // Close modal on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    hideModal();
                }
            });
        })();
    </script>
@endsection
