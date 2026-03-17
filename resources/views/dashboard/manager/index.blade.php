@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('dashboard.manager.participants.individual') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peserta Individu</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['individualParticipants'] }}</p>
        </a>

        <a href="{{ route('dashboard.manager.participants.group') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peserta Grup</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['groupParticipants'] }}</p>
        </a>

        <a href="{{ route('dashboard.manager.instructors') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pengajar Aktif</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['activeInstructors'] }}</p>
        </a>

        <a href="{{ route('dashboard.manager.programs') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Program Aktif</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['activePrograms'] }}</p>
        </a>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Aktivitas Terkini</h2>
                <a href="{{ route('dashboard.manager.reports') }}"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Lihat
                    laporan</a>
            </div>
            <div class="mt-4 space-y-3">
                @foreach ($activities as $activity)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $activity['title'] }}</p>
                            <span class="text-xs text-slate-500">{{ $activity['time'] }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600">{{ $activity['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Aksi Cepat</h2>
            <div class="mt-4 space-y-3">
                <a href="{{ route('dashboard.manager.participants.individual') }}"
                    class="block rounded-xl border border-slate-200 p-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kelola
                    peserta individu</a>
                <a href="{{ route('dashboard.manager.participants.group') }}"
                    class="block rounded-xl border border-slate-200 p-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kelola
                    peserta kelompok</a>
                <a href="{{ route('dashboard.manager.instructors') }}"
                    class="block rounded-xl border border-slate-200 p-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kelola
                    pengajar</a>
                <a href="{{ route('dashboard.manager.settings') }}"
                    class="block rounded-xl border border-slate-200 p-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Pengaturan
                    sistem</a>
            </div>
        </article>
    </section>
@endsection
