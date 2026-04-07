@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="space-y-6">
        @forelse ($modulesWithProgress as $moduleData)
            @php
                $module = $moduleData['module'];
                $progress = $moduleData['progress'];
            @endphp
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-2xl font-bold text-slate-800 text-center">{{ $module->title }}</h2>
                <p class="mt-5 text-base font-semibold text-slate-700">Durasi: {{ $module->duration }} Jam</p>
                <div class="mt-3 h-2 w-full rounded-full bg-slate-200">
                    <div class="h-2 w-[{{ $progress }}%] rounded-full bg-blue-600"></div>
                </div>
                <p class="mt-1 text-right text-xs text-slate-500">{{ $progress }}% selesai</p>

                <div class="mt-6 flex flex-col sm:justify-end">
                    <a href="{{ route('dashboard.participant.modules.detail', ['module' => $module->slug, 'tab' => 'materi']) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-full bg-black px-6 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800 transition">Lihat
                        Modul</a>
                </div>

                @php
                    $summaryMaterials = $module->materials->isNotEmpty()
                        ? $module->materials
                        : collect($module->chapters ?? [])->map(function ($chapter, $index) {
                            $slug = isset($chapter['title'])
                                ? \Illuminate\Support\Str::slug($chapter['title'])
                                : 'chapter-' . ($index + 1);

                            return (object) [
                                'title' => $chapter['title'] ?? 'Bab ' . ($index + 1),
                                'slug' => $slug . '-' . ($index + 1),
                                'content' => $chapter['content'] ?? ($chapter['description'] ?? null),
                                'thumbnail_url' => null,
                            ];
                        });
                @endphp

                @if ($summaryMaterials->isNotEmpty())
                    <details class="group mt-6 border-t border-slate-200 pt-4" open>
                        <summary
                            class="flex cursor-pointer list-none items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                            <h3 class="text-sm font-bold uppercase tracking-wide text-slate-700">Ringkasan Materi</h3>
                            <span class="text-xs font-semibold text-slate-500 transition group-open:rotate-180">▼</span>
                        </summary>

                        <div class="mt-3 space-y-3">
                            @foreach ($summaryMaterials as $material)
                                <a href="{{ route('dashboard.participant.modules.detail', ['module' => $module->slug, 'tab' => 'materi', 'material' => $material->slug]) }}"
                                    class="block rounded-xl border border-slate-200 p-3 transition hover:border-slate-300 hover:bg-slate-50">
                                    <p class="text-sm font-semibold text-slate-800">{{ $material->title }}</p>
                                    <div class="mt-2 flex items-start gap-3">
                                        <div
                                            class="h-14 w-20 shrink-0 rounded-md border border-slate-300 bg-slate-100 grid place-items-center text-[10px] text-slate-500">
                                            {{ $material->thumbnail_url ? 'Preview' : 'Thumbnail' }}
                                        </div>
                                        <div class="text-xs leading-relaxed text-slate-600">
                                            {{ $material->content ? \Illuminate\Support\Str::limit(strip_tags($material->content), 100) : 'Deskripsi materi belum tersedia.' }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </details>
                @endif
        </article>
        @empty
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-6 text-center">
                <h3 class="text-lg font-semibold text-blue-800">Belum ada modul tersedia</h3>
                <p class="text-sm text-blue-600 mt-2">Modul akan segera ditambahkan oleh pengajar.</p>
            </div>
            @endforelse
        </section>
    @endsection
