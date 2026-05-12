@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Kelola Gudang</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali</a>
        </div>

        <div id="storage-feedback" class="mt-4 hidden rounded-lg px-3 py-2 text-xs font-medium sm:text-sm"></div>

        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
            <form id="search-material-form" class="flex w-full gap-2 flex-col sm:flex-row">
                <input type="text" id="search-material-input" name="search" placeholder="Cari nama atau kategori bahan"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-slate-500 focus:outline-none">
                <button type="submit"
                    class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cari</button>
            </form>
            <button type="button" id="open-create-material"
                class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Tambah
                Bahan</button>
        </div>

        <div id="create-material-wrapper" class="mt-4 hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="text-sm font-semibold text-slate-900">Tambah Bahan</h3>
            <form id="create-material-form" enctype="multipart/form-data" class="mt-3 grid gap-3 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Bahan</label>
                    <input type="text" name="name" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Contoh: Kain Katun">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Kategori Bahan</label>
                    <input type="text" name="category" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Contoh: Kain / Pewarna / Alat">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Satuan</label>
                    <input type="text" name="unit" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Contoh: meter, kg, pak">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Jumlah Stok</label>
                    <input type="number" name="stock" min="0" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="0">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Minimum Stok</label>
                    <input type="number" name="minimum_stock" min="0" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="0">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Upload Gambar Bahan</label>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi Bahan</label>
                    <textarea name="description" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Tuliskan deskripsi singkat bahan"></textarea>
                </div>

                <div class="sm:col-span-2 flex flex-col sm:flex-row sm:justify-end gap-2">
                    <button type="button" id="cancel-create-material"
                        class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                </div>
            </form>
        </div>

        <div class="mt-5 overflow-hidden rounded-xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Gambar
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Nama
                                Bahan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Kategori
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Stok
                                Tersedia</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Minimum
                                Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Status
                                Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="materials-table-body" class="divide-y divide-slate-200 bg-white">
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-600">Memuat data bahan...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div id="delete-modal-material" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" id="modal-backdrop-material">
            </div>

            <div
                class="inline-block transform overflow-hidden rounded-2xl bg-white px-4 pt-5 pb-4 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2.032 2.032 0 0116.138 21H7.862a2.032 2.032 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title-material">Hapus Bahan
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500" id="modal-message-material">
                                Apakah Anda yakin ingin menghapus data bahan ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" id="confirm-delete-btn-material"
                        class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 sm:w-auto">Hapus</button>
                    <button type="button" id="cancel-delete-btn-material"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const apiBaseUrl = '/api/v1/manager/storage-materials';
            const fileBaseUrl = '{{ url('/files') }}/';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const feedbackEl = document.getElementById('storage-feedback');
            const searchForm = document.getElementById('search-material-form');
            const searchInput = document.getElementById('search-material-input');
            const tableBody = document.getElementById('materials-table-body');

            const createWrapper = document.getElementById('create-material-wrapper');
            const openCreateBtn = document.getElementById('open-create-material');
            const cancelCreateBtn = document.getElementById('cancel-create-material');
            const createForm = document.getElementById('create-material-form');

            const modal = document.getElementById('delete-modal-material');
            const backdrop = document.getElementById('modal-backdrop-material');
            const modalTitle = document.getElementById('modal-title-material');
            const modalMessage = document.getElementById('modal-message-material');
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn-material');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn-material');

            let deletingMaterial = null;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function showFeedback(message, type = 'success') {
                if (!feedbackEl) {
                    return;
                }

                feedbackEl.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800',
                    'border-rose-200',
                    'bg-rose-50', 'text-rose-700');

                if (type === 'error') {
                    feedbackEl.classList.add('border', 'border-rose-200', 'bg-rose-50', 'text-rose-700');
                } else {
                    feedbackEl.classList.add('border', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800');
                }

                feedbackEl.textContent = message;
            }

            function hideFeedback() {
                if (!feedbackEl) {
                    return;
                }
                feedbackEl.classList.add('hidden');
                feedbackEl.textContent = '';
            }

            function getStatusBadge(stock, minimumStock) {
                if (stock <= 0) {
                    return {
                        label: 'Stok Habis',
                        className: 'bg-rose-100 text-rose-700'
                    };
                }

                if (stock <= minimumStock) {
                    return {
                        label: 'Hampir Habis',
                        className: 'bg-amber-100 text-amber-700'
                    };
                }

                return {
                    label: 'Stok Aman',
                    className: 'bg-emerald-100 text-emerald-700'
                };
            }

            function buildRow(material) {
                const stock = Number(material.stock || 0);
                const minimumStock = Number(material.minimum_stock || 0);
                const status = getStatusBadge(stock, minimumStock);

                const imageHtml = material.image_path ?
                    `<img src="${fileBaseUrl}${encodeURIComponent(material.image_path).replace(/%2F/g, '/')}" alt="${escapeHtml(material.name)}" class="h-14 w-14 rounded-lg border border-slate-200 object-cover">` :
                    `<div class="flex h-14 w-14 items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-[11px] text-slate-500">No Image</div>`;

                return `
                    <tr class="align-top" data-material-id="${material.id}">
                        <td class="px-4 py-3">${imageHtml}</td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-900">${escapeHtml(material.name)}</p>
                            <p class="mt-1 text-xs text-slate-500">Satuan: ${escapeHtml(material.unit || 'unit')}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-700">${escapeHtml(material.category || '-')}</td>
                        <td class="px-4 py-3 text-slate-700">${stock} ${escapeHtml(material.unit || '')}</td>
                        <td class="px-4 py-3 text-slate-700">${minimumStock} ${escapeHtml(material.unit || '')}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ${status.className}">${status.label}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <button type="button" data-edit-toggle="edit-material-${material.id}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Edit</button>
                                <button type="button" data-delete-id="${material.id}" data-delete-name="${escapeHtml(material.name)}" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    <tr id="edit-material-${material.id}" class="hidden bg-slate-50">
                        <td colspan="7" class="px-4 py-4">
                            <div class="rounded-lg border border-slate-200 bg-white p-4">
                                <h4 class="mb-3 text-sm font-semibold text-slate-900">Edit Bahan</h4>
                                <form class="edit-material-form grid gap-3 sm:grid-cols-2" data-edit-id="${material.id}" enctype="multipart/form-data">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Nama Bahan</label>
                                        <input type="text" name="name" value="${escapeHtml(material.name)}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Kategori Bahan</label>
                                        <input type="text" name="category" value="${escapeHtml(material.category)}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Satuan</label>
                                        <input type="text" name="unit" value="${escapeHtml(material.unit)}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Jumlah Stok</label>
                                        <input type="number" name="stock" min="0" value="${stock}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Minimum Stok</label>
                                        <input type="number" name="minimum_stock" min="0" value="${minimumStock}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Upload Gambar Bahan</label>
                                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">Deskripsi Bahan</label>
                                        <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">${escapeHtml(material.description || '')}</textarea>
                                    </div>
                                    <div class="sm:col-span-2 flex justify-end">
                                        <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>`;
            }

            async function parseResponse(response) {
                let result = null;
                try {
                    result = await response.json();
                } catch (error) {
                    result = null;
                }

                if (!response.ok || !result || result.success !== true) {
                    const errorMessage = result?.message || 'Terjadi kesalahan saat memproses permintaan.';
                    throw new Error(errorMessage);
                }

                return result;
            }

            async function loadMaterials(searchValue = '') {
                if (!tableBody) {
                    return;
                }

                tableBody.innerHTML =
                    '<tr><td colspan="7" class="px-4 py-6 text-center text-sm text-slate-600">Memuat data bahan...</td></tr>';

                const queryString = searchValue ? `?search=${encodeURIComponent(searchValue)}` : '';
                const response = await fetch(`${apiBaseUrl}${queryString}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const result = await parseResponse(response);
                const materials = Array.isArray(result.data) ? result.data : [];

                if (materials.length === 0) {
                    tableBody.innerHTML =
                        '<tr><td colspan="7" class="px-4 py-6 text-center text-sm text-slate-600">Data bahan belum tersedia.</td></tr>';
                    return;
                }

                tableBody.innerHTML = materials.map(buildRow).join('');
                attachRowEvents();
            }

            async function createMaterial(event) {
                event.preventDefault();
                hideFeedback();

                const formData = new FormData(createForm);
                const response = await fetch(apiBaseUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: formData,
                });

                const result = await parseResponse(response);
                createForm.reset();
                createWrapper.classList.add('hidden');
                showFeedback(result.message || 'Bahan berhasil ditambahkan.');
                await loadMaterials(searchInput?.value?.trim() || '');
            }

            async function updateMaterial(event, materialId) {
                event.preventDefault();
                hideFeedback();

                const form = event.currentTarget;
                const formData = new FormData(form);
                const response = await fetch(`${apiBaseUrl}/${materialId}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: (() => {
                        formData.append('_method', 'PUT');
                        return formData;
                    })(),
                });

                const result = await parseResponse(response);
                showFeedback(result.message || 'Bahan berhasil diperbarui.');
                await loadMaterials(searchInput?.value?.trim() || '');
            }

            function openDeleteModal(id, name) {
                deletingMaterial = id;
                modalTitle.textContent = `Hapus Bahan: ${name}`;
                modalMessage.textContent =
                    `Apakah Anda yakin ingin menghapus bahan "${name}"? Tindakan ini tidak dapat dibatalkan.`;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeDeleteModal() {
                deletingMaterial = null;
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            async function deleteMaterial() {
                if (!deletingMaterial) {
                    return;
                }

                hideFeedback();
                const response = await fetch(`${apiBaseUrl}/${deletingMaterial}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: new URLSearchParams({
                        _method: 'DELETE'
                    }),
                });

                const result = await parseResponse(response);
                closeDeleteModal();
                showFeedback(result.message || 'Bahan berhasil dihapus.');
                await loadMaterials(searchInput?.value?.trim() || '');
            }

            function attachRowEvents() {
                document.querySelectorAll('[data-edit-toggle]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const targetId = button.getAttribute('data-edit-toggle');
                        const target = document.getElementById(targetId);
                        if (target) {
                            target.classList.toggle('hidden');
                        }
                    });
                });

                document.querySelectorAll('[data-delete-id]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        openDeleteModal(button.getAttribute('data-delete-id'), button.getAttribute(
                            'data-delete-name') || 'ini');
                    });
                });

                document.querySelectorAll('.edit-material-form').forEach(function(form) {
                    const materialId = form.getAttribute('data-edit-id');
                    form.addEventListener('submit', function(event) {
                        updateMaterial(event, materialId).catch(function(error) {
                            showFeedback(error.message, 'error');
                        });
                    });
                });
            }

            if (searchForm) {
                searchForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    hideFeedback();
                    loadMaterials(searchInput?.value?.trim() || '').catch(function(error) {
                        showFeedback(error.message, 'error');
                    });
                });
            }

            if (openCreateBtn && createWrapper) {
                openCreateBtn.addEventListener('click', function() {
                    createWrapper.classList.remove('hidden');
                });
            }

            if (cancelCreateBtn && createWrapper) {
                cancelCreateBtn.addEventListener('click', function() {
                    createWrapper.classList.add('hidden');
                });
            }

            if (createForm) {
                createForm.addEventListener('submit', function(event) {
                    createMaterial(event).catch(function(error) {
                        showFeedback(error.message, 'error');
                    });
                });
            }

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function() {
                    deleteMaterial().catch(function(error) {
                        showFeedback(error.message, 'error');
                    });
                });
            }

            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener('click', closeDeleteModal);
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeDeleteModal);
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
            });

            const initialSearch = new URLSearchParams(window.location.search).get('search') || '';
            if (searchInput) {
                searchInput.value = initialSearch;
            }

            loadMaterials(initialSearch).catch(function(error) {
                showFeedback(error.message, 'error');
                if (tableBody) {
                    tableBody.innerHTML =
                        '<tr><td colspan="7" class="px-4 py-6 text-center text-sm text-rose-700">Gagal memuat data bahan.</td></tr>';
                }
            });
        })();
    </script>
@endsection
