@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <!-- Header with Search and Add Button -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <form method="GET" action="{{ route('dashboard.instructor.modules') }}" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari modul..." value="{{ request('search', '') }}"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs sm:text-sm text-slate-700 placeholder-slate-500 focus:border-slate-500 focus:outline-none">
                    <button type="submit"
                        class="rounded-lg border border-slate-300 bg-white px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        Cari
                    </button>
                </form>
            </div>
            <a href="{{ route('dashboard.instructor.modules.create') }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Modul
            </a>
        </div>

        <!-- Modules Grid -->
        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($modules as $module)
                <article
                    class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-slate-300 hover:shadow-md">
                    <!-- Cover Image -->
                    <div class="relative h-40 w-full overflow-hidden bg-linear-to-br from-slate-100 to-slate-200 sm:h-48">
                        @if ($module['cover'])
                            <img src="{{ $module['cover'] }}" alt="{{ $module['title'] }}"
                                class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center bg-slate-100">
                                <svg class="h-12 w-12 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="absolute right-2 top-2">
                            <span
                                class="inline-block rounded-full border border-white/50 bg-slate-900/80 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur">
                                {{ $module['status'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4 sm:p-5">
                        <h3 class="line-clamp-2 text-sm font-bold text-slate-900">{{ $module['title'] }}</h3>
                        <p class="mt-1 text-xs text-slate-500">Durasi:
                            {{ $module['duration_label'] ?? $module['duration'] }}</p>

                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-600">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1">
                                <span class="font-semibold text-slate-800">{{ count($module['chapters'] ?? []) }}</span> bab
                            </span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1">
                                <span class="font-semibold text-slate-800">{{ $module['participants'] }}</span> peserta
                            </span>
                        </div>

                        <p class="mt-3 text-xs text-slate-500">Update: {{ $module['updated_at'] }}</p>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('dashboard.instructor.modules.detail', ['module' => $module['id']]) }}"
                                class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                                title="Lihat detail modul">
                                Lihat Detail
                            </a>
                            <a href="{{ route('dashboard.instructor.modules.edit', ['module' => $module['id']]) }}"
                                class="flex-1 rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200"
                                title="Edit modul">
                                Edit
                            </a>
                            <button type="button" data-delete-module="{{ $module['id'] }}"
                                data-delete-title="{{ $module['title'] }}"
                                class="delete-module-btn flex-1 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100"
                                title="Hapus modul">
                                Hapus
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if (count($modules) === 0)
            <div class="mt-8 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6v6m0 0v6m0-6h6m0 0h6M6 12a6 6 0 1112 0 6 6 0 01-12 0z" />
                </svg>
                <p class="mt-3 text-sm font-semibold text-slate-600">Belum ada modul</p>
                <p class="mt-1 text-xs text-slate-500">Mulai dengan membuat modul baru menggunakan tombol di atas.</p>
            </div>
        @endif
    </section>

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
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title">
                            Hapus Modul
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500" id="modal-message">
                                Apakah Anda yakin ingin menghapus modul ini? Tindakan ini tidak dapat dibatalkan.
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
            let moduleTitle = '';

            // Show modal
            function showModal(title, message, form) {
                moduleTitle = title;
                modalTitle.textContent = `Hapus Modul: ${title}`;
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
                moduleTitle = '';
            }

            // Handle delete button clicks
            document.querySelectorAll('.delete-module-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const moduleId = this.getAttribute('data-delete-module');
                    const title = this.getAttribute('data-delete-title');

                    // Create form dynamically
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/dashboard/penguji/kelola-modul/${moduleId}`;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken.getAttribute('content');
                        form.appendChild(csrfInput);
                    }

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    showModal(title,
                        `Apakah Anda yakin ingin menghapus modul "${title}"? Tindakan ini tidak dapat dibatalkan.`,
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
