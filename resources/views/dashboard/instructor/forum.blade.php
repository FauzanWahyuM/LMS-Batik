@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="space-y-6">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="flex-1">
                    <input type="text" placeholder="Input komentar/pertanyaan"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <button
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Modul</button>
                <button
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Kirim</button>
            </div>
        </div>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-2xl font-bold text-slate-800 text-center">Modul 1 - Teknik Canting Dasar</h2>

            <div class="mt-5 space-y-4">
                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-800">Adi - Peserta</p>
                    <p class="mt-2 text-sm text-slate-600">Komentar 1</p>
                    <button class="mt-2 text-xs font-semibold text-blue-700 hover:underline">Balas komentar ini</button>
                </div>

                <div class="flex flex-col gap-2 md:flex-row">
                    <input type="text" placeholder="Balas komentar"
                        class="flex-1 rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <button
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Kirim</button>
                </div>

                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-800">Susanti - Pengajar</p>
                    <p class="mt-2 text-sm text-slate-600">Komentar 2</p>
                    <button class="mt-2 text-xs font-semibold text-blue-700 hover:underline">Balas komentar ini</button>
                </div>
            </div>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-2xl font-bold text-slate-800 text-center">Modul 2 - Teknik Pewarnaan Dasar</h2>

            <div class="mt-5 space-y-4">
                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-800">Adi - Peserta</p>
                    <p class="mt-2 text-sm text-slate-600">Komentar 1</p>
                    <button class="mt-2 text-xs font-semibold text-blue-700 hover:underline">Balas komentar ini</button>
                </div>

                <div class="flex flex-col gap-2 md:flex-row">
                    <input type="text" placeholder="Balas komentar"
                        class="flex-1 rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <button
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Kirim</button>
                </div>

                <div class="rounded-lg border border-slate-200 p-4">
                    <p class="text-sm font-semibold text-slate-800">Susanti - Pengajar</p>
                    <p class="mt-2 text-sm text-slate-600">Komentar 2</p>
                    <button class="mt-2 text-xs font-semibold text-blue-700 hover:underline">Balas komentar ini</button>
                </div>
            </div>
        </article>
    </section>
@endsection
