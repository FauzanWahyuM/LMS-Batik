@extends('dashboard.layouts.app')

@section('dashboard-content')
    <div class="space-y-6">
        <!-- Assessment Detail Card -->
        <article class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <!-- Main Header -->
            <div class="border-b border-slate-200 px-6 py-5 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Penilaian Tugas</h1>
                </div>
                <a href="{{ route('dashboard.instructor.assessments') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Kembali
                </a>
            </div>

            <!-- Task Header Info -->
            <div class="border-b border-slate-200 px-6 py-5">
                <p class="text-sm font-semibold text-slate-900">{{ $submission['participant'] ?? 'Peserta' }}</p>
                <p class="mt-2 text-xs text-slate-600">
                    <span class="font-medium">Modul:</span> {{ $submission['module'] ?? 'N/A' }}
                </p>
            </div>

            <!-- Content -->
            <div class="space-y-6 px-6 py-6">
                <!-- Task Section -->
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Tugas</h3>
                    <div class="mt-3 border-2 border-slate-300 bg-slate-100 p-8">
                        <div class="flex h-40 items-center justify-center text-center">
                            <p class="text-base font-semibold text-slate-500">Gambar</p>
                        </div>
                    </div>
                </div>

                <!-- Score Section -->
                <div>
                    <label for="score" class="block text-sm font-semibold text-slate-900">
                        Beri Nilai
                    </label>
                    <input type="number" id="score" name="score" min="0" max="100"
                        value="{{ $submission['score'] ?? '' }}" placeholder="Masukkan nilai (0-100)"
                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>

                <!-- Feedback Section -->
                <div>
                    <label for="feedback" class="block text-sm font-semibold text-slate-900">
                        Beri Feedback
                    </label>
                    <textarea id="feedback" name="feedback" rows="6" placeholder="Masukkan feedback untuk peserta..."
                        class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                </div>

                <!-- Action Button -->
                <div class="flex justify-end pt-4">
                    <form method="POST"
                        action="{{ route('dashboard.instructor.assessments.score', ['submission' => $submission['id'] ?? '']) }}"
                        class="w-full sm:w-auto">
                        @csrf
                        <input type="hidden" name="score" id="score-hidden">
                        <input type="hidden" name="feedback" id="feedback-hidden">
                        <button type="submit"
                            class="w-full rounded-lg bg-slate-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                            Nilai Tugas
                        </button>
                    </form>
                </div>
            </div>
        </article>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            document.getElementById('score-hidden').value = document.getElementById('score').value;
            document.getElementById('feedback-hidden').value = document.getElementById('feedback').value;
        });
    </script>
@endsection
