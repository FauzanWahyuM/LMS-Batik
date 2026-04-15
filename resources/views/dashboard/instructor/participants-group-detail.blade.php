@extends('dashboard.layouts.app')

@section('dashboard-content')
    <div class="space-y-6">
        <!-- Participants List -->
        <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <!-- Header -->
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Peserta Kelompok</h1>
                        <p class="mt-1 text-sm text-slate-600">Daftar lengkap semua peserta kelompok yang terdaftar</p>
                    </div>
                    <a href="{{ route('dashboard.instructor.participants') }}"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Kembali
                    </a>
                </div>
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
                                    <td class="px-6 py-4 text-slate-600">{{ $participant['group'] ?? 'Tidak tersedia' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 4.354a4 4 0 110 8.646 4 4 0 0 0 0-8.646zM9 9H3m18 0h-6M9 20c-3.738 0-7-1.343-7-3 0-1.667 3.134-3 7-3s7 1.333 7 3c0 1.657-3.262 3-7 3m0 0c3.866 0 7-1.343 7-3M9 20c3.738 0 7-1.343 7-3">
                                            </path>
                                        </svg>
                                        <p class="mt-4 font-semibold">Tidak ada peserta kelompok</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="border-t border-slate-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-600">Menampilkan {{ count($groupParticipants ?? []) }} dari
                        {{ count($groupParticipants ?? []) }} peserta</p>
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
                        </div>
                        <button
                            class="flex items-center justify-center rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 disabled:opacity-50"
                            disabled>
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
