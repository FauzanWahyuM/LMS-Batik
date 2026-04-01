@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="space-y-6">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-2xl font-bold text-slate-800 text-center">Modul 1 - Teknik Canting Dasar</h2>
            <p class="mt-5 text-base font-semibold text-slate-700">Durasi: 72 Jam</p>
            <div class="mt-3 h-2 w-full rounded-full bg-slate-200">
                <div class="h-2 w-[79%] rounded-full bg-blue-600"></div>
            </div>
            <p class="mt-1 text-right text-xs text-slate-500">79% selesai</p>

            <div class="mt-6 flex flex-col sm:justify-end">
                <a href="{{ route('dashboard.participant.modules.detail', ['module' => 'modul-1', 'tab' => 'materi', 'material' => 'bab-1-persiapan-alat-bahan']) }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center rounded-full bg-black px-6 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800 transition">Lihat
                    Modul</a>
            </div>

            <details class="group mt-6 border-t border-slate-200 pt-4" open>
                <summary
                    class="flex cursor-pointer list-none items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-slate-700">Ringkasan Materi</h3>
                    <span class="text-xs font-semibold text-slate-500 transition group-open:rotate-180">▼</span>
                </summary>

                <div class="mt-3 space-y-3">
                    <a href="{{ route('dashboard.participant.modules.detail', ['module' => 'modul-1', 'tab' => 'materi', 'material' => 'bab-1-persiapan-alat-bahan']) }}"
                        class="block rounded-xl border border-slate-200 p-3 transition hover:border-slate-300 hover:bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800">Bab 1 - Persiapan Alat dan Bahan Batik</p>
                        <div class="mt-2 flex items-start gap-3">
                            <div
                                class="h-14 w-20 shrink-0 rounded-md border border-slate-300 bg-slate-100 grid place-items-center text-[10px] text-slate-500">
                                Thumbnail
                            </div>
                            <p class="text-xs leading-relaxed text-slate-600">Pengenalan alat utama batik, fungsi, dan cara
                                menyiapkan area kerja yang aman.</p>
                        </div>
                    </a>

                    <a href="{{ route('dashboard.participant.modules.detail', ['module' => 'modul-1', 'tab' => 'materi', 'material' => 'bab-2-pembuatan-pola']) }}"
                        class="block rounded-xl border border-slate-200 p-3 transition hover:border-slate-300 hover:bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800">Bab 2 - Pembuatan Pola Batik</p>
                        <div class="mt-2 flex items-start gap-3">
                            <div
                                class="h-14 w-20 shrink-0 rounded-md border border-slate-300 bg-slate-100 grid place-items-center text-[10px] text-slate-500">
                                Thumbnail
                            </div>
                            <p class="text-xs leading-relaxed text-slate-600">Dasar membuat pola batik yang proporsional
                                sebelum proses pencantingan.</p>
                        </div>
                    </a>
                </div>
            </details>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-2xl font-bold text-slate-800 text-center">Modul 2 - Teknik Warna Dasar</h2>
            <p class="mt-5 text-base font-semibold text-slate-700">Durasi: 120 Jam</p>
            <div class="mt-3 h-2 w-full rounded-full bg-slate-200">
                <div class="h-2 w-[79%] rounded-full bg-blue-600"></div>
            </div>
            <p class="mt-1 text-right text-xs text-slate-500">79% selesai</p>

            <div class="mt-6 flex flex-col sm:justify-end">
                <a href="{{ route('dashboard.participant.modules.detail', ['module' => 'modul-2', 'tab' => 'materi', 'material' => 'bab-1-pengenalan-warna']) }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center rounded-full bg-black px-6 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800 transition">Lihat
                    Modul</a>
            </div>

            <details class="group mt-6 border-t border-slate-200 pt-4" open>
                <summary
                    class="flex cursor-pointer list-none items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-slate-700">Ringkasan Materi</h3>
                    <span class="text-xs font-semibold text-slate-500 transition group-open:rotate-180">▼</span>
                </summary>

                <div class="mt-3 space-y-3">
                    <a href="{{ route('dashboard.participant.modules.detail', ['module' => 'modul-2', 'tab' => 'materi', 'material' => 'bab-1-pengenalan-warna']) }}"
                        class="block rounded-xl border border-slate-200 p-3 transition hover:border-slate-300 hover:bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800">Bab 1 - Pengenalan Warna Dasar</p>
                        <div class="mt-2 flex items-start gap-3">
                            <div
                                class="h-14 w-20 shrink-0 rounded-md border border-slate-300 bg-slate-100 grid place-items-center text-[10px] text-slate-500">
                                Thumbnail
                            </div>
                            <p class="text-xs leading-relaxed text-slate-600">Memahami teori warna primer, sekunder, dan
                                pencampuran dasar pada kain batik.</p>
                        </div>
                    </a>

                    <a href="{{ route('dashboard.participant.modules.detail', ['module' => 'modul-2', 'tab' => 'materi', 'material' => 'bab-2-teknik-fiksasi']) }}"
                        class="block rounded-xl border border-slate-200 p-3 transition hover:border-slate-300 hover:bg-slate-50">
                        <p class="text-sm font-semibold text-slate-800">Bab 2 - Teknik Fiksasi Warna</p>
                        <div class="mt-2 flex items-start gap-3">
                            <div
                                class="h-14 w-20 shrink-0 rounded-md border border-slate-300 bg-slate-100 grid place-items-center text-[10px] text-slate-500">
                                Thumbnail
                            </div>
                            <p class="text-xs leading-relaxed text-slate-600">Teknik mengunci warna agar hasil pewarnaan
                                lebih tahan lama dan konsisten.</p>
                        </div>
                    </a>
                </div>
            </details>
        </article>
    </section>
@endsection
