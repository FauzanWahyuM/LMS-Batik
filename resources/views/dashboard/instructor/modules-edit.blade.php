@extends('dashboard.layouts.app')

@section('dashboard-content')
    <div class="max-w-6xl mx-auto">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <h2 class="text-lg font-bold text-slate-900">Edit Modul: {{ $module['title'] }}</h2>
                <a href="{{ route('dashboard.instructor.modules') }}"
                    class="w-full sm:w-auto text-center rounded-lg border border-slate-300 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100">
                    Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('dashboard.instructor.modules.update', ['module' => $module['id']]) }}"
                enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Module Basic Information -->
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Informasi Modul</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-slate-700">Nama Modul</label>
                            <input type="text" id="title" name="title" required
                                value="{{ old('title', $module['title']) }}"
                                class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                            @error('title')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-semibold text-slate-700">Durasi Modul</label>
                            <input type="text" id="duration" name="duration" required
                                value="{{ old('duration', $module['duration']) }}"
                                class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                            @error('duration')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="cover" class="block text-sm font-semibold text-slate-700">Gambar Sampul Modul
                                (Opsional)</label>
                            <div class="mt-2 flex items-center justify-center">
                                <label
                                    class="flex h-24 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-white transition hover:border-slate-400 hover:bg-slate-50">
                                    <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Ganti gambar</p>
                                    <input type="file" id="cover" name="cover" accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-slate-700">Deskripsi
                                Modul</label>
                            <textarea id="description" name="description" rows="3"
                                class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">{{ old('description', $module['description']) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Chapters Management -->
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-900">Daftar Bab</h3>
                        <button type="button" id="add-chapter-btn"
                            class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            + Tambah Bab
                        </button>
                    </div>

                    <div id="chapters-container" class="space-y-4">
                        @foreach ($module['chapters'] as $idx => $chapter)
                            @include('dashboard.instructor.partials.chapter-form', [
                                'chapter' => $chapter,
                                'index' => $idx,
                            ])
                        @endforeach
                    </div>

                    @if (empty($module['chapters']))
                        <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center">
                            <p class="text-sm text-slate-600">Belum ada bab. Tambahkan bab pertama Anda sekarang.</p>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3 sm:flex-row pt-4">
                    <button type="submit"
                        class="w-full sm:flex-1 rounded-lg bg-slate-900 px-4 py-2 text-xs sm:text-sm font-semibold text-white transition hover:bg-slate-800">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('dashboard.instructor.modules') }}"
                        class="w-full sm:flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        Batal
                    </a>
                </div>
            </form>
        </section>
    </div>

    <script>
        let chapterCount = {{ count($module['chapters']) }};

        document.getElementById('add-chapter-btn').addEventListener('click', function() {
            const container = document.getElementById('chapters-container');
            const template = document.createElement('div');

            const chapterIndex = chapterCount++;
            template.innerHTML = `
            <div class="rounded-lg border border-slate-300 bg-white p-4" data-chapter-index="${chapterIndex}">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-200">
                    <h4 class="font-semibold text-slate-900">Bab Baru</h4>
                    <button type="button" class="remove-chapter-btn text-red-600 hover:text-red-700 text-xs font-semibold">
                        Hapus
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Judul Bab</label>
                        <input type="text" name="chapters[${chapterIndex}][title]" placeholder="contoh: Bab 1 - Pengenalan Alat"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Bab</label>
                        <textarea name="chapters[${chapterIndex}][description]" rows="2" placeholder="Penjelasan singkat tentang bab ini..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Konten Pembelajaran</label>
                        <textarea name="chapters[${chapterIndex}][content]" rows="3" placeholder="Tuliskan konten pembelajaran..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Link Video (YouTube, Vimeo, dll - embed link)</label>
                        <input type="url" name="chapters[${chapterIndex}][video]" placeholder="https://www.youtube.com/embed/..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tugas Pembelajaran</label>
                        <textarea name="chapters[${chapterIndex}][assignment]" rows="2" placeholder="Tugas atau aktivitas untuk peserta..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deadline Tugas</label>
                        <input type="text" name="chapters[${chapterIndex}][assignment_deadline]" placeholder="contoh: 31 Desember 2026"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                    </div>
                </div>
            </div>
            `;

            container.appendChild(template);

            template.querySelector('.remove-chapter-btn').addEventListener('click', function(e) {
                e.preventDefault();
                template.remove();
            });

            container.scrollIntoView({
                behavior: 'smooth',
                block: 'end'
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-chapter-btn')) {
                e.preventDefault();
                e.target.closest('[data-chapter-index]').remove();
            }
        });
    </script>
@endsection
