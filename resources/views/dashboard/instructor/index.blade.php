@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="grid gap-4 md:grid-cols-3">
        <a href="{{ route('dashboard.instructor.participants') }}"
            class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Peserta Aktif</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-bold text-slate-900">{{ $summary['activeParticipants'] }}</p>
                <span
                    class="rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600 transition group-hover:border-slate-300">Lihat
                    Detail</span>
            </div>
        </a>

        <a href="{{ route('dashboard.instructor.assessments') }}"
            class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Karya Menunggu Penilaian</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-bold text-slate-900">{{ $summary['pendingReviews'] }}</p>
                <span
                    class="rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600 transition group-hover:border-slate-300">Nilai
                    Sekarang</span>
            </div>
        </a>

        <a href="{{ route('dashboard.instructor.modules') }}"
            class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Modul Tersedia</p>
            <div class="mt-3 flex items-end justify-between gap-3">
                <p class="text-3xl font-bold text-slate-900">{{ $summary['totalModules'] }}</p>
                <span
                    class="rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600 transition group-hover:border-slate-300">Kelola
                    Modul</span>
            </div>
        </a>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Peserta Aktif</h2>
            <a href="{{ route('dashboard.instructor.participants') }}"
                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">Lihat
                Semua Peserta</a>
        </div>

        <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs uppercase tracking-[0.12em] text-slate-500">
                        <th class="px-4 py-3">Nama Peserta</th>
                        <th class="px-4 py-3">Program</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($activeParticipants as $participant)
                        <tr class="text-slate-700 transition hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $participant['name'] }}</td>
                            <td class="px-4 py-3">{{ $participant['program_type'] }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">{{ $participant['status'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dashboard.instructor.participants', ['participant' => $participant['id']]) }}"
                                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-100">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Modul yang Tersedia</h2>
                <a href="{{ route('dashboard.instructor.modules') }}"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">Buka
                    Kelola Modul</a>
            </div>

            <div class="mt-4 space-y-3">
                @foreach ($availableModules as $module)
                    <a href="{{ route('dashboard.instructor.modules') }}"
                        class="block rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-slate-300 hover:bg-white">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $module['title'] }}</h3>
                                <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $module['summary'] }}</p>
                            </div>
                            <span
                                class="shrink-0 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-700">{{ $module['status'] }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Karya Menunggu Penilaian</h2>
                <a href="{{ route('dashboard.instructor.assessments') }}"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">Buka
                    Halaman Penilaian</a>
            </div>

            <div class="mt-4 space-y-3">
                @foreach ($pendingWorks as $work)
                    <a href="{{ route('dashboard.instructor.assessments') }}"
                        class="block rounded-xl border border-slate-200 bg-slate-50 p-4 transition hover:border-slate-300 hover:bg-white">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $work['participant'] }}</p>
                                <p class="mt-1 text-xs text-slate-600">Modul: {{ $work['module'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">Dikumpulkan: {{ $work['submitted_at'] }}</p>
                            </div>
                            <span
                                class="inline-flex w-fit rounded-full border border-amber-300 bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700">{{ $work['status'] }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </article>
    </section>
@endsection
