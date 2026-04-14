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
                                    <td class="px-6 py-4 text-slate-600">{{ (($individualPage ?? 1) - 1) * 5 + $index + 1 }}
                                    </td>
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
                    @if (($individualTotal ?? 0) > 5)
                        <div class="flex items-center gap-1">
                            <a href="{{ route('dashboard.instructor.participants', ['individual_page' => ($individualPage ?? 1) - 1]) }}"
                                class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 {{ ($individualPage ?? 1) <= 1 ? 'opacity-50 pointer-events-none' : '' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                            <div class="flex gap-0.5">
                                @php
                                    $currentIndividualPage = $individualPage ?? 1;
                                    $totalIndividualPages = $individualTotalPages ?? 1;
                                    $startPage = max(1, $currentIndividualPage - 2);
                                    $endPage = min($totalIndividualPages, $startPage + 4);
                                @endphp
                                @if ($startPage > 1)
                                    <a href="{{ route('dashboard.instructor.participants', ['individual_page' => 1]) }}"
                                        class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 flex items-center justify-center">1</a>
                                    @if ($startPage > 2)
                                        <button
                                            class="h-10 w-10 rounded-lg border border-slate-300 text-sm text-slate-500">...</button>
                                    @endif
                                @endif
                                @for ($i = $startPage; $i <= $endPage; $i++)
                                    <a href="{{ route('dashboard.instructor.participants', ['individual_page' => $i]) }}"
                                        class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold transition flex items-center justify-center {{ $i === $currentIndividualPage ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">{{ $i }}</a>
                                @endfor
                                @if ($endPage < $totalIndividualPages)
                                    @if ($endPage < $totalIndividualPages - 1)
                                        <button
                                            class="h-10 w-10 rounded-lg border border-slate-300 text-sm text-slate-500">...</button>
                                    @endif
                                    <a href="{{ route('dashboard.instructor.participants', ['individual_page' => $totalIndividualPages]) }}"
                                        class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 flex items-center justify-center">{{ $totalIndividualPages }}</a>
                                @endif
                            </div>
                            <a href="{{ route('dashboard.instructor.participants', ['individual_page' => ($individualPage ?? 1) + 1]) }}"
                                class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 {{ ($individualPage ?? 1) >= ($individualTotalPages ?? 1) ? 'opacity-50 pointer-events-none' : '' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </a>
                        </div>
                    @endif
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
                                <tr
                                    class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} hover:bg-blue-50 transition">
                                    <td class="px-6 py-4 text-slate-600">{{ (($groupPage ?? 1) - 1) * 5 + $index + 1 }}
                                    </td>
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
                    @if (($groupTotal ?? 0) > 5)
                        <div class="flex items-center gap-1">
                            <a href="{{ route('dashboard.instructor.participants', ['group_page' => ($groupPage ?? 1) - 1]) }}"
                                class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 {{ ($groupPage ?? 1) <= 1 ? 'opacity-50 pointer-events-none' : '' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                            <div class="flex gap-0.5">
                                @php
                                    $currentGroupPage = $groupPage ?? 1;
                                    $totalGroupPages = $groupTotalPages ?? 1;
                                    $startPage = max(1, $currentGroupPage - 2);
                                    $endPage = min($totalGroupPages, $startPage + 4);
                                @endphp
                                @if ($startPage > 1)
                                    <a href="{{ route('dashboard.instructor.participants', ['group_page' => 1]) }}"
                                        class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 flex items-center justify-center">1</a>
                                    @if ($startPage > 2)
                                        <button
                                            class="h-10 w-10 rounded-lg border border-slate-300 text-sm text-slate-500">...</button>
                                    @endif
                                @endif
                                @for ($i = $startPage; $i <= $endPage; $i++)
                                    <a href="{{ route('dashboard.instructor.participants', ['group_page' => $i]) }}"
                                        class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold transition flex items-center justify-center {{ $i === $currentGroupPage ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-100' }}">{{ $i }}</a>
                                @endfor
                                @if ($endPage < $totalGroupPages)
                                    @if ($endPage < $totalGroupPages - 1)
                                        <button
                                            class="h-10 w-10 rounded-lg border border-slate-300 text-sm text-slate-500">...</button>
                                    @endif
                                    <a href="{{ route('dashboard.instructor.participants', ['group_page' => $totalGroupPages]) }}"
                                        class="h-10 w-10 rounded-lg border border-slate-300 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 flex items-center justify-center">{{ $totalGroupPages }}</a>
                                @endif
                            </div>
                            <a href="{{ route('dashboard.instructor.participants', ['group_page' => ($groupPage ?? 1) + 1]) }}"
                                class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 {{ ($groupPage ?? 1) >= ($groupTotalPages ?? 1) ? 'opacity-50 pointer-events-none' : '' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </article>
    </div>
@endsection
