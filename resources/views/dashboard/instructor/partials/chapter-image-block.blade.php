@php
    $existingPath = old(
        'chapters.' . $chapterIndex . '.images.' . $imageIndex . '.existing_path',
        $image['path'] ?? ($image['existing_path'] ?? null),
    );
    $imageTitle = old(
        'chapters.' . $chapterIndex . '.images.' . $imageIndex . '.title',
        $image['title'] ?? ($image['image_title'] ?? ''),
    );
    $imageCaption = old(
        'chapters.' . $chapterIndex . '.images.' . $imageIndex . '.caption',
        $image['caption'] ?? ($image['image_caption'] ?? ''),
    );
    $imageWidth = (int) old(
        'chapters.' . $chapterIndex . '.images.' . $imageIndex . '.width',
        $image['width'] ?? ($image['image_width'] ?? 75),
    );
    $previewSrc = !empty($existingPath) ? route('public-file', ['path' => ltrim($existingPath, '/')]) : null;
@endphp

<div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm" data-image-item
    data-image-index="{{ $imageIndex }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-slate-800">Gambar {{ $imageIndex + 1 }}</p>
            <p class="text-xs text-slate-500">Upload, judul, caption, dan ukuran tampil.</p>
        </div>
        <button type="button" data-remove-image-btn
            class="rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
            Hapus
        </button>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-[220px_1fr]">
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
            <div data-image-preview-wrap class="{{ $previewSrc ? '' : 'hidden' }}">
                <img src="{{ $previewSrc ?? '' }}" alt="Pratinjau gambar" class="h-40 w-full object-cover"
                    data-image-preview>
            </div>
            <div data-image-placeholder
                class="{{ $previewSrc ? 'hidden' : '' }} flex h-40 items-center justify-center px-4 text-center text-xs text-slate-500">
                Pratinjau gambar akan muncul di sini.
            </div>
            <div class="border-t border-slate-200 bg-white p-3 text-xs text-slate-500" data-image-preview-meta>
                <p class="font-semibold text-slate-700">
                    Nama file: <span
                        data-image-file-name>{{ !empty($existingPath) ? basename($existingPath) : 'Belum dipilih' }}</span>
                </p>
                <p class="mt-1">Ukuran file: <span
                        data-image-file-size>{{ !empty($existingPath) ? 'Tersimpan' : '-' }}</span></p>
                <p class="mt-1">Approx. display: <span data-image-display-label>{{ $imageWidth }}</span>%</p>
            </div>
        </div>

        <div class="space-y-3">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">File Gambar</label>
                    <input type="file"
                        name="chapters[{{ $chapterIndex }}][images][{{ $imageIndex }}][image_upload]" accept="image/*"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold"
                        data-image-upload>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Lebar Tampil (25%-100%)</label>
                    <input type="range" min="25" max="100" step="5"
                        name="chapters[{{ $chapterIndex }}][images][{{ $imageIndex }}][width]"
                        value="{{ $imageWidth }}" class="w-full" data-image-width-range>
                    <p class="mt-1 text-xs text-slate-500">Approx. display: <span
                            data-image-width-label>{{ $imageWidth }}</span>%</p>
                </div>
            </div>

            <input type="hidden" name="chapters[{{ $chapterIndex }}][images][{{ $imageIndex }}][existing_path]"
                value="{{ $existingPath ?? '' }}" data-existing-image-path>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Judul Gambar</label>
                <input type="text" name="chapters[{{ $chapterIndex }}][images][{{ $imageIndex }}][title]"
                    value="{{ $imageTitle }}" placeholder="Contoh: Pola Dasar Batik"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">Caption Gambar</label>
                <textarea name="chapters[{{ $chapterIndex }}][images][{{ $imageIndex }}][caption]" rows="2"
                    placeholder="Penjelasan singkat tentang gambar..."
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">{{ $imageCaption }}</textarea>
            </div>
        </div>
    </div>
</div>
