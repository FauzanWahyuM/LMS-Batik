@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Kelola Testimoni</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali</a>
        </div>

        @if ($errors->has('testimonials'))
            <div
                class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 sm:text-sm">
                {{ $errors->first('testimonials') }}
            </div>
        @endif

        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('dashboard.manager.testimonials') }}"
                class="flex w-full gap-2 flex-col sm:flex-row">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama/role/testimoni"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-slate-500 focus:outline-none">
                <button type="submit"
                    class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cari</button>
            </form>
            <button type="button" id="open-create-testimonial"
                class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Create
                Testimoni</button>
        </div>

        <div id="create-testimonial-form" class="mt-4 hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="text-sm font-semibold text-slate-900">Tambah Testimoni</h3>
            <form method="POST" action="{{ route('dashboard.manager.testimonials.store') }}"
                class="mt-3 grid gap-3 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Nama</label>
                    <input type="text" name="name" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Role/Keterangan</label>
                    <input type="text" name="role_label"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                        placeholder="Contoh: Peserta Program Batik Dasar">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Isi Testimoni</label>
                    <textarea name="quote" rows="4" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input type="checkbox" id="is_active_create_testimonial" name="is_active" value="1" checked>
                    <label for="is_active_create_testimonial" class="text-xs text-slate-700">Tampilkan di beranda</label>
                </div>
                <div class="sm:col-span-2 flex flex-col sm:flex-row sm:justify-end gap-2">
                    <button type="button" id="cancel-create-testimonial"
                        class="w-full sm:w-auto rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                    <button type="submit"
                        class="w-full sm:w-auto rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                </div>
            </form>
        </div>

        <div class="mt-5 space-y-3 max-w-7xl mx-auto">
            @forelse ($testimonials as $testimonial)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-900">{{ $testimonial->name }}</p>
                            <p class="text-xs text-slate-600">{{ $testimonial->role_label ?: '-' }}</p>
                            <p class="text-xs text-slate-500 italic">"{{ $testimonial->quote }}"</p>
                            <p
                                class="text-xs font-semibold {{ $testimonial->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                {{ $testimonial->is_active ? 'Aktif ditampilkan' : 'Tidak ditampilkan' }}
                            </p>
                        </div>
                        <div class="flex w-full flex-col gap-2 sm:w-32">
                            <button type="button" data-toggle="edit-{{ $testimonial->id }}"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Edit</button>
                            <form method="POST"
                                action="{{ route('dashboard.manager.testimonials.delete', ['testimonial' => $testimonial->id]) }}"
                                onsubmit="return confirm('Hapus testimoni ini?')">
                                @csrf
                                <button type="submit"
                                    class="w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <div id="edit-{{ $testimonial->id }}"
                        class="mt-4 hidden rounded-lg border border-slate-200 bg-white p-4">
                        <h4 class="mb-3 text-sm font-semibold text-slate-900">Edit Testimoni</h4>
                        <form method="POST"
                            action="{{ route('dashboard.manager.testimonials.edit', ['testimonial' => $testimonial->id]) }}"
                            class="grid gap-3 sm:grid-cols-2">
                            @csrf
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Nama</label>
                                <input type="text" name="name" value="{{ $testimonial->name }}" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Role/Keterangan</label>
                                <input type="text" name="role_label" value="{{ $testimonial->role_label }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Isi Testimoni</label>
                                <textarea name="quote" rows="4" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $testimonial->quote }}</textarea>
                            </div>
                            <div class="flex items-center gap-2 pt-6">
                                <input type="checkbox" id="is_active_testimonial_{{ $testimonial->id }}" name="is_active"
                                    value="1" {{ $testimonial->is_active ? 'checked' : '' }}>
                                <label for="is_active_testimonial_{{ $testimonial->id }}"
                                    class="text-xs text-slate-700">Tampilkan di beranda</label>
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
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">Testimoni tidak
                    ditemukan.</div>
            @endforelse
        </div>
    </section>

    <script>
        (function() {
            const openButton = document.getElementById('open-create-testimonial');
            const cancelButton = document.getElementById('cancel-create-testimonial');
            const createForm = document.getElementById('create-testimonial-form');

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
        })();
    </script>
@endsection
