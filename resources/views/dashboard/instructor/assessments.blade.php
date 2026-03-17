@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Review Tugas Peserta</h2>
            <a href="{{ route('dashboard.instructor.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali
                ke dashboard</a>
        </div>

        <div class="mt-4 space-y-3">
            @foreach ($submissions as $submission)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $submission['participant'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $submission['module'] }} ·
                                {{ $submission['submitted_at'] }}</p>
                            <span
                                class="mt-2 inline-block rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                {{ $submission['status'] }}
                            </span>
                        </div>

                        <form method="POST"
                            action="{{ route('dashboard.instructor.assessments.score', ['submission' => $submission['id']]) }}"
                            class="flex w-full flex-col items-stretch gap-2 sm:w-auto sm:flex-row sm:items-center">
                            @csrf
                            <input type="number" name="score" min="0" max="100" required
                                value="{{ $submission['score'] ?? '' }}"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none sm:w-28"
                                placeholder="0-100">
                            <button type="submit"
                                class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan
                                Nilai</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
