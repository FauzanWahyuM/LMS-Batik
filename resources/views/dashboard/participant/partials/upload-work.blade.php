<section class="max-w-8xl">
    @php
        $artworkModel = isset($artwork) && !empty($artwork) ? $artwork : null;
        $isEditMode = !empty($artworkModel);
    @endphp
    <!-- Form Card -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="POST"
            action="{{ $isEditMode ? route('dashboard.participant.gallery.update', ['artwork' => $artworkModel->id]) : route('dashboard.participant.gallery.store') }}"
            enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @if ($isEditMode)
                @method('PUT')
            @endif
            <!-- Nama Pembuat -->
            <div>
                <label for="nama-pembuat" class="block text-sm font-semibold text-slate-900 mb-2">
                    Nama Pembuat
                </label>
                <input type="text" id="nama-pembuat" name="nama_pembuat" placeholder="Masukkan nama Anda"
                    value="{{ old('nama_pembuat', $artworkModel?->creator_name ?? ($user['name'] ?? '')) }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    required>
                @error('nama_pembuat')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Judul Karya -->
            <div>
                <label for="judul-karya" class="block text-sm font-semibold text-slate-900 mb-2">
                    Judul Karya
                </label>
                <input type="text" id="judul-karya" name="judul_karya" placeholder="Masukkan judul karya Anda"
                    value="{{ old('judul_karya', $artworkModel?->title ?? '') }}"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    required>
                @error('judul_karya')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-semibold text-slate-900 mb-2">
                    Deskripsi
                </label>
                <textarea id="deskripsi" name="deskripsi"
                    placeholder="Deskripsikan karya Anda, teknik yang digunakan, inspirasi, dll..." rows="6"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none"
                    required>{{ old('deskripsi', $artworkModel?->description ?? '') }}</textarea>
                @error('deskripsi')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gambar/Foto Karya -->
            <div>
                <label for="gambar-karya" class="block text-sm font-semibold text-slate-900 mb-2">
                    {{ $isEditMode ? 'Gambar/Foto Karya Baru' : 'Gambar/Foto Karya' }}
                </label>
                <div class="relative">
                    <input type="file" id="gambar-karya" name="gambar_karya" accept="image/*" class="sr-only"
                        @if (!$isEditMode) required @endif>

                    @if ($isEditMode && !empty($artworkModel?->image_path))
                        <div class="mb-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Gambar Saat Ini
                            </p>
                            <img src="{{ route('public-file', ['path' => ltrim($artworkModel->image_path, '/')]) }}"
                                alt="{{ $artworkModel->title }}" class="max-h-64 w-full rounded-md object-cover">
                            <p class="mt-2 text-xs text-slate-500">Biarkan kosong jika tidak ingin mengganti gambar.</p>
                        </div>
                    @endif

                    <!-- Upload Area (Hidden when file is selected) -->
                    <label id="upload-area" for="gambar-karya"
                        class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-8 cursor-pointer transition hover:border-blue-500 hover:bg-blue-50">
                        <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="mt-3 text-sm font-medium text-slate-700">Pilih gambar atau drag & drop</span>
                        <span class="mt-1 text-xs text-slate-500">PNG, JPG, GIF hingga 10MB</span>
                    </label>

                    <!-- File Preview (Shown when file is selected) -->
                    <div id="file-preview" class="hidden">
                        <div class="rounded-lg border-2 border-slate-200 bg-slate-50 px-6 py-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 flex-1">
                                    <svg class="h-10 w-10 text-blue-600 shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
                                        </path>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p id="file-name-display" class="text-sm font-semibold text-slate-900 truncate">
                                        </p>
                                        <p id="file-size-display" class="text-xs text-slate-600"></p>
                                    </div>
                                </div>
                                <button id="remove-file-btn" type="button"
                                    class="ml-3 shrink-0 rounded-lg bg-red-100 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-200 active:bg-red-300">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @error('gambar_karya')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 active:bg-blue-800">
                    {{ $isEditMode ? 'Perbarui Karya' : 'Simpan Karya' }}
                </button>
                <a href="{{ route('dashboard.participant.gallery') }}"
                    class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
        <div class="flex gap-3">
            <svg class="h-5 w-5 text-blue-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"></path>
            </svg>
            <div class="text-xs text-blue-800">
                <p class="font-semibold mb-1">Tips Upload Karya:</p>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Gunakan gambar berkualitas tinggi</li>
                    <li>Deskripsikan karya Anda dengan detail</li>
                    <li>Isi semua kolom dengan informasi lengkap</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<script>
    const fileInput = document.getElementById('gambar-karya');
    const uploadArea = document.getElementById('upload-area');
    const filePreview = document.getElementById('file-preview');
    const fileNameDisplay = document.getElementById('file-name-display');
    const fileSizeDisplay = document.getElementById('file-size-display');
    const removeFileBtn = document.getElementById('remove-file-btn');

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    }

    // Show file preview
    function showFilePreview() {
        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            fileNameDisplay.textContent = file.name;
            fileSizeDisplay.textContent = formatFileSize(file.size);
            uploadArea.classList.add('hidden');
            filePreview.classList.remove('hidden');
        }
    }

    // Remove file
    function removeFile() {
        fileInput.value = '';
        uploadArea.classList.remove('hidden');
        filePreview.classList.add('hidden');
        fileNameDisplay.textContent = '';
        fileSizeDisplay.textContent = '';
    }

    // File input change event
    fileInput.addEventListener('change', showFilePreview);

    // Remove file button click
    removeFileBtn.addEventListener('click', function(e) {
        e.preventDefault();
        removeFile();
    });

    // Drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        uploadArea.classList.add('border-blue-500', 'bg-blue-50');
    }

    function unhighlight(e) {
        uploadArea.classList.remove('border-blue-500', 'bg-blue-50');
    }

    uploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        // Only allow one file
        const dt = e.dataTransfer;
        const files = Array.from(dt.files);

        // Filter only image files
        const imageFiles = files.filter(file => file.type.startsWith('image/'));

        if (imageFiles.length > 0) {
            // Create a new DataTransfer and add only the first image file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(imageFiles[0]);
            fileInput.files = dataTransfer.files;

            // Trigger change event
            const event = new Event('change', {
                bubbles: true
            });
            fileInput.dispatchEvent(event);
        }
    }
</script>
