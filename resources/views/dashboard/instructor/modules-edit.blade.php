@extends('dashboard.layouts.app')

@section('dashboard-content')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
        .ql-editor img {
            max-width: 100%;
            height: auto;
        }
    </style>
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
                            <input type="number" id="duration" name="duration" required min="0.25" step="0.25"
                                value="{{ old('duration', $module['duration']) }}"
                                class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                            <p class="mt-1 text-xs text-slate-500">Durasi disimpan otomatis dalam satuan jam.</p>
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
                                            <svg class="h-10 w-10 text-blue-600 shrink-0" fill="currentColor"
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

                    <div id="new-chapter-container" class="space-y-4"></div>

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

            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="text-base font-bold text-slate-900">Diskusi Modul</h3>
                    <span class="text-xs text-slate-500">{{ count($moduleDiscussions ?? []) }} pertanyaan</span>
                </div>
                @include('forum.module-discussions-list', [
                    'discussions' => $moduleDiscussions ?? collect(),
                    'user' => $user ?? [],
                    'moduleContext' => true,
                    'showModuleFilter' => false,
                    'moduleSlug' => $module['slug'] ?? '',
                    'moduleTitle' => $module['title'] ?? null,
                    'selectedModuleSlug' => $module['slug'] ?? '',
                ])
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        let chapterCount = {{ count($module['chapters']) }};

        const quillEditors = new Map();

        function syncEditorToInput(editorId) {
            const quill = quillEditors.get(editorId);
            const input = document.querySelector(`[data-rich-editor-target="${editorId}"]`);
            if (!quill || !input) {
                return;
            }
            input.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
        }

        function initRichEditors(root = document) {
            root.querySelectorAll('[data-rich-editor]').forEach((container) => {
                const editorId = container.dataset.richEditor;

                if (!editorId || quillEditors.has(editorId)) {
                    return;
                }

                const input = document.querySelector(`[data-rich-editor-target="${editorId}"]`);
                if (!input) {
                    return;
                }

                const modulesConfig = {
                    toolbar: {
                        container: [
                            [{
                                header: [1, 2, 3, false]
                            }],
                            ['bold', 'italic', 'underline'],
                            [{
                                list: 'ordered'
                            }, {
                                list: 'bullet'
                            }],
                            [{
                                align: []
                            }],
                            ['link'],
                            ['clean'],
                        ],
                    },
                };

                const quill = new Quill(container, {
                    theme: 'snow',
                    placeholder: 'Tuliskan konten pembelajaran...',
                    modules: modulesConfig,
                });

                const initialHtml = input.value || '';
                if (initialHtml.trim() !== '') {
                    quill.clipboard.dangerouslyPasteHTML(initialHtml);
                }
                quill.on('text-change', () => syncEditorToInput(editorId));
                quillEditors.set(editorId, quill);
            });
        }

        function initVideoSourceSelectors(root = document) {
            root.querySelectorAll('[data-chapter-index]').forEach((chapterCard) => {
                const selector = chapterCard.querySelector('[data-video-source]');
                const linkWrapper = chapterCard.querySelector('[data-video-link-wrapper]');
                const uploadWrapper = chapterCard.querySelector('[data-video-upload-wrapper]');

                if (!selector || selector.dataset.bound === '1') {
                    return;
                }

                const syncVisibility = () => {
                    const value = selector.value;
                    linkWrapper?.classList.toggle('hidden', value !== 'link');
                    uploadWrapper?.classList.toggle('hidden', value !== 'upload');
                };

                selector.addEventListener('change', syncVisibility);
                selector.dataset.bound = '1';
                syncVisibility();
            });
        }

        function initImageWidthSelectors(root = document) {
            root.querySelectorAll('[data-image-width-range]').forEach((rangeInput) => {
                if (rangeInput.dataset.bound === '1') {
                    return;
                }

                const label = rangeInput.parentElement?.querySelector('[data-image-width-label]');
                const syncLabel = () => {
                    if (label) {
                        label.textContent = rangeInput.value;
                    }
                };

                rangeInput.addEventListener('input', syncLabel);
                rangeInput.dataset.bound = '1';
                syncLabel();
            });
        }

        function createImageBlockHtml(chapterIndex, imageIndex) {
            return `
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm" data-image-item data-image-index="${imageIndex}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Gambar ${imageIndex + 1}</p>
                            <p class="text-xs text-slate-500">Upload, judul, caption, dan ukuran tampil.</p>
                        </div>
                        <button type="button" data-remove-image-btn class="rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                            Hapus
                        </button>
                    </div>

                    <div class="mt-4 grid gap-4 lg:grid-cols-[220px_1fr]">
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                            <div data-image-preview-wrap class="hidden">
                                <img alt="Pratinjau gambar" class="h-40 w-full object-cover" data-image-preview>
                            </div>
                            <div data-image-placeholder class="flex h-40 items-center justify-center px-4 text-center text-xs text-slate-500">
                                Pratinjau gambar akan muncul di sini.
                            </div>
                            <div class="border-t border-slate-200 bg-white p-3 text-xs text-slate-500" data-image-preview-meta>
                                <p class="font-semibold text-slate-700">Nama file: <span data-image-file-name>Belum dipilih</span></p>
                                <p class="mt-1">Ukuran file: <span data-image-file-size>-</span></p>
                                <p class="mt-1">Approx. display: <span data-image-display-label>75</span>%</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">File Gambar</label>
                                    <input type="file" name="chapters[${chapterIndex}][images][${imageIndex}][image_upload]" accept="image/*"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold"
                                        data-image-upload>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-600">Lebar Tampil (25%-100%)</label>
                                    <input type="range" min="25" max="100" step="5"
                                        name="chapters[${chapterIndex}][images][${imageIndex}][width]"
                                        value="75" class="w-full" data-image-width-range>
                                    <p class="mt-1 text-xs text-slate-500">Approx. display: <span data-image-width-label>75</span>%</p>
                                </div>
                            </div>

                            <input type="hidden" name="chapters[${chapterIndex}][images][${imageIndex}][existing_path]" value="" data-existing-image-path>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Judul Gambar</label>
                                <input type="text" name="chapters[${chapterIndex}][images][${imageIndex}][title]"
                                    placeholder="Contoh: Pola Dasar Batik"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-600">Caption Gambar</label>
                                <textarea name="chapters[${chapterIndex}][images][${imageIndex}][caption]" rows="2"
                                    placeholder="Penjelasan singkat tentang gambar..."
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function syncImageBlockPreview(imageBlock) {
            const fileInput = imageBlock.querySelector('[data-image-upload]');
            const previewWrap = imageBlock.querySelector('[data-image-preview-wrap]');
            const preview = imageBlock.querySelector('[data-image-preview]');
            const placeholder = imageBlock.querySelector('[data-image-placeholder]');
            const fileName = imageBlock.querySelector('[data-image-file-name]');
            const fileSize = imageBlock.querySelector('[data-image-file-size]');
            const widthRange = imageBlock.querySelector('[data-image-width-range]');
            const widthLabel = imageBlock.querySelector('[data-image-width-label]');
            const displayLabel = imageBlock.querySelector('[data-image-display-label]');

            const updateDisplayLabel = () => {
                const widthValue = widthRange?.value || '75';
                if (widthLabel) widthLabel.textContent = widthValue;
                if (displayLabel) displayLabel.textContent = widthValue;
                if (preview) {
                    preview.style.width = `${widthValue}%`;
                    preview.style.maxWidth = '100%';
                    preview.style.height = 'auto';
                }
            };

            const showPreview = (src, name, size) => {
                if (preview) preview.src = src;
                previewWrap?.classList.remove('hidden');
                placeholder?.classList.add('hidden');
                if (fileName) fileName.textContent = name || 'Belum dipilih';
                if (fileSize) fileSize.textContent = size || '-';
            };

            if (fileInput && !fileInput.dataset.previewBound) {
                fileInput.addEventListener('change', () => {
                    const file = fileInput.files?.[0];
                    if (!file) {
                        return;
                    }

                    const objectUrl = URL.createObjectURL(file);
                    showPreview(objectUrl, file.name, formatFileSize(file.size));
                });
                fileInput.dataset.previewBound = '1';
            }

            if (widthRange && !widthRange.dataset.previewBound) {
                widthRange.addEventListener('input', updateDisplayLabel);
                widthRange.dataset.previewBound = '1';
            }

            updateDisplayLabel();
        }

        function initImageBlocks(root = document) {
            root.querySelectorAll('[data-image-item]').forEach((imageBlock) => {
                if (imageBlock.dataset.bound === '1') {
                    return;
                }

                syncImageBlockPreview(imageBlock);

                const removeBtn = imageBlock.querySelector('[data-remove-image-btn]');
                removeBtn?.addEventListener('click', () => {
                    const list = imageBlock.closest('[data-image-list]');
                    imageBlock.remove();

                    if (list && !list.querySelector('[data-image-item]')) {
                        const emptyState = document.createElement('div');
                        emptyState.className =
                            'rounded-lg border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-500';
                        emptyState.setAttribute('data-image-empty-state', '');
                        emptyState.textContent =
                            'Belum ada gambar. Klik "Tambah Gambar" untuk menambahkan gambar pendukung bab.';
                        list.appendChild(emptyState);
                    }
                });

                imageBlock.dataset.bound = '1';
            });

            root.querySelectorAll('[data-add-image-btn]').forEach((button) => {
                if (button.dataset.bound === '1') {
                    return;
                }

                button.addEventListener('click', () => {
                    const section = button.closest('.rounded-xl');
                    const list = section?.querySelector('[data-image-list]');
                    if (!list) {
                        return;
                    }

                    const emptyState = list.querySelector('[data-image-empty-state]');
                    if (emptyState) {
                        emptyState.remove();
                    }

                    const nextIndex = Number(list.dataset.nextImageIndex || '0');
                    const chapterIndex = button.closest('[data-chapter-index]')?.dataset.chapterIndex ||
                        '0';
                    list.insertAdjacentHTML('beforeend', createImageBlockHtml(chapterIndex, nextIndex));
                    list.dataset.nextImageIndex = String(nextIndex + 1);

                    const newBlock = list.lastElementChild;
                    if (newBlock) {
                        syncImageBlockPreview(newBlock);
                        newBlock.dataset.bound = '1';
                        newBlock.querySelector('[data-remove-image-btn]')?.addEventListener('click', () =>
                            newBlock.remove());
                    }
                });

                button.dataset.bound = '1';
            });
        }

        function initDynamicChapterFeatures(root = document) {
            initRichEditors(root);
            initVideoSourceSelectors(root);
            initImageWidthSelectors(root);
            initImageBlocks(root);
        }

        function collapseAllExistingChapters() {
            document.querySelectorAll('[data-chapter-card]').forEach((chapterCard) => {
                chapterCard.open = false;
            });
        }

        initDynamicChapterFeatures(document);

        document.getElementById('add-chapter-btn').addEventListener('click', function() {
            const container = document.getElementById('chapters-container');
            const newChapterContainer = document.getElementById('new-chapter-container');
            const template = document.createElement('div');

            const chapterIndex = chapterCount++;
            template.innerHTML = `
            <details class="rounded-lg border border-slate-300 bg-white" data-chapter-index="${chapterIndex}" data-chapter-card open>
                <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3">
                    <div>
                        <h4 class="font-semibold text-slate-900">Bab Baru</h4>
                        <p class="text-xs text-slate-500">Lihat detail bab</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Detail</span>
                </summary>

                <div class="space-y-4 border-t border-slate-200 p-4">
                    <div class="flex justify-end">
                        <button type="button" class="remove-chapter-btn text-red-600 hover:text-red-700 text-xs font-semibold">
                            Hapus
                        </button>
                    </div>
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
                        <textarea name="chapters[${chapterIndex}][content]" data-rich-editor-target="chapter-content-${chapterIndex}" class="hidden"></textarea>
                        <div data-rich-editor="chapter-content-${chapterIndex}" class="min-h-[180px] rounded-lg border border-slate-300 bg-white"></div>
                        <p class="mt-1 text-xs text-slate-500">Konten pembelajaran hanya untuk teks dan format dasar. Gambar diatur di bagian terpisah di bawah.</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Gambar Pendukung Bab</label>
                                <p class="text-xs text-slate-500">Upload lebih dari satu gambar. Setiap gambar bisa diberi judul, caption, dan ukuran tampil.</p>
                            </div>
                            <button type="button" data-add-image-btn
                                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                                + Tambah Gambar
                            </button>
                        </div>

                        <div class="space-y-4" data-image-list data-next-image-index="0">
                            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-500" data-image-empty-state>
                                Belum ada gambar. Klik "Tambah Gambar" untuk menambahkan gambar pendukung bab.
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Sumber Video</label>
                        <select name="chapters[${chapterIndex}][video_source]" data-video-source
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                            <option value="none" selected>Tidak ada video</option>
                            <option value="link">Gunakan link video</option>
                            <option value="upload">Upload file video</option>
                        </select>

                        <div data-video-link-wrapper class="mt-2 hidden">
                            <input type="url" name="chapters[${chapterIndex}][video_link]" placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://vimeo.com/VIDEO_ID"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                        </div>

                        <div data-video-upload-wrapper class="mt-2 hidden">
                            <input type="file" name="chapters[${chapterIndex}][video_upload]" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-matroska"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                        </div>

                        <input type="hidden" name="chapters[${chapterIndex}][existing_video]" value="">
                        <p class="mt-1 text-xs text-slate-500">Link cocok untuk YouTube/Vimeo. Upload mendukung mp4, webm, ogg, mov, mkv.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tugas Pembelajaran</label>
                        <textarea name="chapters[${chapterIndex}][assignment]" rows="2" placeholder="Tugas atau aktivitas untuk peserta..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Deadline Tugas</label>
                        <input type="date" name="chapters[${chapterIndex}][assignment_deadline]"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                    </div>
                </div>
            </details>
            `;

            collapseAllExistingChapters();
            newChapterContainer.innerHTML = '';
            newChapterContainer.appendChild(template);
            initDynamicChapterFeatures(template);

            template.querySelector('.remove-chapter-btn').addEventListener('click', function(e) {
                e.preventDefault();
                template.remove();
            });

            newChapterContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-chapter-btn')) {
                e.preventDefault();
                e.target.closest('[data-chapter-index]').remove();
            }
        });

        document.querySelector('form')?.addEventListener('submit', () => {
            quillEditors.forEach((_, editorId) => syncEditorToInput(editorId));
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
