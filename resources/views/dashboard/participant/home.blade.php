@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-center text-2xl font-bold text-slate-800 sm:text-left">Progres Pelatihan</h2>
                <p class="mt-1 text-center text-sm text-slate-500 sm:text-left">Halo, {{ $user['name'] ?? 'Peserta' }}.
                    Berikut progres belajar Anda saat ini.</p>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-xs sm:min-w-72">
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-slate-500">Modul</p>
                    <p class="text-lg font-bold text-slate-900">{{ $homeStats['module_total'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-slate-500">Selesai</p>
                    <p class="text-lg font-bold text-slate-900">{{ $homeStats['module_completed'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <p class="text-slate-500">Karya</p>
                    <p class="text-lg font-bold text-slate-900">{{ $homeStats['artwork_total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-5">
            @forelse ($moduleProgressItems ?? [] as $module)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800">{{ $module['title'] }}</h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $module['completed_count'] }} dari
                                {{ $module['total_count'] }} bab selesai</p>
                        </div>
                        <span class="w-fit rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                            {{ $module['status_label'] }}
                        </span>
                    </div>

                    <div class="mt-3 h-2 w-full rounded-full bg-slate-200">
                        <div class="h-2 rounded-full bg-blue-600" style="width: {{ $module['progress'] }}%"></div>
                    </div>
                    <p class="mt-1 text-right text-xs text-slate-500">{{ $module['progress'] }}% selesai</p>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ $module['url'] }}"
                            class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700">
                            Lanjutkan
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    Belum ada modul yang tersedia untuk ditampilkan.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mt-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-center text-2xl font-bold text-slate-800">Akses Cepat</h2>

        <div class="mt-5 grid gap-3 sm:gap-4 sm:grid-cols-2">
            <a href="{{ $continueModuleUrl ?? route('dashboard.participant.modules') }}"
                class="flex min-h-12 sm:min-h-14 items-center justify-center rounded-xl border border-cyan-500/60 bg-cyan-50 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs sm:text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-cyan-100">
                Lanjutkan Modul Terakhir
            </a>
            <a href="{{ route('dashboard.participant.forum') }}"
                class="flex min-h-12 sm:min-h-14 items-center justify-center rounded-xl border border-cyan-500/60 bg-cyan-50 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs sm:text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-cyan-100">
                Forum Diskusi
            </a>
            <a href="{{ route('dashboard.participant.gallery') }}"
                class="flex min-h-12 sm:min-h-14 items-center justify-center rounded-xl border border-cyan-500/60 bg-cyan-50 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs sm:text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-cyan-100">
                Upload Hasil Karya
            </a>
            <a href="{{ route('dashboard.participant.gallery') }}"
                class="flex min-h-12 sm:min-h-14 items-center justify-center rounded-xl border border-cyan-500/60 bg-cyan-50 px-3 sm:px-4 py-2 sm:py-3 text-center text-xs sm:text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-cyan-100">
                Lihat Galeri Karya
            </a>
        </div>
    </section>

    <section class="mt-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-center text-2xl font-bold text-slate-800">Karya Terbaru Saya</h2>

        @if (!empty($latestArtwork))
            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                <div class="grid gap-0 md:grid-cols-[220px_minmax(0,1fr)]">
                    <div class="bg-slate-100">
                        <img src="{{ route('public-file', ['path' => ltrim($latestArtwork->image_path, '/')]) }}"
                            alt="{{ $latestArtwork->title }}" width="800" height="600"
                            class="h-full w-full object-cover" loading="lazy" decoding="async">
                    </div>
                    <div class="p-5">
                        <p class="text-sm font-semibold text-slate-800">{{ $latestArtwork->title }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $latestArtwork->description }}</p>
                        <p class="mt-4 text-xs text-slate-500">Pembuat: {{ $latestArtwork->creator_name }}</p>
                        <p class="mt-1 text-xs text-slate-500">Tanggal Rilis:
                            {{ $latestArtwork->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-6 text-center">
                <p class="text-sm font-semibold text-slate-800">Belum ada karya yang diunggah.</p>
                <p class="mt-1 text-xs text-slate-500">Silakan upload karya pertama Anda melalui menu galeri.</p>
            </div>
        @endif
    </section>
@endsection
