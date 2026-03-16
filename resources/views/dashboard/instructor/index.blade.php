@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-emerald-700">Kelas Hari Ini</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">2</p>
        </article>

        <article class="rounded-xl border border-cyan-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-cyan-700">Peserta Hadir</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">42</p>
        </article>

        <article class="rounded-xl border border-yellow-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-yellow-700">Perlu Review</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">8</p>
        </article>

        <article class="rounded-xl border border-violet-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-violet-700">Materi Terunggah</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">14</p>
        </article>
    </section>
@endsection
