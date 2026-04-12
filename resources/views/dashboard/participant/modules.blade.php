@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="space-y-6">
        @forelse ($modulesWithProgress as $moduleData)
            @php
                $module = $moduleData['module'];
                $progress = $moduleData['progress'];
                $summaryMaterials = $moduleData['moduleData']['materials'];
                $completedMaterials = $moduleData['moduleData']['completed_count'];
                $totalMaterials = $moduleData['moduleData']['total_count'];
            @endphp
            <article
                class="group overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-slate-300 hover:shadow-md">
                <div class="relative h-40 w-full overflow-hidden bg-linear-to-br from-slate-100 to-slate-200 sm:h-48">
                    @if ($module->cover)
                        <img src="{{ route('public-file', ['path' => ltrim($module->cover, '/')]) }}"
                            alt="{{ $module->title }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center bg-slate-100">
                            <svg class="h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="p-5 sm:p-6">
                    <h2 class="text-xl font-bold text-slate-800">{{ $module->title }}</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-700">Durasi: {{ $module->duration }} Jam</p>
                    <div class="mt-3 h-2 w-full rounded-full bg-slate-200">
                        <div class="h-2 rounded-full bg-blue-600" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="mt-1 text-right text-xs text-slate-500">{{ $progress }}% selesai</p>
                    <p class="mt-1 text-xs text-slate-500">Bab selesai: {{ $completedMaterials }}/{{ $totalMaterials }}</p>

                    <div class="mt-5 flex flex-col sm:justify-end">
                        <a href="{{ route('dashboard.participant.modules.detail', ['module' => $module->slug, 'tab' => 'materi']) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center rounded-full bg-black px-6 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800 transition">Lihat
                            Modul</a>
                    </div>

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
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="text-sm font-semibold text-slate-800">{{ $material->title }}</p>
                                            @if (!empty($material->is_completed))
                                                <span
                                                    class="rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Selesai</span>
                                            @endif
                                        </div>
                                        <div class="mt-2 text-xs leading-relaxed text-slate-600">
                                            {{ $material->content ? \Illuminate\Support\Str::limit(strip_tags($material->content), 120) : 'Deskripsi materi belum tersedia.' }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-6 text-center">
                <h3 class="text-lg font-semibold text-blue-800">Belum ada modul tersedia</h3>
                <p class="text-sm text-blue-600 mt-2">Modul akan segera ditambahkan oleh pengajar.</p>
            </div>
        @endforelse
    </section>
@endsection
