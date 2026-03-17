@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="grid gap-6 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="text-lg font-bold text-slate-900">Daftar Peserta</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-wider text-slate-500">
                            <th class="px-3 py-3">Nama</th>
                            <th class="px-3 py-3">Batch</th>
                            <th class="px-3 py-3">Progres</th>
                            <th class="px-3 py-3">Aktivitas</th>
                            <th class="px-3 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($participants as $participant)
                            <tr class="text-slate-700">
                                <td class="px-3 py-3 font-semibold text-slate-900">{{ $participant['name'] }}</td>
                                <td class="px-3 py-3">{{ $participant['batch'] }}</td>
                                <td class="px-3 py-3">{{ $participant['progress'] }}%</td>
                                <td class="px-3 py-3">{{ $participant['last_activity'] }}</td>
                                <td class="px-3 py-3">
                                    <a href="{{ route('dashboard.instructor.participants', ['participant' => $participant['id']]) }}"
                                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Detail Peserta</h2>
            @php
                $detail = collect($participants)->firstWhere('id', $selectedParticipant) ?? $participants[0];
            @endphp
            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-sm font-bold text-slate-900">{{ $detail['name'] }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $detail['batch'] }}</p>
                <p class="mt-4 text-sm text-slate-600">Progres pembelajaran</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $detail['progress'] }}%</p>
                <p class="mt-2 text-xs text-slate-500">Aktivitas terakhir: {{ $detail['last_activity'] }}</p>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-sky-600" style="width: {{ $detail['progress'] }}%"></div>
                </div>
                <a href="{{ route('dashboard.instructor.assessments') }}"
                    class="mt-5 inline-block rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Lihat
                    tugas peserta</a>
            </div>
        </article>
    </section>
@endsection
