@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Kelola Prestasi</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali</a>
        </div>
        <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Event</label>
        @if ($errors->has('achievements'))
            <div
                class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 sm:text-sm">
                {{ $errors->first('achievements') }}
            </div>
        @endif

        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('dashboard.manager.achievements') }}"
                class="flex w-full gap-2 flex-col sm:flex-row">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari judul/event/pemenang"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-slate-500 focus:outline-none">
                <button type="submit"
                    class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cari</button>
            </form>
            <button type="button" id="open-create-achievement"
                class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Create
                Prestasi</button>
        </div>

        <div id="create-achievement-form" class="mt-4 hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="text-sm font-semibold text-slate-900">Tambah Prestasi</h3>
            <form method="POST" action="{{ route('dashboard.manager.achievements.store') }}"
                class="mt-3 grid gap-3 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Peringkat</label>
                    <select name="rank" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Tanpa peringkat</option>
                        <option value="1">Juara 1</option>
                        <option value="2">Juara 2</option>
                        <option value="3">Juara 3</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                    <input type="number" name="year" min="2000" max="2100"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="2026">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Judul Prestasi</label>
                    <input type="text" name="title" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Contoh: Juara 1 Batik Nusantara">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Event</label>
                    <input type="text" name="event_name" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Nama event kompetisi">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Pemenang</label>
                    <input type="text" name="winner_name" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Nama alumni/peserta">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Ringkasan prestasi"></textarea>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="checkbox" id="is_active_create" name="is_active" value="1" checked>
                    <label for="is_active_create" class="text-xs text-slate-700">Tampilkan di landing page</label>
                </div>
                <div class="sm:col-span-2 flex flex-col sm:flex-row sm:justify-end gap-2">
                    <button type="button" id="cancel-create-achievement"
                        class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                </div>
            </form>
        </div>

        <div class="mt-5 space-y-3 max-w-7xl mx-auto">
            @forelse ($achievements as $achievement)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-900">{{ $achievement->title }}</p>
                            <p class="text-xs text-slate-600">Acara: {{ $achievement->event_name }}</p>
                            <p class="text-xs text-slate-600">Pemenang: {{ $achievement->winner_name }}</p>
                            <p class="text-xs text-slate-600">Peringkat:
                                {{ $achievement->rank ? 'Juara ' . $achievement->rank : 'Tanpa peringkat' }}</p>
                            <p class="text-xs text-slate-600">Tahun: {{ $achievement->year ?? '-' }}</p>
                            <p class="text-xs text-slate-500">{{ $achievement->description }}</p>
                            <p
                                class="text-xs font-semibold {{ $achievement->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                {{ $achievement->is_active ? 'Aktif ditampilkan' : 'Tidak ditampilkan' }}
                            </p>
                        </div>
                        <div class="flex w-full flex-col gap-2 sm:w-32">
                            <button type="button" data-toggle="edit-{{ $achievement->id }}"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Edit</button>
                            <form method="POST"
                                action="{{ route('dashboard.manager.achievements.delete', ['achievement' => $achievement->id]) }}">
                                @csrf
                                <button type="button" data-delete-title="{{ $achievement->title }}"
                                    class="delete-achievement-btn w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <div id="edit-{{ $achievement->id }}"
                        class="mt-4 hidden rounded-lg border border-slate-200 bg-white p-4">
                        <h4 class="mb-3 text-sm font-semibold text-slate-900">Edit Prestasi</h4>
                        <form method="POST"
                            action="{{ route('dashboard.manager.achievements.edit', ['achievement' => $achievement->id]) }}"
                            class="grid gap-3 sm:grid-cols-2">
                            @csrf
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Peringkat</label>
                                <select name="rank"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    <option value="" {{ is_null($achievement->rank) ? 'selected' : '' }}>Tanpa
                                        peringkat</option>
                                    <option value="1" {{ $achievement->rank === 1 ? 'selected' : '' }}>Juara 1
                                    </option>
                                    <option value="2" {{ $achievement->rank === 2 ? 'selected' : '' }}>Juara 2
                                    </option>
                                    <option value="3" {{ $achievement->rank === 3 ? 'selected' : '' }}>Juara 3
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Tahun</label>
                                <input type="number" name="year" min="2000" max="2100"
                                    value="{{ $achievement->year }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Judul Prestasi</label>
                                <input type="text" name="title" value="{{ $achievement->title }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Event</label>
                                <input type="text" name="event_name" value="{{ $achievement->event_name }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Pemenang</label>
                                <input type="text" name="winner_name" value="{{ $achievement->winner_name }}"
                                    required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi</label>
                                <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $achievement->description }}</textarea>
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-2">
                                <input type="checkbox" id="is_active_{{ $achievement->id }}" name="is_active"
                                    value="1" {{ $achievement->is_active ? 'checked' : '' }}>
                                <label for="is_active_{{ $achievement->id }}" class="text-xs text-slate-700">Tampilkan di
                                    landing page</label>
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
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">Prestasi tidak
                    ditemukan.</div>
            @endforelse
        </div>
    </section>

    <div id="delete-modal-achievement" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                id="modal-backdrop-achievement">
            </div>

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
                        <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title-achievement">
                            Hapus Prestasi
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500" id="modal-message-achievement">
                                Apakah Anda yakin ingin menghapus data prestasi ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" id="confirm-delete-btn-achievement"
                        class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Hapus
                    </button>
                    <button type="button" id="cancel-delete-btn-achievement"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const openButton = document.getElementById('open-create-achievement');
            const cancelButton = document.getElementById('cancel-create-achievement');
            const createForm = document.getElementById('create-achievement-form');
            const modal = document.getElementById('delete-modal-achievement');
            const backdrop = document.getElementById('modal-backdrop-achievement');
            const confirmBtn = document.getElementById('confirm-delete-btn-achievement');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn-achievement');
            const modalTitle = document.getElementById('modal-title-achievement');
            const modalMessage = document.getElementById('modal-message-achievement');

            let deleteForm = null;

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

            function showModal(title, form) {
                if (!modal || !modalTitle || !modalMessage) {
                    return;
                }

                modalTitle.textContent = `Hapus Prestasi: ${title}`;
                modalMessage.textContent =
                    `Apakah Anda yakin ingin menghapus prestasi "${title}"? Tindakan ini tidak dapat dibatalkan.`;
                deleteForm = form;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function hideModal() {
                if (!modal) {
                    return;
                }

                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                deleteForm = null;
            }

            document.querySelectorAll('.delete-achievement-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const title = button.getAttribute('data-delete-title') || 'ini';
                    const form = button.closest('form');

                    if (form) {
                        showModal(title, form);
                    }
                });
            });

            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    if (deleteForm) {
                        deleteForm.submit();
                    }
                });
            }

            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener('click', function() {
                    hideModal();
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    hideModal();
                });
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    hideModal();
                }
            });
        })();
    </script>
@endsection
