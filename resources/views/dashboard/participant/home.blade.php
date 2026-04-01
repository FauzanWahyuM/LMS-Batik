@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-center text-2xl font-bold text-slate-800">Progres Pelatihan</h2>

        <div class="mt-6 space-y-7">
            <article>
                <h3 class="text-lg font-semibold text-slate-800">Judul Materi 1</h3>
                <div class="mt-3 h-2 w-full rounded-full bg-slate-200">
                    <div class="h-2 w-[79%] rounded-full bg-blue-600"></div>
                </div>
                <p class="mt-1 text-right text-xs text-slate-500">79% completed</p>
            </article>

            <article>
                <h3 class="text-lg font-semibold text-slate-800">Judul Materi 2</h3>
                <div class="mt-3 h-2 w-full rounded-full bg-slate-200">
                    <div class="h-2 w-[35%] rounded-full bg-blue-600"></div>
                </div>
                <p class="mt-1 text-right text-xs text-slate-500">35% completed</p>
            </article>
        </div>
    </section>

    <section class="mt-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-center text-2xl font-bold text-slate-800">Akses Cepat</h2>

        <div class="mt-5 grid gap-3 sm:gap-4 sm:grid-cols-2">
            <a href="{{ route('dashboard.participant.modules') }}"
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

        <div class="mt-6 flex justify-center">
            <div class="h-52 w-36 rounded-md border-2 border-slate-800 bg-slate-50"></div>
        </div>

        <p class="mt-4 text-center text-sm text-slate-600">Tanggal Rilis: 24/10/2025</p>
    </section>
@endsection
