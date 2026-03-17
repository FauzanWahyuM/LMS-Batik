@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @if (session('status'))
            <div
                class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-center text-xl font-bold text-slate-800 sm:text-left">Modul Pembelajaran</h2>
            <a href="{{ route('dashboard.participant.modules') }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                Kembali
            </a>
        </div>
        <p class="mt-3 text-center text-sm font-semibold text-slate-700">{{ $moduleData['title'] }}</p>
        <p class="mt-1 text-sm font-semibold text-slate-700">Durasi: {{ $moduleData['duration'] }}</p>

        <div class="mt-4 h-2 w-full rounded-full bg-slate-200">
            <div class="h-2 rounded-full bg-blue-600" style="width: {{ $moduleData['progress'] }}%"></div>
        </div>
        <p class="mt-1 text-right text-xs text-slate-500">{{ $moduleData['progress'] }}% selesai</p>

        <div class="mt-4 border-t border-slate-300 pt-4"></div>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">
            @php
                $tabMap = [
                    'materi' => 'Materi',
                    'video' => 'Video',
                    'tugas' => 'Tugas',
                    'diskusi' => 'Diskusi',
                ];
            @endphp

            @foreach ($tabMap as $tabKey => $tabLabel)
                <a href="{{ route('dashboard.participant.modules.detail', ['module' => $moduleSlug, 'tab' => $tabKey]) }}"
                    class="rounded-md border px-3 py-2 text-center text-xs font-semibold transition {{ $activeTab === $tabKey ? 'border-black bg-black text-white' : 'border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                    {{ $tabLabel }}
                </a>
            @endforeach
        </div>

        @if ($activeTab === 'materi')
            <div class="mt-5 border-t border-slate-300 pt-4">
                <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Pindah Materi</h4>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($moduleData['materials'] as $material)
                        <a href="{{ route('dashboard.participant.modules.detail', ['module' => $moduleSlug, 'tab' => 'materi', 'material' => $material['slug']]) }}"
                            class="rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $selectedMaterial['slug'] === $material['slug'] ? 'border-black bg-black text-white' : 'border-slate-300 text-slate-700 hover:bg-white' }}">
                            {{ $material['title'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-slate-300 bg-slate-50 p-4 sm:p-5">
                <h3 class="text-lg font-bold text-slate-800">{{ $selectedMaterial['title'] }}</h3>
                <div class="mt-3 grid gap-4 sm:grid-cols-[220px_1fr] sm:items-start">
                    <div
                        class="h-36 rounded-lg border-2 border-slate-700 bg-white grid place-items-center text-xs text-slate-600 sm:h-32">
                        {{ $selectedMaterial['thumbnailLabel'] }}
                    </div>
                    <p class="text-sm leading-relaxed text-slate-700">{{ $selectedMaterial['summary'] }}</p>
                </div>
            </div>
        @elseif ($activeTab === 'video')
            <div class="mt-6 rounded-lg border border-slate-200 p-4">
                <h3 class="text-center text-sm font-semibold text-slate-700">{{ $moduleData['videoTitle'] }}</h3>
                <div
                    class="mt-3 h-48 rounded-md border border-slate-400 bg-slate-100 grid place-items-center text-sm text-slate-600">
                    Video
                </div>

                <p class="mt-4 text-justify text-sm leading-relaxed text-slate-600">{{ $moduleData['description'] }}</p>
            </div>
        @elseif ($activeTab === 'tugas')
            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:p-6">
                <h3 class="text-center text-2xl font-bold text-slate-800">Instruksi Tugas</h3>

                <ol class="mx-auto mt-5 max-w-2xl list-decimal space-y-2 pl-5 text-base font-semibold text-slate-800">
                    @foreach ($moduleData['taskItems'] as $taskItem)
                        <li>{{ $taskItem }}</li>
                    @endforeach
                </ol>

                <p class="mx-auto mt-5 max-w-2xl text-lg font-bold text-slate-800">Deadline Tugas:
                    {{ $moduleData['deadline'] }}</p>

                <form action="{{ route('dashboard.participant.modules.tasks.upload', ['module' => $moduleSlug]) }}"
                    method="POST" enctype="multipart/form-data"
                    class="mx-auto mt-6 max-w-2xl rounded-none border-2 border-slate-700 bg-white p-6">
                    @csrf
                    <p class="text-center text-lg font-bold text-slate-800">Upload Tugas</p>

                    <div class="mt-4">
                        <label for="assignment_file" class="mb-2 block text-sm font-semibold text-slate-700">Pilih File
                            Tugas</label>
                        <input id="assignment_file" name="assignment_file" type="file" required
                            class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded file:border-0 file:bg-slate-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-slate-800">
                        <p class="mt-2 text-xs text-slate-500">Format: PDF, DOC, DOCX, PPT, PPTX, JPG, PNG, ZIP, RAR.
                            Maksimal 10MB.</p>
                        @error('assignment_file')
                            <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center rounded-full bg-black px-5 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Kirim Tugas
                        </button>
                    </div>
                </form>

                <div class="mx-auto mt-6 max-w-2xl space-y-4">
                    <div>
                        <p class="mb-1 text-lg font-bold text-slate-800">Nilai Tugas</p>
                        <input type="text" placeholder="Belum ada nilai" readonly
                            class="w-full rounded-sm border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 focus:outline-none">
                    </div>
                    <div>
                        <p class="mb-1 text-lg font-bold text-slate-800">Feedback Pengajar</p>
                        <input type="text" placeholder="Belum ada feedback" readonly
                            class="w-full rounded-sm border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500 focus:outline-none">
                    </div>
                </div>
            </div>
        @else
            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:p-6">
                <h3 class="text-center text-3xl font-bold text-slate-800">Forum Diskusi</h3>

                <div class="mx-auto mt-5 max-w-4xl space-y-3">
                    <form class="flex items-center gap-2">
                        <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-slate-400 bg-white">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M5.121 17.804A9 9 0 1118.879 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <input type="text" placeholder="Input komentar/Pertanyaan"
                            class="h-8 flex-1 rounded-full border border-slate-400 bg-white px-3 text-xs text-slate-700 focus:border-slate-600 focus:outline-none">
                        <button type="button"
                            class="inline-flex h-8 items-center gap-1 rounded-full border border-slate-500 px-4 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                            Kirim
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </form>

                    @foreach ($moduleData['discussionItems'] as $discussionItem)
                        <div class="flex items-start gap-2">
                            <div
                                class="mt-1 grid h-8 w-8 shrink-0 place-items-center rounded-full border border-slate-400 bg-white">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M5.121 17.804A9 9 0 1118.879 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 rounded-xl border border-slate-400 bg-white px-3 py-2">
                                <p class="text-xs font-semibold text-slate-700">{{ $discussionItem['name'] }}</p>
                                <p class="mt-1 text-xs text-slate-600">{{ $discussionItem['message'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
