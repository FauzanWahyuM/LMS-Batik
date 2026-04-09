<details class="rounded-lg border border-slate-300 bg-white" data-chapter-index="{{ $index }}" data-chapter-card>
    <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3">
        <div>
            <h4 class="font-semibold text-slate-900">{{ $chapter['title'] }}</h4>
            <p class="text-xs text-slate-500">Lihat detail bab</p>
        </div>
        <span class="text-xs font-semibold text-slate-500">Detail</span>
    </summary>

    <div class="space-y-5 border-t border-slate-200 p-4">
        <div class="flex justify-end">
            <button type="button" class="remove-chapter-btn text-xs font-semibold text-red-600 hover:text-red-700">
                Hapus
            </button>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Judul Bab</label>
            <input type="text" name="chapters[{{ $index }}][title]" value="{{ $chapter['title'] }}"
                placeholder="contoh: Bab 1 - Pengenalan Alat"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"
                required>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Deskripsi Bab</label>
            <textarea name="chapters[{{ $index }}][description]" rows="2"
                placeholder="Penjelasan singkat tentang bab ini..."
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">{{ $chapter['description'] ?? '' }}</textarea>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Konten Pembelajaran</label>
            <textarea name="chapters[{{ $index }}][content]" data-rich-editor-target="chapter-content-{{ $index }}"
                class="hidden">{{ old('chapters.' . $index . '.content', $chapter['content'] ?? '') }}</textarea>
            <div data-rich-editor="chapter-content-{{ $index }}"
                class="min-h-[180px] rounded-lg border border-slate-300 bg-white"></div>
            <p class="mt-2 text-xs text-slate-500">Konten pembelajaran hanya untuk teks dan format dasar.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Gambar Pendukung Bab</label>
                    <p class="text-xs text-slate-500">Upload lebih dari satu gambar. Setiap gambar bisa diberi judul,
                        caption, dan ukuran tampil.</p>
                </div>
                <button type="button" data-add-image-btn
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                    + Tambah Gambar
                </button>
            </div>

            @php
                $imageBlocks = old('chapters.' . $index . '.images', $chapter['images'] ?? []);
                if (!is_array($imageBlocks)) {
                    $imageBlocks = [];
                }
                if (empty($imageBlocks) && !empty($chapter['image_path'])) {
                    $imageBlocks = [
                        [
                            'path' => $chapter['image_path'],
                            'title' => $chapter['image_title'] ?? '',
                            'caption' => $chapter['image_caption'] ?? '',
                            'width' => $chapter['image_width'] ?? 75,
                        ],
                    ];
                }
            @endphp

            <div class="space-y-4" data-image-list data-next-image-index="{{ count($imageBlocks) }}">
                @forelse ($imageBlocks as $imageIndex => $image)
                    @include('dashboard.instructor.partials.chapter-image-block', [
                        'chapterIndex' => $index,
                        'imageIndex' => $imageIndex,
                        'image' => $image,
                    ])
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-500"
                        data-image-empty-state>
                        Belum ada gambar. Klik "Tambah Gambar" untuk menambahkan gambar pendukung bab.
                    </div>
                @endforelse
            </div>
        </div>

        <div>
            @php
                $storedVideo = old('chapters.' . $index . '.existing_video', $chapter['video'] ?? null);
                $isVideoUrl = is_string($storedVideo) && filter_var($storedVideo, FILTER_VALIDATE_URL);
                $videoType = old(
                    'chapters.' . $index . '.video_source',
                    $chapter['video_type'] ?? ($storedVideo ? ($isVideoUrl ? 'link' : 'upload') : 'none'),
                );
                $videoLink = old(
                    'chapters.' . $index . '.video_link',
                    $chapter['video_link'] ?? ($isVideoUrl ? $storedVideo : ''),
                );
            @endphp
            <label class="mb-2 block text-sm font-semibold text-slate-700">Sumber Video</label>
            <select name="chapters[{{ $index }}][video_source]" data-video-source
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                <option value="none" {{ $videoType === 'none' ? 'selected' : '' }}>Tidak ada video</option>
                <option value="link" {{ $videoType === 'link' ? 'selected' : '' }}>Gunakan link video</option>
                <option value="upload" {{ $videoType === 'upload' ? 'selected' : '' }}>Upload file video</option>
            </select>

            <div data-video-link-wrapper class="mt-2 {{ $videoType === 'link' ? '' : 'hidden' }}">
                <input type="url" name="chapters[{{ $index }}][video_link]" value="{{ $videoLink }}"
                    placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://vimeo.com/VIDEO_ID"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
            </div>

            <div data-video-upload-wrapper class="mt-2 {{ $videoType === 'upload' ? '' : 'hidden' }}">
                <input type="file" name="chapters[{{ $index }}][video_upload]"
                    accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-matroska"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold">
                @if (!empty($storedVideo) && $videoType === 'upload')
                    <p class="mt-1 text-xs text-slate-500">Video saat ini tersimpan. Upload file baru untuk mengganti.
                    </p>
                @endif
            </div>

            <input type="hidden" name="chapters[{{ $index }}][existing_video]"
                value="{{ $videoType === 'upload' ? $storedVideo ?? '' : '' }}">
            <p class="mt-1 text-xs text-slate-500">Link cocok untuk YouTube/Vimeo. Upload mendukung mp4, webm, ogg, mov,
                mkv.</p>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Tugas Pembelajaran</label>
            <textarea name="chapters[{{ $index }}][assignment]" rows="2"
                placeholder="Tugas atau aktivitas untuk peserta..."
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">{{ $chapter['assignment'] ?? '' }}</textarea>
        </div>

        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Deadline Tugas</label>
            <input type="date" name="chapters[{{ $index }}][assignment_deadline]"
                value="{{ old('chapters.' . $index . '.assignment_deadline', $chapter['assignment_deadline'] ?? '') }}"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
        </div>
    </div>
</details>
