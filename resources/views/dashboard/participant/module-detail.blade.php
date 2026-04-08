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
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                Kembali
            </a>
        </div>
        <p class="mt-3 text-center text-sm font-semibold text-slate-700">{{ $module->title }}</p>
        <p class="mt-1 text-sm font-semibold text-slate-700">Durasi: {{ $module->duration }} Jam</p>

        <div class="mt-4 h-2 w-full rounded-full bg-slate-200">
            <div id="module-progress-bar" class="h-2 rounded-full bg-blue-600"
                style="width: {{ $moduleData['overall_progress'] }}%"></div>
        </div>
        <p id="module-progress-text" class="mt-1 text-right text-xs text-slate-500">{{ $moduleData['overall_progress'] }}%
            selesai
        </p>
        <p id="module-progress-count" class="mt-1 text-xs text-slate-500">Bab selesai:
            {{ $moduleData['completed_count'] }}/{{ $moduleData['total_count'] }}</p>

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
                        <a href="{{ route('dashboard.participant.modules.detail', ['module' => $moduleSlug, 'tab' => 'materi', 'material' => $material->slug]) }}"
                            data-material-chip="{{ $material->slug }}"
                            class="rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $selectedMaterial && $selectedMaterial->slug === $material->slug ? 'border-black bg-black text-white' : 'border-slate-300 text-slate-700 hover:bg-white' }}">
                            {{ $material->title }}
                            @if (!empty($material->is_completed))
                                <span class="ml-1">✓</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($selectedMaterial)
                <div class="mt-6 rounded-xl border border-slate-300 bg-slate-50 p-4 sm:p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3" id="selected-material-header"
                        data-material-slug="{{ $selectedMaterial->slug }}">
                        <h3 class="text-lg font-bold text-slate-800">{{ $selectedMaterial->title }}</h3>
                        @if (!empty($selectedMaterial->is_completed))
                            <span id="selected-material-badge"
                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Bab
                                selesai</span>
                        @else
                            <button type="button" id="mark-complete-btn"
                                data-complete-url="{{ route('dashboard.participant.modules.material.complete', ['module' => $moduleSlug, 'material' => $selectedMaterial->slug]) }}"
                                class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                Tandai Bab Selesai
                            </button>
                        @endif
                    </div>
                    <div class="mt-3 text-sm leading-relaxed text-slate-700">
                        {!! $selectedMaterial->content ?: '<p>Konten materi belum tersedia.</p>' !!}
                    </div>
                </div>
            @endif
        @elseif ($activeTab === 'video')
            <div class="mt-6 rounded-lg border border-slate-200 p-4">
                <h3 class="text-center text-sm font-semibold text-slate-700">{{ $module->title }} - Video Pembelajaran</h3>
                <div class="mt-3 rounded-md border border-slate-400 bg-slate-100 p-4 text-sm text-slate-600">
                    @if ($selectedMaterial && $selectedMaterial->video_url)
                        @php
                            $videoUrl = $selectedMaterial->video_url;
                            $isDirectVideo = preg_match('/\.(mp4|webm|ogg|mov|m3u8)(\?|$)/i', $videoUrl);
                        @endphp

                        @if ($isDirectVideo)
                            <video controls class="w-full rounded-lg bg-black">
                                <source src="{{ $videoUrl }}" type="video/mp4">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        @else
                            <div class="relative h-64 w-full overflow-hidden rounded-lg bg-slate-900">
                                <iframe class="h-full w-full" src="{{ $videoUrl }}" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                        @endif
                    @else
                        <p>Video belum tersedia</p>
                    @endif
                </div>

                <p class="mt-4 text-justify text-sm leading-relaxed text-slate-600">
                    {!! $selectedMaterial
                        ? ($selectedMaterial->content ?:
                            '<span>Deskripsi video belum tersedia.</span>')
                        : '<span>Pilih materi terlebih dahulu.</span>' !!}
                </p>
            </div>
        @elseif ($activeTab === 'tugas')
            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:p-6">
                <h3 class="text-center text-2xl font-bold text-slate-800">Instruksi Tugas</h3>

                @php
                    $assignmentMaterials = $moduleData['materials']->where('type', 'assignment');
                @endphp

                @if ($assignmentMaterials->count() > 0)
                    <div class="mx-auto mt-5 max-w-3xl space-y-4">
                        @foreach ($assignmentMaterials as $assignment)
                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-base font-bold text-slate-800">{{ $assignment->title }}</p>
                                    <span class="text-xs font-semibold text-amber-700">Deadline:
                                        {{ $assignment->metadata['deadline'] ?? 'Belum ditentukan' }}</span>
                                </div>

                                @if (!empty($assignment->content))
                                    <div class="mt-2 text-sm text-slate-600">{!! $assignment->content !!}</div>
                                @endif

                                <form
                                    action="{{ route('dashboard.participant.modules.tasks.upload', ['module' => $moduleSlug]) }}"
                                    method="POST" enctype="multipart/form-data"
                                    class="mt-4 rounded-lg border border-slate-300 bg-slate-50 p-4">
                                    @csrf
                                    <input type="hidden" name="material_slug" value="{{ $assignment->slug }}">

                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Upload File Tugas</label>
                                    <input name="assignment_file" type="file" required
                                        class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded file:border-0 file:bg-slate-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-slate-800">
                                    <p class="mt-2 text-xs text-slate-500">Format: PDF, DOC, DOCX, PPT, PPTX, JPG, PNG, ZIP,
                                        RAR. Maksimal 10MB.</p>

                                    <div class="mt-4 flex justify-end">
                                        <button type="submit"
                                            class="rounded-full bg-black px-5 py-2 text-xs sm:text-sm font-semibold text-white transition hover:bg-slate-800">
                                            Kirim Tugas
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mx-auto mt-5 max-w-2xl text-center text-slate-600">Instruksi tugas belum tersedia.</p>
                @endif

                <div class="mx-auto mt-6 max-w-2xl space-y-4">
                    @php
                        $userAssignments = $moduleData['assignments'];
                    @endphp

                    @if ($userAssignments->count() > 0)
                        @foreach ($userAssignments as $assignment)
                            <div class="border border-slate-200 rounded p-4">
                                <p class="mb-1 text-lg font-bold text-slate-800">Status Pengumpulan</p>
                                <p class="text-sm text-slate-600">{{ $assignment->submitted_at->format('d M Y H:i') }}</p>
                                <p class="text-sm text-slate-600">File: {{ $assignment->original_filename }}
                                    ({{ $assignment->formatted_file_size }})
                                </p>

                                @if ($assignment->isGraded())
                                    <div class="mt-3">
                                        <p class="mb-1 text-lg font-bold text-slate-800">Nilai Tugas</p>
                                        <p class="text-sm text-slate-600">{{ $assignment->score }}/100</p>
                                        <p class="mb-1 text-lg font-bold text-slate-800">Feedback Pengajar</p>
                                        <p class="text-sm text-slate-600">
                                            {{ $assignment->feedback ?: 'Tidak ada feedback' }}</p>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <p class="mb-1 text-lg font-bold text-slate-800">Nilai Tugas</p>
                                        <p class="text-sm text-slate-600">Belum dinilai</p>
                                        <p class="mb-1 text-lg font-bold text-slate-800">Feedback Pengajar</p>
                                        <p class="text-sm text-slate-600">Belum ada feedback</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
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
                    @endif
                </div>
            </div>
        @else
            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:p-6">
                <h3 class="text-center text-3xl font-bold text-slate-800">Forum Diskusi</h3>

                <div class="mx-auto mt-5 max-w-4xl space-y-3">
                    <form class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-2">
                        <div
                            class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-slate-400 bg-white">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M5.121 17.804A9 9 0 1118.879 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <input type="text" placeholder="Input komentar/Pertanyaan"
                            class="flex-1 h-8 rounded-full border border-slate-400 bg-white px-3 text-xs text-slate-700 focus:border-slate-600 focus:outline-none">
                        <button type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1 h-8 rounded-full border border-slate-500 px-4 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                            Kirim
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </section>

    <script>
        const markCompleteBtn = document.getElementById('mark-complete-btn');
        const progressBar = document.getElementById('module-progress-bar');
        const progressText = document.getElementById('module-progress-text');
        const progressCount = document.getElementById('module-progress-count');
        const selectedMaterialHeader = document.getElementById('selected-material-header');

        async function refreshProgress() {
            const url = "{{ route('dashboard.participant.modules.progress', ['module' => $moduleSlug]) }}";
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const percentage = data?.progress ?? 0;
            const completed = data?.statistics?.completed_materials ?? 0;
            const total = data?.statistics?.total_materials ?? 0;

            if (progressBar) {
                progressBar.style.width = `${percentage}%`;
            }
            if (progressText) {
                progressText.textContent = `${percentage}% selesai`;
            }
            if (progressCount) {
                progressCount.textContent = `Bab selesai: ${completed}/${total}`;
            }
        }

        function markCurrentChapterCompletedUI() {
            const slug = selectedMaterialHeader?.dataset.materialSlug;
            if (!slug) {
                return;
            }

            const chip = document.querySelector(`[data-material-chip="${slug}"]`);
            if (chip && !chip.textContent.includes('✓')) {
                const check = document.createElement('span');
                check.className = 'ml-1';
                check.textContent = '✓';
                chip.appendChild(check);
            }

            if (!document.getElementById('selected-material-badge') && selectedMaterialHeader) {
                const badge = document.createElement('span');
                badge.id = 'selected-material-badge';
                badge.className = 'rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700';
                badge.textContent = 'Bab selesai';
                selectedMaterialHeader.appendChild(badge);
            }

            markCompleteBtn?.remove();
        }

        markCompleteBtn?.addEventListener('click', async function() {
            const url = this.dataset.completeUrl;
            if (!url) {
                return;
            }

            this.disabled = true;
            this.textContent = 'Menyimpan...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Gagal menyimpan progres');
                }

                markCurrentChapterCompletedUI();
                await refreshProgress();
            } catch (error) {
                this.disabled = false;
                this.textContent = 'Tandai Bab Selesai';
                alert('Tidak dapat menandai bab sebagai selesai. Silakan coba lagi.');
            }
        });
    </script>
@endsection
