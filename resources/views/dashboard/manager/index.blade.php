@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-slate-600">Total Peserta Aktif</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">187</p>
        </article>

        <article class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-amber-700">Kelas Berjalan</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">11</p>
        </article>

        <article class="rounded-xl border border-rose-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-rose-700">Isu Operasional</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">4</p>
        </article>

        <article class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase text-emerald-700">Kepuasan Program</p>
            <p class="mt-3 text-3xl font-bold text-slate-800">92%</p>
        </article>
    </section>
@endsection
