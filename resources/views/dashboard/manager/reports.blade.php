@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Ringkasan Laporan</h2>
                <form method="POST" action="{{ route('dashboard.manager.reports.export') }}" class="flex items-center gap-2">
                    @csrf
                    <select name="report_type"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700">
                        <option value="partisipasi">Partisipasi</option>
                        <option value="kinerja">Kinerja</option>
                        <option value="pengajar">Pengajar</option>
                    </select>
                    <button type="submit"
                        class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Export</button>
                </form>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Total Pendaftaran</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $reports['total_registration'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Completion Rate</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $reports['completion_rate'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Rata-rata Kehadiran</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $reports['avg_attendance'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-500">Utilisasi Pengajar</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $reports['instructor_utilization'] }}</p>
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Partisipasi Bulanan</h2>
            <div class="mt-4 space-y-3">
                @foreach ($monthlyParticipation as $item)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs text-slate-500">
                            <span>{{ $item['month'] }}</span>
                            <span>{{ $item['value'] }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-slate-700"
                                style="width: {{ min((int) $item['value'], 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
@endsection
