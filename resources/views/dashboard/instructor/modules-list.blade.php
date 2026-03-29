@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <!-- Header with Search and Add Button -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <form method="GET" action="{{ route('dashboard.instructor.modules') }}" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari modul..." value="{{ request('search', '') }}"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-500 focus:border-slate-500 focus:outline-none">
                    <button type="submit"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                        Cari
                    </button>
                </form>
            </div>
            <a href="{{ route('dashboard.instructor.modules.create') }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Modul
            </a>
        </div>

        <!-- Modules Grid -->
        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($modules as $module)
                <article
                    class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-slate-300 hover:shadow-md">
                    <!-- Cover Image -->
                    <div class="relative h-40 w-full overflow-hidden bg-linear-to-br from-slate-100 to-slate-200 sm:h-48">
                        @if ($module['cover'])
                            <img src="{{ $module['cover'] }}" alt="{{ $module['title'] }}"
                                class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-1 text-xs text-slate-500">Gambar sampul</p>
                                </div>
                            </div>
                        @endif
                        <div class="absolute right-2 top-2">
                            <span
                                class="inline-block rounded-full border border-white/50 bg-slate-900/80 px-2.5 py-1 text-[11px] font-bold text-white backdrop-blur">
                                {{ $module['status'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4 sm:p-5">
                        <h3 class="line-clamp-2 text-sm font-bold text-slate-900">{{ $module['title'] }}</h3>
                        <p class="mt-1 text-xs text-slate-500">Durasi: {{ $module['duration'] }}</p>

                        <div class="mt-3 flex flex-wrap gap-2 text-xs text-slate-600">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1">
                                <span class="font-semibold text-slate-800">{{ $module['chapters'] }}</span> bab
                            </span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1">
                                <span class="font-semibold text-slate-800">{{ $module['participants'] }}</span> peserta
                            </span>
                        </div>

                        <p class="mt-3 text-xs text-slate-500">Update: {{ $module['updated_at'] }}</p>

                        <!-- Action Buttons -->
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('dashboard.instructor.modules.detail', ['module' => $module['id']]) }}"
                                class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                                title="Lihat detail modul">
                                Lihat Detail
                            </a>
                            <a href="{{ route('dashboard.instructor.modules.edit', ['module' => $module['id']]) }}"
                                class="flex-1 rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-200"
                                title="Edit modul">
                                Edit
                            </a>
                            <form method="POST"
                                action="{{ route('dashboard.instructor.modules.delete', ['module' => $module['id']]) }}"
                                class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus modul ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100"
                                    title="Hapus modul">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if (count($modules) === 0)
            <div class="mt-8 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6v6m0 0v6m0-6h6m0 0h6M6 12a6 6 0 1112 0 6 6 0 01-12 0z" />
                </svg>
                <p class="mt-3 text-sm font-semibold text-slate-600">Belum ada modul</p>
                <p class="mt-1 text-xs text-slate-500">Mulai dengan membuat modul baru menggunakan tombol di atas.</p>
            </div>
        @endif
    </section>
@endsection
