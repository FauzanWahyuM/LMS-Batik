@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Kelola Prestasi</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali</a>
        </div>

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
                            <p class="text-xs text-slate-600">Event: {{ $achievement->event_name }}</p>
                            <p class="text-xs text-slate-600">Pemenang: {{ $achievement->winner_name }}</p>
                            <p class="text-xs text-slate-600">Peringkat:
                                {{ $achievement->rank ? 'Juara ' . $achievement->rank : 'Non-ranking' }}</p>
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
                                action="{{ route('dashboard.manager.achievements.delete', ['achievement' => $achievement->id]) }}"
                                onsubmit="return confirm('Hapus prestasi ini?')">
                                @csrf
                                <button type="submit"
                                    class="w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700">Hapus</button>
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

    <script>
        (function() {
            const openButton = document.getElementById('open-create-achievement');
            const cancelButton = document.getElementById('cancel-create-achievement');
            const createForm = document.getElementById('create-achievement-form');

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
        })();
    </script>
@endsection
