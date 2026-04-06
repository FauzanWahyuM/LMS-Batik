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
                @method('PUT')

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
                            <div class="mt-2">
                                <div id="current-cover-area"
                                    class="relative rounded-lg overflow-hidden border border-slate-300 {{ $module['cover'] ? '' : 'hidden' }}">
                                    @if ($module['cover'])
                                        <img src="{{ $module['cover'] }}" alt="Gambar sampul saat ini"
                                            class="w-full h-32 object-cover">
                                        <div class="absolute top-2 right-2 flex gap-2">
                                            <button type="button" id="replace-cover-btn"
                                                class="rounded bg-slate-900 px-2 py-1 text-xs text-white hover:bg-slate-800">
                                                Edit
                                            </button>
                                            <button type="button" id="delete-cover-btn"
                                                class="rounded bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-700">
                                                Hapus
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <label id="cover-upload-area" for="cover"
                                    class="flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 transition hover:border-slate-400 hover:bg-slate-100 {{ $module['cover'] ? 'hidden' : '' }}">
                                    <div class="text-center">
                                        <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs font-semibold text-slate-600">Pilih gambar sampul</p>
                                    </div>
                                </label>

                                <input type="file" id="cover" name="cover" accept="image/*" class="sr-only">
                                <input type="checkbox" id="delete_cover" name="delete_cover" value="1" class="hidden">

                                <div id="cover-preview"
                                    class="hidden mt-4 rounded-lg border-2 border-slate-200 bg-slate-50 px-6 py-6">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <svg class="h-10 w-10 text-blue-600 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                                </path>
                                            </svg>
                                            <div class="min-w-0">
                                                <p id="cover-file-name"
                                                    class="truncate text-sm font-semibold text-slate-900"></p>
                                                <p id="cover-file-size" class="text-xs text-slate-600"></p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button id="edit-cover-btn" type="button"
                                                class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                                                Edit
                                            </button>
                                            <button id="remove-cover-btn" type="button"
                                                class="rounded-lg bg-red-100 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-200">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <p class="mt-2 text-xs text-slate-500">Klik "Edit" untuk mengganti file atau "Hapus" untuk
                                    mengosongkan cover dan menghapusnya dari database.</p>
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
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Link Video (YouTube, Vimeo, dll)</label>
                        <input type="url" name="chapters[${chapterIndex}][video]" placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                        <p class="mt-1 text-xs text-slate-500">Masukkan URL video YouTube atau Vimeo. Sistem akan otomatis mengkonversi ke format embed.</p>
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

        const coverInput = document.getElementById('cover');
        const coverUploadArea = document.getElementById('cover-upload-area');
        const coverPreview = document.getElementById('cover-preview');
        const coverFileName = document.getElementById('cover-file-name');
        const coverFileSize = document.getElementById('cover-file-size');
        const coverEditBtn = document.getElementById('edit-cover-btn');
        const coverRemoveBtn = document.getElementById('remove-cover-btn');
        const replaceCoverBtn = document.getElementById('replace-cover-btn');
        const currentCoverArea = document.getElementById('current-cover-area');
        const deleteCoverCheckbox = document.getElementById('delete_cover');

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }

        function showCoverUploadArea() {
            coverUploadArea?.classList.remove('hidden');
            currentCoverArea?.classList.add('hidden');
            coverPreview?.classList.add('hidden');
            if (deleteCoverCheckbox) deleteCoverCheckbox.checked = false;
        }

        function showCoverPreview() {
            if (!coverInput || !coverInput.files || !coverInput.files[0]) {
                return;
            }

            const file = coverInput.files[0];
            coverFileName.textContent = file.name;
            coverFileSize.textContent = formatFileSize(file.size);
            coverPreview?.classList.remove('hidden');
            coverUploadArea?.classList.add('hidden');
            currentCoverArea?.classList.add('hidden');
            if (deleteCoverCheckbox) deleteCoverCheckbox.checked = false;
        }

        function clearExistingCover() {
            if (deleteCoverCheckbox) deleteCoverCheckbox.checked = true;
            if (coverInput) coverInput.value = '';
            coverPreview?.classList.add('hidden');
            coverUploadArea?.classList.remove('hidden');
            currentCoverArea?.classList.add('hidden');
            if (coverFileName) coverFileName.textContent = '';
            if (coverFileSize) coverFileSize.textContent = '';
        }

        replaceCoverBtn?.addEventListener('click', function() {
            coverInput?.click();
        });

        coverInput?.addEventListener('change', function() {
            if (coverInput.files && coverInput.files[0]) {
                showCoverPreview();
            } else {
                showCoverUploadArea();
            }
        });

        coverEditBtn?.addEventListener('click', function() {
            coverInput?.click();
        });

        coverRemoveBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            clearExistingCover();
        });
    </script>
@endsection
