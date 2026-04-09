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
                <p class="text-sm font-semibold text-slate-900">{{ $submission->user->name ?? 'Peserta' }}</p>
                <p class="mt-2 text-xs text-slate-600">
                    <span class="font-medium">Modul:</span> {{ $submission->module->title ?? 'N/A' }}
                </p>
            </div>

            <!-- Content -->
            <div class="space-y-6 px-6 py-6">
                <!-- Task Section -->
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Tugas</h3>

                    @if ($submission->original_filename && str_starts_with($submission->mime_type ?? '', 'image/'))
                        <div class="mt-3 overflow-hidden rounded-2xl border border-slate-300 bg-slate-100">
                            <img src="{{ $submission->file_url }}" alt="Preview tugas"
                                class="max-h-72 w-full object-contain bg-slate-100" />
                        </div>
                    @endif

                    @if ($submission->original_filename)
                        <div class="mt-4 rounded-lg border border-slate-200 bg-white p-4">
                            <p class="text-sm font-semibold text-slate-900">File Pengiriman</p>
                            <p class="mt-2 text-sm text-slate-600">
                                <a href="{{ $submission->file_url }}" target="_blank"
                                    class="text-blue-600 underline">{{ $submission->original_filename }}</a>
                            </p>
                            <p class="mt-1 text-xs text-slate-500">Ukuran: {{ $submission->formatted_file_size }}</p>
                            <p class="mt-1 text-xs text-slate-500">Tanggal Pengiriman:
                                {{ optional($submission->submitted_at)->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}
                                WIB</p>
                        </div>
                    @endif
                </div>

                <form method="POST"
                    action="{{ route('dashboard.instructor.assessments.score', ['submission' => $submission->id]) }}"
                    class="space-y-6">
                    @csrf

                    <!-- Score Section -->
                    <div>
                        <label for="score" class="block text-sm font-semibold text-slate-900">
                            Beri Nilai
                        </label>
                        <input type="number" id="score" name="score" min="0" max="100"
                            value="{{ old('score', $submission->score ?? '') }}" placeholder="Masukkan nilai (0-100)"
                            class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        @error('score')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Feedback Section -->
                    <div>
                        <label for="feedback" class="block text-sm font-semibold text-slate-900">
                            Beri Feedback
                        </label>
                        <textarea id="feedback" name="feedback" rows="6" placeholder="Masukkan feedback untuk peserta..."
                            class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 placeholder-slate-400 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 resize-none">{{ old('feedback', $submission->feedback ?? '') }}</textarea>
                        @error('feedback')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Button -->
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="w-full rounded-lg bg-slate-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                            Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </article>
    </div>
@endsection
