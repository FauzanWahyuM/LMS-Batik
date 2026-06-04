@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @if (session('status'))
            <div
                class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('dashboard.participant.gallery') }}" class="flex flex-1 gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari judul karya"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <button type="submit"
                    class="rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-300 transition">Cari</button>
            </form>
            <a href="{{ route('dashboard.participant.gallery.upload') }}"
                class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 transition">Upload
                Karya</a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse (($artworks ?? collect()) as $artwork)
                <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="h-36 rounded-lg overflow-hidden bg-slate-100">
                        <img src="{{ route('public-file', ['path' => ltrim($artwork->image_path, '/')]) }}"
                            alt="{{ $artwork->title }}" width="800" height="450" class="h-full w-full object-cover"
                            loading="lazy" decoding="async">
                    </div>
                    <h3 class="mt-3 text-lg font-bold text-slate-800">{{ $artwork->title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ \Illuminate\Support\Str::limit($artwork->description, 140) }}
                    </p>
                    <div class="mt-3 flex justify-end">
                        <span
                            class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700">{{ $artwork->creator_name }}</span>
                    </div>
                    @if (!empty($user['email']) && $artwork->creator_email === $user['email'])
                        <div class="mt-4 flex items-center gap-2">
                            <a href="{{ route('dashboard.participant.gallery.edit', ['artwork' => $artwork->id]) }}"
                                class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-center text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                Edit
                            </a>
                            <form method="POST"
                                action="{{ route('dashboard.participant.gallery.delete', ['artwork' => $artwork->id]) }}"
                                class="flex-1 delete-artwork-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" data-delete-name="{{ $artwork->title }}"
                                    class="delete-artwork-btn w-full rounded-lg border border-rose-300 bg-white px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div
                    class="col-span-full rounded-xl border border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-600">
                    Belum ada karya yang diunggah.
                </div>
            @endforelse
        </div>

        <div id="delete-artwork-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
            <div class="absolute inset-0 bg-slate-900/60" id="delete-artwork-backdrop"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 9v4m0 4h.01M10.29 3.86l-7.4 12.75A2 2 0 004.63 20h14.74a2 2 0 001.74-3.39l-7.4-12.75a2 2 0 00-3.48 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 id="delete-artwork-title" class="text-lg font-bold text-slate-900">Hapus Karya</h3>
                        <p id="delete-artwork-message" class="mt-2 text-sm leading-relaxed text-slate-600">
                            Apakah Anda yakin ingin menghapus karya ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <button type="button" id="cancel-delete-artwork-btn"
                        class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="button" id="confirm-delete-artwork-btn"
                        class="flex-1 rounded-lg border border-rose-600 bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700">
                        Hapus
                    </button>
                </div>
            </div>
        </div>

    </section>

    <script>
        const deleteArtworkModal = document.getElementById('delete-artwork-modal');
        const deleteArtworkBackdrop = document.getElementById('delete-artwork-backdrop');
        const deleteArtworkTitle = document.getElementById('delete-artwork-title');
        const deleteArtworkMessage = document.getElementById('delete-artwork-message');
        const confirmDeleteArtworkBtn = document.getElementById('confirm-delete-artwork-btn');
        const cancelDeleteArtworkBtn = document.getElementById('cancel-delete-artwork-btn');

        let deleteArtworkForm = null;

        function showDeleteArtworkModal(name, form) {
            if (!deleteArtworkModal) {
                return;
            }

            deleteArtworkTitle.textContent = `Hapus Karya: ${name}`;
            deleteArtworkMessage.textContent =
                `Apakah Anda yakin ingin menghapus karya "${name}"? Tindakan ini tidak dapat dibatalkan.`;
            deleteArtworkForm = form;
            deleteArtworkModal.classList.remove('hidden');
            deleteArtworkModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function hideDeleteArtworkModal() {
            if (!deleteArtworkModal) {
                return;
            }

            deleteArtworkModal.classList.add('hidden');
            deleteArtworkModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            deleteArtworkForm = null;
        }

        document.querySelectorAll('.delete-artwork-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const name = button.getAttribute('data-delete-name') || 'karya ini';
                const form = button.closest('form');

                if (form) {
                    showDeleteArtworkModal(name, form);
                }
            });
        });

        if (confirmDeleteArtworkBtn) {
            confirmDeleteArtworkBtn.addEventListener('click', function() {
                if (deleteArtworkForm) {
                    deleteArtworkForm.submit();
                }
            });
        }

        if (cancelDeleteArtworkBtn) {
            cancelDeleteArtworkBtn.addEventListener('click', function() {
                hideDeleteArtworkModal();
            });
        }

        if (deleteArtworkBackdrop) {
            deleteArtworkBackdrop.addEventListener('click', function() {
                hideDeleteArtworkModal();
            });
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && deleteArtworkModal && !deleteArtworkModal.classList.contains('hidden')) {
                hideDeleteArtworkModal();
            }
        });
    </script>
@endsection
