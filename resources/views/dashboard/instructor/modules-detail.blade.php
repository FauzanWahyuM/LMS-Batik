@extends('dashboard.layouts.app')

@section('dashboard-content')
    <div class="max-w-4xl mx-auto">
        <!-- Module Header -->
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-slate-900">{{ $module['title'] }}</h1>
                        <span
                            class="inline-block rounded-full border border-slate-300 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $module['status'] }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $module['description'] }}</p>
                    <div class="mt-4 flex flex-wrap gap-4 text-sm text-slate-600">
                        <p><span class="font-semibold text-slate-900">Durasi:</span> {{ $module['duration'] }}</p>
                        <p><span class="font-semibold text-slate-900">Jumlah Bab:</span> {{ count($module['chapters']) }}
                        </p>
                        <p><span class="font-semibold text-slate-900">Peserta:</span> {{ $module['participants'] }}</p>
                        <p><span class="font-semibold text-slate-900">Update:</span> {{ $module['updated_at'] }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
                    <a href="{{ route('dashboard.instructor.modules.edit', ['module' => $module['id']]) }}"
                        class="w-full sm:w-auto rounded-lg bg-slate-900 px-4 py-2 sm:py-1.5 text-xs sm:text-sm font-semibold text-white text-center transition hover:bg-slate-800">
                        Edit Modul
                    </a>
                    <a href="{{ route('dashboard.instructor.modules') }}"
                        class="w-full sm:w-auto rounded-lg border border-slate-300 px-4 py-2 sm:py-1.5 text-xs sm:text-sm font-semibold text-slate-700 text-center transition hover:bg-slate-100">
                        Kembali
                    </a>
                </div>
            </div>
        </section>

        <!-- Chapters -->
        <section class="space-y-4">
            @foreach ($module['chapters'] as $chapter)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:p-8">
                    <!-- Chapter Header -->
                    <div class="mb-6 border-b border-slate-200 pb-6">
                        <h2 class="text-lg font-bold text-slate-900">{{ $chapter['title'] }}</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $chapter['description'] }}</p>
                    </div>

                    <!-- Chapter Materials -->
                    <div class="space-y-4">
                        <!-- Content Section -->
                        @if ($chapter['content'])
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <h3 class="mb-3 text-sm font-bold text-slate-900">Konten Pembelajaran</h3>
                                <div class="prose prose-sm text-slate-700">
                                    {!! $chapter['content'] !!}
                                </div>
                            </div>
                        @endif

                        <!-- Images Section -->
                        @if (!empty($chapter['images']))
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <h3 class="mb-3 text-sm font-bold text-slate-900">Gambar Pembelajaran</h3>
                                <div class="grid gap-4 md:grid-cols-2">
                                    @foreach ($chapter['images'] as $image)
                                        <div class="overflow-hidden rounded-lg bg-slate-200">
                                            <img src="{{ $image }}" alt="Materi gambar"
                                                class="h-48 w-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Video Section -->
                        @if ($chapter['video'])
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                <h3 class="mb-3 text-sm font-bold text-slate-900">Video Pembelajaran</h3>
                                <div class="relative h-64 w-full overflow-hidden rounded-lg bg-slate-900">
                                    <iframe class="h-full w-full" src="{{ $chapter['video'] }}" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                </div>
                            </div>
                        @endif

                        <!-- Assignment Section -->
                        @if ($chapter['assignment'])
                            <div class="rounded-lg border-2 border-amber-200 bg-amber-50 p-4">
                                <h3 class="mb-2 text-sm font-bold text-amber-900">Tugas Pembelajaran</h3>
                                <p class="text-sm text-amber-800">{{ $chapter['assignment'] }}</p>
                                @if ($chapter['assignment_deadline'])
                                    <p class="mt-2 text-xs text-amber-700">
                                        <span class="font-semibold">Deadline:</span> {{ $chapter['assignment_deadline'] }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>

        @if (empty($module['chapters']))
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-3 text-sm font-semibold text-slate-600">Modul belum memiliki bab</p>
                <p class="mt-1 text-xs text-slate-500">Mulai dengan menambahkan bab melalui halaman edit.</p>
                <a href="{{ route('dashboard.instructor.modules.edit', ['module' => $module['id']]) }}"
                    class="mt-4 w-full sm:w-auto inline-block rounded-lg bg-slate-900 px-4 py-2 text-xs sm:text-sm font-semibold text-white transition hover:bg-slate-800">
                    Edit dan Tambah Bab
                </a>
            </div>
        @endif
    </div>
@endsection
