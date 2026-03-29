@extends('dashboard.layouts.app')

@section('dashboard-content')
    <div class="max-w-4xl mx-auto">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-6">
                <h2 class="text-lg font-bold text-slate-900">Tambah Modul Baru</h2>
                <a href="{{ route('dashboard.instructor.modules') }}"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">
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
                    <input type="text" id="duration" name="duration" required placeholder="contoh: 72 Jam"
                        value="{{ old('duration') }}"
                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-500 focus:outline-none">
                    @error('duration')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cover Image -->
                <div>
                    <label for="cover" class="block text-sm font-semibold text-slate-700">Gambar Sampul Modul</label>
                    <div class="mt-2 flex items-center justify-center">
                        <label
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
                            <input type="file" id="cover" name="cover" accept="image/*" class="hidden">
                        </label>
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
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Buat Modul
                    </button>
                    <a href="{{ route('dashboard.instructor.modules') }}"
                        class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        Batal
                    </a>
                </div>
            </form>

            <p class="mt-6 text-xs text-slate-500 border-t border-slate-200 pt-6">
                💡 Setelah membuat modul, Anda dapat menambahkan bab dan materi pembelajaran melalui halaman edit modul.
            </p>
        </section>
    </div>
@endsection
