@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Kelola Mitra</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali</a>
        </div>

        @if ($errors->has('partners'))
            <div
                class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 sm:text-sm">
                {{ $errors->first('partners') }}
            </div>
        @endif

        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('dashboard.manager.partners') }}"
                class="flex w-full gap-2 flex-col sm:flex-row">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama mitra"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-slate-500 focus:outline-none">
                <button type="submit"
                    class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cari</button>
            </form>
            <button type="button" id="open-create-partner"
                class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Create
                Mitra</button>
        </div>

        <div id="create-partner-form" class="mt-4 hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="text-sm font-semibold text-slate-900">Tambah Mitra</h3>
            <form method="POST" enctype="multipart/form-data" action="{{ route('dashboard.manager.partners.store') }}"
                class="mt-3 grid gap-3 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Mitra</label>
                    <input type="text" name="name" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Logo</label>
                    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" id="is_active_create_partner" name="is_active" value="1" checked>
                    <label for="is_active_create_partner" class="text-xs text-slate-700">Tampilkan di landing page</label>
                </div>
                <div class="sm:col-span-2 flex flex-col sm:flex-row sm:justify-end gap-2">
                    <button type="button" id="cancel-create-partner"
                        class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                </div>
            </form>
        </div>

        <div class="mt-5 space-y-3 max-w-7xl mx-auto">
            @forelse ($partners as $partner)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-900">{{ $partner->name }}</p>
                            @if (!empty($partner->logo_path))
                                <img src="{{ route('public-file', ['path' => $partner->logo_path]) }}"
                                    alt="{{ $partner->name }}"
                                    class="mt-2 h-20 w-20 rounded-full object-contain bg-white border border-slate-200 p-2">
                            @endif
                            <p
                                class="text-xs font-semibold {{ $partner->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                {{ $partner->is_active ? 'Aktif ditampilkan' : 'Tidak ditampilkan' }}
                            </p>
                        </div>
                        <div class="flex w-full flex-col gap-2 sm:w-32">
                            <button type="button" data-toggle="edit-{{ $partner->id }}"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Edit</button>
                            <form method="POST"
                                action="{{ route('dashboard.manager.partners.delete', ['partner' => $partner->id]) }}">
                                @csrf
                                <button type="button" data-delete-name="{{ $partner->name }}"
                                    class="delete-partner-btn w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <div id="edit-{{ $partner->id }}" class="mt-4 hidden rounded-lg border border-slate-200 bg-white p-4">
                        <h4 class="mb-3 text-sm font-semibold text-slate-900">Edit Mitra</h4>
                        <form method="POST" enctype="multipart/form-data"
                            action="{{ route('dashboard.manager.partners.edit', ['partner' => $partner->id]) }}"
                            class="grid gap-3 sm:grid-cols-2">
                            @csrf
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Mitra</label>
                                <input type="text" name="name" value="{{ $partner->name }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Ganti Logo</label>
                                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                            </div>
                            <div class="flex items-center gap-2 pt-6">
                                <input type="checkbox" id="is_active_partner_{{ $partner->id }}" name="is_active"
                                    value="1" {{ $partner->is_active ? 'checked' : '' }}>
                                <label for="is_active_partner_{{ $partner->id }}" class="text-xs text-slate-700">Tampilkan
                                    di landing page</label>
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
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">Mitra tidak
                    ditemukan.</div>
            @endforelse
        </div>
    </section>

    <div id="delete-modal-partner" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" id="modal-backdrop-partner">
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
                        <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title-partner">
                            Hapus Mitra
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500" id="modal-message-partner">
                                Apakah Anda yakin ingin menghapus data mitra ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" id="confirm-delete-btn-partner"
                        class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Hapus
                    </button>
                    <button type="button" id="cancel-delete-btn-partner"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const openButton = document.getElementById('open-create-partner');
            const cancelButton = document.getElementById('cancel-create-partner');
            const createForm = document.getElementById('create-partner-form');
            const modal = document.getElementById('delete-modal-partner');
            const backdrop = document.getElementById('modal-backdrop-partner');
            const confirmBtn = document.getElementById('confirm-delete-btn-partner');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn-partner');
            const modalTitle = document.getElementById('modal-title-partner');
            const modalMessage = document.getElementById('modal-message-partner');

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
                    const target = document.getElementById(button.getAttribute('data-toggle'));
                    if (target) {
                        target.classList.toggle('hidden');
                    }
                });
            });

            function showModal(name, form) {
                if (!modal || !modalTitle || !modalMessage) {
                    return;
                }

                modalTitle.textContent = `Hapus Mitra: ${name}`;
                modalMessage.textContent =
                    `Apakah Anda yakin ingin menghapus mitra "${name}"? Tindakan ini tidak dapat dibatalkan.`;
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

            document.querySelectorAll('.delete-partner-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const name = button.getAttribute('data-delete-name') || 'ini';
                    const form = button.closest('form');

                    if (form) {
                        showModal(name, form);
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
