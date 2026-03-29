@extends('dashboard.layouts.app')

@section('dashboard-content')
    <div class="space-y-6">
        <!-- Peserta Individu -->
        <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">Peserta Individu</h2>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-600">No</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-600">Nama</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($individualParticipants ?? [] as $index => $participant)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} hover:bg-blue-50 transition">
                                    <td class="px-6 py-4 text-slate-600">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $participant['name'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-8 text-center text-slate-500">Tidak ada peserta
                                        individu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination Controls -->
            <div class="border-t border-slate-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <a href="{{ route('dashboard.instructor.participants.individual.detail') }}"
                        class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                        Lihat detail
                    </a>
                    <div class="flex items-center gap-1">
                        <button
                            class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 disabled:opacity-50"
                            disabled>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </button>
                        <div class="flex gap-0.5">
                            <button
                                class="h-10 w-10 rounded-lg border border-slate-300 bg-blue-50 text-sm font-semibold text-blue-600 transition hover:bg-blue-100">1</button>
                            <button
                                class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">2</button>
                            <button class="h-10 w-10 rounded-lg border border-slate-300 text-sm text-slate-500">...</button>
                            <button
                                class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">5</button>
                        </div>
                        <button
                            class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </article>

        <!-- Peserta Kelompok -->
        <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">Peserta Kelompok</h2>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-600">No</th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-600">Nama
                                </th>
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-600">Kelompok
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($groupParticipants ?? [] as $index => $participant)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} hover:bg-blue-50 transition">
                                    <td class="px-6 py-4 text-slate-600">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $participant['name'] }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $participant['group'] ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-500">Tidak ada peserta
                                        kelompok</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination Controls -->
            <div class="border-t border-slate-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <a href="{{ route('dashboard.instructor.participants.group.detail') }}"
                        class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                        Lihat detail
                    </a>
                    <div class="flex items-center gap-1">
                        <button
                            class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 disabled:opacity-50"
                            disabled>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </button>
                        <div class="flex gap-0.5">
                            <button
                                class="h-10 w-10 rounded-lg border border-slate-300 bg-blue-50 text-sm font-semibold text-blue-600 transition hover:bg-blue-100">1</button>
                            <button
                                class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">2</button>
                            <button class="h-10 w-10 rounded-lg border border-slate-300 text-sm text-slate-500">...</button>
                            <button
                                class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">5</button>
                        </div>
                        <button
                            class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </article>
    </div>
@endsection
