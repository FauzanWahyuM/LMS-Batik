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
                <a href="{{ route('dashboard.participant.modules.detail', array_filter(['module' => $moduleSlug, 'tab' => $tabKey, 'material' => $selectedMaterial?->slug])) }}"
                    class="rounded-md border px-3 py-2 text-center text-xs font-semibold transition {{ $activeTab === $tabKey ? 'border-black bg-black text-white' : 'border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                    {{ $tabLabel }}
                </a>
            @endforeach
        </div>

        @if ($moduleData['materials']->isNotEmpty())
            <div class="mt-5 border-t border-slate-300 pt-4">
                <h4 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Pindah Materi</h4>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($moduleData['materials'] as $material)
                        <a href="{{ route('dashboard.participant.modules.detail', array_filter(['module' => $moduleSlug, 'tab' => $activeTab, 'material' => $material->slug])) }}"
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
        @endif

        @if ($activeTab === 'materi')
            @if ($selectedMaterial)
                @php
                    $materialMeta = is_array($selectedMaterial->metadata ?? null) ? $selectedMaterial->metadata : [];
                    $materialImages = $materialMeta['images'] ?? [];

                    if (!is_array($materialImages)) {
                        $materialImages = [];
                    }

                    if (empty($materialImages) && !empty($materialMeta['image_path'])) {
                        $materialImages = [
                            [
                                'path' => $materialMeta['image_path'],
                                'title' => $materialMeta['image_title'] ?? '',
                                'caption' => $materialMeta['image_caption'] ?? '',
                                'width' => $materialMeta['image_width'] ?? 75,
                            ],
                        ];
                    }

                    $materialImages = array_values(
                        array_filter(
                            array_map(function (array $image): array {
                                return [
                                    'path' => $image['path'] ?? ($image['existing_path'] ?? null),
                                    'title' => $image['title'] ?? ($image['image_title'] ?? ''),
                                    'caption' => $image['caption'] ?? ($image['image_caption'] ?? ''),
                                    'width' => max(
                                        25,
                                        min(100, (int) ($image['width'] ?? ($image['image_width'] ?? 75))),
                                    ),
                                ];
                            }, $materialImages),
                            fn(array $image) => !empty($image['path']),
                        ),
                    );
                @endphp
                <div class="mt-6 rounded-xl border border-slate-300 bg-slate-50 p-4 sm:p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3" id="selected-material-header"
                        data-material-slug="{{ $selectedMaterial->slug }}">
                        <h3 class="text-lg font-bold text-slate-800">{{ $selectedMaterial->title }}</h3>
                        <button type="button" id="mark-complete-btn"
                            data-complete-url="{{ route('dashboard.participant.modules.material.complete', ['module' => $moduleSlug, 'material' => $selectedMaterial->slug]) }}"
                            data-completed="{{ !empty($selectedMaterial->is_completed) ? '1' : '0' }}"
                            class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                            {{ !empty($selectedMaterial->is_completed) ? 'Bab Selesai' : 'Tandai Sebagai Selesai' }}
                        </button>
                    </div>
                    <div class="mt-3 text-sm leading-relaxed text-slate-700">
                        {!! $selectedMaterial->content ?: '<p>Konten materi belum tersedia.</p>' !!}
                    </div>

                    @if (!empty($materialImages))
                        <div class="mt-5 rounded-lg border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-slate-800">Gambar Pendukung</p>
                                <span class="text-xs text-slate-500">{{ count($materialImages) }} gambar</span>
                            </div>

                            <div class="mt-4 space-y-4">
                                @foreach ($materialImages as $image)
                                    <article class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                        <div class="border-b border-slate-200 bg-white p-4">
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $image['title'] ?: 'Gambar Pendukung' }}
                                            </p>
                                        </div>
                                        <div class="p-4">
                                            <div class="flex justify-center">
                                                <img src="{{ route('public-file', ['path' => ltrim($image['path'], '/')]) }}"
                                                    alt="{{ $image['title'] ?: 'Gambar pendukung materi' }}"
                                                    class="rounded-md border border-slate-200 object-cover"
                                                    style="width: {{ $image['width'] }}%; max-width: 100%; height: auto;">
                                            </div>
                                            @if (!empty($image['caption']))
                                                <p class="mt-3 text-center text-xs text-slate-600">{{ $image['caption'] }}
                                                </p>
                                            @endif
                                            <p class="mt-2 text-center text-[11px] uppercase tracking-wide text-slate-400">
                                                Approx. display: {{ $image['width'] }}%
                                            </p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
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
                    $materialAssignments = collect($moduleData['task_instructions'] ?? [])->filter(function (
                        $assignment,
                    ) use ($selectedMaterial) {
                        return $selectedMaterial && $assignment->slug === $selectedMaterial->slug;
                    });
                @endphp

                @if ($materialAssignments->count() > 0)
                    <div class="mx-auto mt-5 max-w-3xl space-y-4">
                        @foreach ($materialAssignments as $assignment)
                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-base font-bold text-slate-800">{{ $assignment->title }}</p>
                                    <span class="text-xs font-semibold text-amber-700">Deadline:
                                        {{ $assignment->deadline ?? 'Belum ditentukan' }}</span>
                                </div>

                                @if (!empty($assignment->assignment))
                                    <div class="mt-2 text-sm text-slate-600">{{ $assignment->assignment }}</div>
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
                    <p class="mx-auto mt-5 max-w-2xl text-center text-slate-600">Instruksi tugas belum tersedia untuk bab
                        ini.</p>
                @endif

                <div class="mx-auto mt-6 max-w-2xl space-y-4">
                    @php
                        $userAssignments = collect($moduleData['assignments'])->filter(function ($assignment) use (
                            $selectedMaterial,
                        ) {
                            if (!$selectedMaterial) {
                                return false;
                            }
                            return $assignment->material_id == $selectedMaterial->id ||
                                $assignment->material_slug === $selectedMaterial->slug;
                        });
                    @endphp

                    @if ($userAssignments->count() > 0)
                        @foreach ($userAssignments as $assignment)
                            <div class="border border-slate-200 rounded p-4">
                                <p class="mb-1 text-lg font-bold text-slate-800">Status Pengumpulan</p>
                                <p class="text-sm text-slate-600">
                                    {{ $assignment->submitted_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                </p>

                                @if (str_starts_with($assignment->mime_type ?? '', 'image/'))
                                    <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                                        <img src="{{ $assignment->file_url }}" alt="Preview tugas"
                                            class="h-72 w-full object-contain bg-slate-100">
                                    </div>
                                    <p class="mt-3 text-sm text-slate-600">{{ $assignment->original_filename }}
                                        ({{ $assignment->formatted_file_size }})
                                    </p>
                                @else
                                    <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-sm font-semibold text-slate-800">File Pengiriman</p>
                                        <p class="mt-2 text-sm text-slate-600">{{ $assignment->original_filename }}</p>
                                        <p class="text-xs text-slate-500">{{ $assignment->formatted_file_size }}</p>
                                        <p class="mt-2 text-sm">
                                            <a href="{{ $assignment->file_url }}" target="_blank"
                                                class="text-blue-600 underline">Lihat file</a>
                                        </p>
                                    </div>
                                @endif

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
                            <p class="mb-1 text-lg font-bold text-slate-800">Status Pengumpulan</p>
                            <p class="text-sm text-slate-600">Belum ada pengumpulan untuk bab ini</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="mt-6">
                @php
                    $filteredDiscussions = $moduleDiscussions ?? collect();
                    if ($selectedMaterial) {
                        $materialTitle = strtolower($selectedMaterial->title);
                        $filteredDiscussions = $filteredDiscussions->filter(function ($discussion) use (
                            $materialTitle,
                        ) {
                            return str_contains(strtolower($discussion->title), $materialTitle) ||
                                str_contains(strtolower($discussion->content), $materialTitle);
                        });
                    }
                @endphp
                @include('forum.discussions-list', [
                    'discussions' => $filteredDiscussions,
                    'user' => $user ?? [],
                    'moduleContext' => true,
                    'showModuleFilter' => false,
                    'moduleSlug' => $moduleSlug,
                    'moduleTitle' => $module->title ?? null,
                    'selectedModuleSlug' => $moduleSlug,
                ])
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
            const percentage = data?.overall_progress ?? data?.progress ?? 0;
            const completed = data?.statistics?.completed_materials ?? 0;
            const total = data?.statistics?.total_materials ?? 0;

            updateProgressSummary(percentage, completed, total);
        }

        function updateProgressSummary(percentage, completed, total) {
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

        function updateSelectedMaterialStatus(isCompleted) {
            const slug = selectedMaterialHeader?.dataset.materialSlug;
            if (slug) {
                const chip = document.querySelector(`[data-material-chip="${slug}"]`);
                if (chip) {
                    const hasCheck = Array.from(chip.childNodes).some(node => node.textContent === '✓');
                    if (isCompleted && !hasCheck) {
                        const check = document.createElement('span');
                        check.className = 'ml-1';
                        check.textContent = '✓';
                        chip.appendChild(check);
                    }
                    if (!isCompleted && hasCheck) {
                        const check = Array.from(chip.childNodes).find(node => node.textContent === '✓');
                        if (check) {
                            chip.removeChild(check);
                        }
                    }
                }
            }

            if (markCompleteBtn) {
                markCompleteBtn.textContent = isCompleted ? 'Bab Selesai' : 'Tandai Sebagai Selesai';
                markCompleteBtn.dataset.completed = isCompleted ? '1' : '0';
            }

            const selectedProgress = document.getElementById('selected-material-progress');
            const selectedProgressText = document.getElementById('selected-material-progress-text');
            if (selectedProgress) {
                selectedProgress.style.width = isCompleted ? '100%' : '0%';
            }
            if (selectedProgressText) {
                selectedProgressText.textContent = isCompleted ? '100% selesai' : '0% selesai';
            }
        }

        markCompleteBtn?.addEventListener('click', async function() {
            const url = this.dataset.completeUrl;
            if (!url) {
                return;
            }

            this.disabled = true;
            this.textContent = 'Menyimpan...';
            let completedState = this.dataset.completed === '1';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: new URLSearchParams({
                        _token: '{{ csrf_token() }}',
                    }),
                });

                if (!response.ok) {
                    throw new Error('Gagal menyimpan progres');
                }

                const data = await response.json();
                if (data?.completed !== undefined) {
                    completedState = Boolean(data.completed);
                } else {
                    // fallback kalau backend ga kirim
                    completedState = !completedState;
                }
                updateSelectedMaterialStatus(completedState);

                if (data?.progress?.overall !== undefined) {
                    updateProgressSummary(
                        data.progress.overall,
                        data?.statistics?.completed_materials ?? 0,
                        data?.statistics?.total_materials ?? 0,
                    );
                } else {
                    await refreshProgress();
                }
            } catch (error) {
                alert('Tidak dapat mengubah status bab. Silakan coba lagi.');
            } finally {
                if (markCompleteBtn) {
                    markCompleteBtn.disabled = false;
                    markCompleteBtn.textContent = completedState ? 'Bab Selesai' : 'Tandai Sebagaiagai Selesai';
                    markCompleteBtn.dataset.completed = completedState ? '1' : '0';
                }
            }
        });

        refreshProgress();
    </script>
@endsection
