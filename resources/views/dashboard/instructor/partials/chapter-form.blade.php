<div class="rounded-lg border border-slate-300 bg-white p-4" data-chapter-index="{{ $index }}">
    <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-200">
        <h4 class="font-semibold text-slate-900">{{ $chapter['title'] }}</h4>
        <button type="button" class="remove-chapter-btn text-red-600 hover:text-red-700 text-xs font-semibold">
            Hapus
        </button>
    </div>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Judul Bab</label>
            <input type="text" name="chapters[{{ $index }}][title]" value="{{ $chapter['title'] }}"
                placeholder="contoh: Bab 1 - Pengenalan Alat"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"
                required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Bab</label>
            <textarea name="chapters[{{ $index }}][description]" rows="2"
                placeholder="Penjelasan singkat tentang bab ini..."
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">{{ $chapter['description'] ?? '' }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Konten Pembelajaran</label>
            <textarea name="chapters[{{ $index }}][content]" rows="3" placeholder="Tuliskan konten pembelajaran..."
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">{{ $chapter['content'] ?? '' }}</textarea>
            <p class="mt-1 text-xs text-slate-500">Anda dapat menggunakan format text biasa atau HTML</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Link Video (YouTube, Vimeo, dll - embed
                link)</label>
            <input type="url" name="chapters[{{ $index }}][video]" value="{{ $chapter['video'] ?? '' }}"
                placeholder="https://www.youtube.com/embed/..."
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
            <p class="mt-1 text-xs text-slate-500">Gunakan embed URL, bukan URL video biasa</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Tugas Pembelajaran</label>
            <textarea name="chapters[{{ $index }}][assignment]" rows="2"
                placeholder="Tugas atau aktivitas untuk peserta..."
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">{{ $chapter['assignment'] ?? '' }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Deadline Tugas</label>
            <input type="text" name="chapters[{{ $index }}][assignment_deadline]"
                value="{{ $chapter['assignment_deadline'] ?? '' }}" placeholder="contoh: 31 Desember 2026"
                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
        </div>
    </div>
</div>
