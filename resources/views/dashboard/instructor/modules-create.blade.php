@extends('dashboard.layouts.app')

@section('dashboard-content')
    <div class="max-w-4xl mx-auto">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                <h2 class="text-lg font-bold text-slate-900">Tambah Modul Baru</h2>
                <a href="{{ route('dashboard.instructor.modules') }}"
                    class="w-full sm:w-auto text-center rounded-lg border border-slate-300 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100">
                    Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('dashboard.instructor.modules.store') }}" enctype="multipart/form-data"
                class="space-y-5">
                @csrf

                <!-- Module Name -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-700">Nama Modul</label>
                    <input type="text" id="title" name="title" required placeholder="contoh: Teknik Canting Dasar"
                        value="{{ old('title') }}"
                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-500 focus:outline-none">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration" class="block text-sm font-semibold text-slate-700">Durasi Modul</label>
                    <input type="number" id="duration" name="duration" required min="0.25" step="0.25"
                        placeholder="contoh: 72" value="{{ old('duration') }}"
                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-500 focus:outline-none">
                    <p class="mt-1 text-xs text-slate-500">Durasi disimpan otomatis dalam satuan jam.</p>
                    @error('duration')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cover Image -->
                <div>
                    <label for="cover" class="block text-sm font-semibold text-slate-700">Gambar Sampul Modul</label>
                    <div class="mt-2">
                        <div class="relative">
                            <label id="cover-upload-area" for="cover"
                                class="flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 transition hover:border-slate-400 hover:bg-slate-100">
                                <div class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm font-semibold text-slate-600">Pilih gambar</p>
                                    <p class="text-xs text-slate-500">atau drag & drop</p>
                                </div>
                            </label>
                            <input type="file" id="cover" name="cover" accept="image/*" class="sr-only">
                        </div>
                        <div id="cover-preview"
                            class="hidden mt-4 rounded-lg border-2 border-slate-200 bg-slate-50 px-6 py-6">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <svg class="h-10 w-10 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                        </path>
                                    </svg>
                                    <div class="min-w-0">
                                        <p id="cover-file-name" class="truncate text-sm font-semibold text-slate-900"></p>
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
                    </div>
                    @error('cover')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700">Deskripsi Modul</label>
                    <textarea id="description" name="description" rows="4" placeholder="Jelaskan tujuan dan konten utama modul ini..."
                        value="{{ old('description') }}"
                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-500 focus:outline-none">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-3 sm:flex-row pt-4">
                    <button type="submit"
                        class="w-full sm:flex-1 rounded-lg bg-slate-900 px-4 py-2 text-xs sm:text-sm font-semibold text-white transition hover:bg-slate-800">
                        Buat Modul
                    </button>
                    <a href="{{ route('dashboard.instructor.modules') }}"
                        class="w-full sm:flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        Batal
                    </a>
                </div>
            </form>

            <p class="mt-6 text-xs text-slate-500 border-t border-slate-200 pt-6">
                💡 Setelah membuat modul, Anda dapat menambahkan bab dan materi pembelajaran melalui halaman edit module.
            </p>
        </section>
    </div>

    <script>
        const coverInput = document.getElementById('cover');
        const coverUploadArea = document.getElementById('cover-upload-area');
        const coverPreview = document.getElementById('cover-preview');
        const coverFileName = document.getElementById('cover-file-name');
        const coverFileSize = document.getElementById('cover-file-size');
        const coverEditBtn = document.getElementById('edit-cover-btn');
        const coverRemoveBtn = document.getElementById('remove-cover-btn');

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }

        function showCoverPreview() {
            if (!coverInput.files || !coverInput.files[0]) {
                return;
            }

            const file = coverInput.files[0];
            coverFileName.textContent = file.name;
            coverFileSize.textContent = formatFileSize(file.size);
            coverUploadArea.classList.add('hidden');
            coverPreview.classList.remove('hidden');
        }

        function clearCoverPreview() {
            coverInput.value = '';
            coverPreview.classList.add('hidden');
            coverUploadArea.classList.remove('hidden');
            coverFileName.textContent = '';
            coverFileSize.textContent = '';
        }

        coverInput.addEventListener('change', showCoverPreview);
        coverEditBtn?.addEventListener('click', () => coverInput.click());
        coverRemoveBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            clearCoverPreview();
        });
    </script>
@endsection
