@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Kelola Program</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali</a>
        </div>

        @if ($errors->has('programs'))
            <div
                class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 sm:text-sm">
                {{ $errors->first('programs') }}
            </div>
        @endif

        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('dashboard.manager.programs') }}"
                class="flex w-full gap-2 flex-col sm:flex-row">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari nama atau deskripsi program"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-slate-500 focus:outline-none">
                <button type="submit"
                    class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cari</button>
            </form>
            <button type="button" id="open-create-program"
                class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Create
                Program</button>
        </div>

        <div id="create-program-form"
            class="mt-4 hidden max-w-7xl mx-auto rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="text-sm font-semibold text-slate-900">Tambah Program</h3>
            <form method="POST" action="{{ route('dashboard.manager.programs.store') }}"
                class="mt-3 grid gap-3 sm:grid-cols-2">
                @csrf
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Program</label>
                    <input type="text" name="name" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Masukkan nama program">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Durasi Program</label>
                    <input type="number" min="0.5" step="0.5" name="duration_hours" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Contoh: 24"
                        data-duration-input="create">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Satuan Durasi</label>
                    <select name="duration_unit" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        data-duration-unit-select="create">
                        <option value="hours" selected>Jam</option>
                        <option value="minutes">Menit</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Biaya Program</label>
                    <input type="number" min="0" step="1000" name="fee_amount" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Contoh: 350000">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Satuan Biaya</label>
                    <input type="text" name="fee_unit" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Contoh: per peserta">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi Program</label>
                    <textarea name="description" rows="3" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Masukkan deskripsi program"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Benefit Program</label>
                    <div id="create-benefits-list" class="space-y-2 mb-3">
                        <input type="text" name="benefits[]" required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                            placeholder="Masukkan benefit">
                    </div>
                    <button type="button" class="add-benefit-btn text-xs font-semibold text-blue-600 hover:text-blue-700">+
                        Tambah Benefit Lagi</button>
                </div>
                <div class="sm:col-span-2 flex items-center gap-2">
                    <input type="checkbox" id="is_active_create" name="is_active" value="1" checked>
                    <label for="is_active_create" class="text-xs text-slate-700">Tampilkan di landing page</label>
                </div>
                <div class="sm:col-span-2 flex flex-col sm:flex-row sm:justify-end gap-2">
                    <button type="button" id="cancel-create-program"
                        class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                </div>
            </form>
        </div>

        <div class="mt-5 space-y-3 max-w-7xl mx-auto">
            @forelse ($programs as $program)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-900">{{ $program->name }}</p>
                            <p class="text-xs text-slate-600">Durasi: {{ $program->duration_label }}</p>
                            <p class="text-xs text-slate-600">Biaya: Rp
                                {{ number_format((float) $program->fee_amount, 0, ',', '.') }} / {{ $program->fee_unit }}
                            </p>
                            <p class="text-xs text-slate-500">{{ $program->description }}</p>
                            <div class="text-xs text-slate-500 mb-1">
                                <p class="font-semibold">Manfaat:</p>
                                <ul class="ml-4 space-y-1">
                                    @foreach ((array) $program->benefits as $benefit)
                                        <li>★ {{ $benefit }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <p
                                class="text-xs font-semibold {{ $program->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                {{ $program->is_active ? 'Aktif ditampilkan' : 'Tidak ditampilkan' }}
                            </p>
                        </div>
                        <div class="flex w-full flex-col gap-2 sm:w-32">
                            <button type="button" data-toggle="edit-{{ $program->id }}"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Edit</button>
                            <form method="POST"
                                action="{{ route('dashboard.manager.programs.delete', ['program' => $program->id]) }}">
                                @csrf
                                <button type="button" data-delete-name="{{ $program->name }}"
                                    class="delete-program-btn w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <div id="edit-{{ $program->id }}"
                        class="mt-4 hidden rounded-lg border border-slate-200 bg-white p-4">
                        <h4 class="mb-3 text-sm font-semibold text-slate-900">Edit Program</h4>
                        <form method="POST"
                            action="{{ route('dashboard.manager.programs.edit', ['program' => $program->id]) }}"
                            class="grid gap-3 sm:grid-cols-2">
                            @csrf
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Program</label>
                                <input type="text" name="name" value="{{ $program->name }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Durasi Program</label>
                                <input type="number" min="0.5" step="0.5" name="duration_hours"
                                    value="{{ $program->duration_hours }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                                    data-duration-input="edit-{{ $program->id }}">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Satuan Durasi</label>
                                <select name="duration_unit"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                                    data-duration-unit-select="edit-{{ $program->id }}">
                                    <option value="hours"
                                        {{ ($program->duration_unit ?? 'hours') === 'hours' ? 'selected' : '' }}>
                                        Jam</option>
                                    <option value="minutes"
                                        {{ ($program->duration_unit ?? 'hours') === 'minutes' ? 'selected' : '' }}>Menit
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Biaya Program</label>
                                <input type="number" min="0" step="1000" name="fee_amount"
                                    value="{{ $program->fee_amount }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Satuan Biaya</label>
                                <input type="text" name="fee_unit" value="{{ $program->fee_unit }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi Program</label>
                                <textarea name="description" rows="3" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $program->description }}</textarea>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Benefit Program</label>
                                <div id="edit-{{ $program->id }}-benefits-list" class="space-y-2 mb-3">
                                    @foreach ((array) $program->benefits as $benefit)
                                        <div class="flex gap-2">
                                            <input type="text" name="benefits[]" required value="{{ $benefit }}"
                                                class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            <button type="button"
                                                class="remove-benefit-btn rounded-lg border border-rose-300 px-2 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button"
                                    class="add-benefit-btn text-xs font-semibold text-blue-600 hover:text-blue-700">+
                                    Tambah Benefit Lagi</button>
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-2">
                                <input type="checkbox" id="is_active_{{ $program->id }}" name="is_active"
                                    value="1" {{ $program->is_active ? 'checked' : '' }}>
                                <label for="is_active_{{ $program->id }}" class="text-xs text-slate-700">Tampilkan di
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
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">Program tidak
                    ditemukan.</div>
            @endforelse
        </div>
    </section>

    <div id="delete-modal-program" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" id="modal-backdrop-program">
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
                        <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title-program">
                            Hapus Program
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500" id="modal-message-program">
                                Apakah Anda yakin ingin menghapus data program ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" id="confirm-delete-btn-program"
                        class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Hapus
                    </button>
                    <button type="button" id="cancel-delete-btn-program"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const openButton = document.getElementById('open-create-program');
            const cancelButton = document.getElementById('cancel-create-program');
            const createForm = document.getElementById('create-program-form');
            const modal = document.getElementById('delete-modal-program');
            const backdrop = document.getElementById('modal-backdrop-program');
            const confirmBtn = document.getElementById('confirm-delete-btn-program');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn-program');
            const modalTitle = document.getElementById('modal-title-program');
            const modalMessage = document.getElementById('modal-message-program');

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

            // Handle dynamic benefit field management
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('add-benefit-btn')) {
                    event.preventDefault();
                    const benefitsList = event.target.previousElementSibling;
                    const newInput = document.createElement('input');
                    newInput.type = 'text';
                    newInput.name = 'benefits[]';
                    newInput.required = true;
                    newInput.placeholder = 'Masukkan benefit';
                    newInput.className = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm';
                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex gap-2';
                    wrapper.appendChild(newInput);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.textContent = 'Hapus';
                    removeBtn.className =
                        'remove-benefit-btn rounded-lg border border-rose-300 px-2 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50';
                    wrapper.appendChild(removeBtn);
                    benefitsList.appendChild(wrapper);
                }

                if (event.target.classList.contains('remove-benefit-btn')) {
                    event.preventDefault();
                    event.target.parentElement.remove();
                }
            });

            function showModal(name, form) {
                if (!modal || !modalTitle || !modalMessage) {
                    return;
                }

                modalTitle.textContent = `Hapus Program: ${name}`;
                modalMessage.textContent =
                    `Apakah Anda yakin ingin menghapus program "${name}"? Tindakan ini tidak dapat dibatalkan.`;
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

            document.querySelectorAll('.delete-program-btn').forEach(function(button) {
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

            function syncDurationInput(selectElement, inputElement) {
                if (!selectElement || !inputElement) {
                    return;
                }

                const isMinutes = selectElement.value === 'minutes';
                inputElement.min = isMinutes ? '1' : '0.5';
                inputElement.step = isMinutes ? '1' : '0.5';
                inputElement.placeholder = isMinutes ? 'Contoh: 90' : 'Contoh: 24';
            }

            document.querySelectorAll('[data-duration-unit-select]').forEach(function(selectElement) {
                const key = selectElement.getAttribute('data-duration-unit-select');
                const inputElement = document.querySelector('[data-duration-input="' + key + '"]');

                syncDurationInput(selectElement, inputElement);
                selectElement.addEventListener('change', function() {
                    syncDurationInput(selectElement, inputElement);
                });
            });
        })();
    </script>
@endsection
