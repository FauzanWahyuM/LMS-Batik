@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('dashboard.instructor.modules') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Modul</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['totalModules'] }}</p>
        </a>
        <a href="{{ route('dashboard.instructor.participants') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peserta Aktif</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['activeParticipants'] }}</p>
        </a>
        <a href="{{ route('dashboard.instructor.assessments') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Menunggu Penilaian</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['pendingReviews'] }}</p>
        </a>
        <a href="{{ route('dashboard.instructor.forum') }}"
            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Thread Diskusi</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stats['discussionThreads'] }}</p>
        </a>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Aktivitas Terbaru</h2>
                <a href="{{ route('dashboard.instructor.forum') }}"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Lihat
                    forum</a>
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
                <a href="{{ route('dashboard.instructor.modules') }}"
                    class="block rounded-xl border border-slate-200 p-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">Kelola
                    modul pembelajaran</a>
                <a href="{{ route('dashboard.instructor.participants') }}"
                    class="block rounded-xl border border-slate-200 p-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">Lihat
                    progres peserta</a>
                <a href="{{ route('dashboard.instructor.assessments') }}"
                    class="block rounded-xl border border-slate-200 p-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">Review
                    tugas masuk</a>
                <a href="{{ route('dashboard.instructor.forum') }}"
                    class="block rounded-xl border border-slate-200 p-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">Balas
                    diskusi peserta</a>
            </div>
        </article>
    </section>
@endsection
