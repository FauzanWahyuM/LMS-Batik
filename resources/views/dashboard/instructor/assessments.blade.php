@extends('dashboard.layouts.app')

@section('dashboard-content')
    <div class="space-y-6">
        <!-- Tasks Section -->
        <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <!-- Section Header -->
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-lg font-bold text-slate-900">Tugas Menunggu Penilaian</h2>
            </div>

            <!-- Tasks List -->
            <div class="divide-y divide-slate-200">
                @forelse ($submissions as $submission)
                    <div
                        class="flex flex-col gap-3 border-b border-slate-200 p-6 lg:flex-row lg:items-center lg:justify-between lg:gap-4 last:border-b-0 hover:bg-slate-50 transition">
                        <!-- Task Info -->
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-900">{{ $submission['participant'] }}</p>
                            <p class="mt-1.5 text-xs text-slate-600">
                                <span class="font-medium">Modul:</span> {{ $submission['module'] }}
                            </p>
                            <p class="mt-0.5 text-xs text-slate-600">
                                <span class="font-medium">Waktu:</span> {{ $submission['submitted_at'] }}
                            </p>
                            <div class="mt-3 flex items-center gap-2">
                                @if ($submission['score'] ?? false)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span
                                        class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                        Menunggu
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action -->
                        <div class="flex w-full flex-col gap-2 lg:w-auto">
                            @if ($submission['score'] ?? false)
                                <a href="{{ route('dashboard.instructor.assessments.detail', ['submission' => $submission['id']]) }}"
                                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                    Edit
                                </a>
                            @else
                                <a href="{{ route('dashboard.instructor.assessments.detail', ['submission' => $submission['id']]) }}"
                                    class="rounded-lg bg-slate-700 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-slate-800">
                                    Nilai
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
                        <svg class="h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
                        <p class="mt-4 font-semibold text-slate-900">Tidak ada tugas menunggu penilaian</p>
                        <p class="mt-1 text-sm text-slate-600">Semua tugas telah dinilai atau belum ada pengumpulan</p>
                    </div>
                @endforelse
            </div>
        </article>
    </div>
@endsection
