@extends('layouts.landing')

@section('title', 'Galeri - LMS Batik')

@section('content')

    <section class="relative bg-cover bg-center h-screen flex items-center justify-center"
        style="background-image: linear-gradient(to bottom, rgba(40, 25, 15, 0.3), rgba(40, 25, 15, 0.8)), url('{{ asset('img/Batik4.jpg') }}');">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-10 relative z-10">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-tight tracking-wide drop-shadow-lg"
                style="font-family: 'Georgia', serif;">
                Galeri Kami
            </h1>
            <div class="w-20 h-1 bg-amber-500 mx-auto my-6 rounded-full opacity-90"></div>
            <p class="text-xl md:text-2xl text-gray-200 font-light tracking-widest drop-shadow-md"
                style="font-family: 'Georgia', serif;">
                LPK Kama Praja Madiun
            </p>
        </div>

    </section>

    <section class="py-16 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <div class="mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900" style="font-family: 'Georgia', serif;">
                        Galeri Karya Peserta
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">Karya terbaru yang diunggah oleh peserta pelatihan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse (($gallery ?? collect()) as $artwork)
                    <article
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="h-56 overflow-hidden bg-slate-100">
                            <img src="{{ route('public-file', ['path' => ltrim($artwork->image_path, '/')]) }}"
                                alt="{{ $artwork->title }}"
                                class="h-full w-full object-cover transition-transform duration-500 hover:scale-105">
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-slate-900" style="font-family: 'Georgia', serif;">
                                {{ $artwork->title }}</h3>
                            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                                {{ \Illuminate\Support\Str::limit($artwork->description, 120) }}
                            </p>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-amber-700">Karya:
                                {{ $artwork->creator_name }}</p>
                        </div>
                    </article>
                @empty
                    <div
                        class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-600">
                        Belum ada karya peserta yang dipublikasikan.
                    </div>
                @endforelse
            </div>

            @if (($gallery ?? null) instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $gallery->total() > 6)
                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a href="{{ $gallery->previousPageUrl() ?? '#' }}"
                        class="inline-flex items-center rounded-full border border-gray-300 px-5 py-2 text-sm font-semibold text-gray-700 transition {{ $gallery->onFirstPage() ? 'pointer-events-none opacity-50' : 'hover:border-amber-500 hover:text-amber-700' }}"
                        aria-disabled="{{ $gallery->onFirstPage() ? 'true' : 'false' }}">
                        Sebelumnya
                    </a>
                    <p class="text-sm text-gray-600">
                        Halaman {{ $gallery->currentPage() }} dari {{ $gallery->lastPage() }}
                    </p>
                    <a href="{{ $gallery->nextPageUrl() ?? '#' }}"
                        class="inline-flex items-center rounded-full border border-gray-300 px-5 py-2 text-sm font-semibold text-gray-700 transition {{ $gallery->hasMorePages() ? 'hover:border-amber-500 hover:text-amber-700' : 'pointer-events-none opacity-50' }}"
                        aria-disabled="{{ $gallery->hasMorePages() ? 'false' : 'true' }}">
                        Berikutnya
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="py-20 bg-gray-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-950 mb-4" style="font-family: 'Georgia', serif;">
                    Prestasi Alumni Kami</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Karya-karya terbaik dari alumni yang telah meraih
                    berbagai penghargaan</p>
            </div>

            <div class="relative max-w-6xl mx-auto">
                <div class="overflow-hidden py-4">
                    <div class="flex transition-transform duration-500 ease-in-out" id="prestasi-slider">
                        @forelse (($achievements ?? collect()) as $achievement)
                            @php
                                $rank = $achievement->rank;
                                $rankLabel = match ($rank) {
                                    1 => 'Gold',
                                    2 => 'Silver',
                                    3 => 'Bronze',
                                    default => null,
                                };
                                $badgeClass = match ($rank) {
                                    1 => 'from-yellow-400 to-yellow-600',
                                    2 => 'from-slate-300 to-slate-500',
                                    3 => 'from-amber-600 to-amber-800',
                                    default => 'from-blue-400 to-blue-600',
                                };
                            @endphp

                            <div class="min-w-full md:min-w-[50%] lg:min-w-[33.333%] shrink-0 px-4">
                                <div
                                    class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition h-full border border-gray-100">
                                    <div
                                        class="bg-linear-to-br {{ $badgeClass }} w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                        @if ($rank === 1)
                                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 3l2.5 5.5L20 9l-4 4 .95 5.5L12 16l-4.95 2.5L8 13 4 9l5.5-.5L12 3z" />
                                            </svg>
                                        @elseif ($rank === 2)
                                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 21h8m-4-4v4M7 4h10l-1 5a4 4 0 01-4 3H12a4 4 0 01-4-3L7 4z" />
                                            </svg>
                                        @elseif ($rank === 3)
                                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 3h6l1 6-4 3-4-3 1-6zm3 9v9m-4 0h8" />
                                            </svg>
                                        @else
                                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                                        {{ $rankLabel ?? $achievement->title }}
                                    </h3>
                                    <p class="text-gray-600 mb-2 text-sm">
                                        {{ $achievement->event_name }}{{ $achievement->year ? ' - ' . $achievement->year : '' }}
                                    </p>
                                    <p class="text-sm text-amber-600 font-semibold">{{ $achievement->winner_name }}</p>
                                    @if (!empty($achievement->description))
                                        <p class="mt-3 text-xs text-slate-500">
                                            {{ \Illuminate\Support\Str::limit($achievement->description, 90) }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="min-w-full px-4">
                                <div
                                    class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-600">
                                    Data prestasi belum tersedia.
                                </div>
                            </div>
                        @endforelse

                    </div>
                </div>

                <button id="prev-prestasi"
                    class="hidden md:flex absolute -left-6 top-1/2 -translate-y-1/2 bg-white text-blue-950 hover:bg-amber-500 hover:text-white border border-gray-200 p-3 rounded-full shadow-lg transition z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button id="next-prestasi"
                    class="hidden md:flex absolute -right-6 top-1/2 -translate-y-1/2 bg-white text-blue-950 hover:bg-amber-500 hover:text-white border border-gray-200 p-3 rounded-full shadow-lg transition z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

            </div>
        </div>
    </section>

    <section class="relative overflow-hidden py-20">
        <div class="absolute inset-0 bg-linear-to-br from-blue-950 via-blue-900 to-slate-900"></div>
        <div class="absolute -left-16 top-0 h-48 w-48 rounded-full bg-amber-500/20 blur-2xl"></div>
        <div class="absolute -right-10 bottom-0 h-52 w-52 rounded-full bg-sky-400/20 blur-2xl"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="rounded-3xl bg-blue-950 border border-white/10 p-8 md:p-12 text-center backdrop-blur-sm shadow-2xl">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-5" style="font-family: 'Georgia', serif;">
                    Ciptakan Karya Batik Anda Sendiri
                </h2>
                <p class="text-base md:text-lg text-slate-200 mb-8 max-w-2xl mx-auto leading-relaxed">
                    Bergabunglah dengan program kami dan wujudkan karya batik impian Anda menjadi nyata bersama pembimbing
                    profesional.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('landing.registration') }}"
                        class="bg-amber-500 text-slate-900 px-8 py-3 rounded-full text-base font-bold hover:bg-amber-400 transition shadow-lg">
                        Daftar Sekarang
                    </a>
                    <a href="{{ route('landing.programs') }}"
                        class="border border-slate-200/70 text-white px-8 py-3 rounded-full text-base font-semibold hover:bg-white hover:text-blue-950 transition">
                        Lihat Program
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            function initPrestasiSlider() {
                const slider = document.getElementById("prestasi-slider");
                const prevBtn = document.getElementById("prev-prestasi");
                const nextBtn = document.getElementById("next-prestasi");

                if (!slider) return;

                let currentSlide = 0;
                const totalSlides = slider.children.length;
                let autoSlideInterval;

                function getVisibleItems() {
                    if (window.innerWidth >= 1024) return 3;
                    if (window.innerWidth >= 768) return 2;
                    return 1;
                }

                function getTotalPages() {
                    return Math.max(1, totalSlides - getVisibleItems() + 1);
                }

                function goToSlide(index) {
                    const pages = getTotalPages();
                    if (pages <= 1) return;

                    if (index < 0) currentSlide = pages - 1;
                    else if (index >= pages) currentSlide = 0;
                    else currentSlide = index;

                    const percentage = 100 / getVisibleItems();
                    slider.style.transform = `translateX(-${currentSlide * percentage}%)`;
                }

                function startAutoSlide() {
                    autoSlideInterval = setInterval(() => goToSlide(currentSlide + 1), 4000);
                }

                function resetAutoSlide() {
                    clearInterval(autoSlideInterval);
                    startAutoSlide();
                }

                if (nextBtn) {
                    nextBtn.addEventListener("click", () => {
                        goToSlide(currentSlide + 1);
                        resetAutoSlide();
                    });
                }
                if (prevBtn) {
                    prevBtn.addEventListener("click", () => {
                        goToSlide(currentSlide - 1);
                        resetAutoSlide();
                    });
                }

                window.addEventListener('resize', () => {
                    currentSlide = 0;
                    goToSlide(0);
                });
                startAutoSlide();
            }

            initPrestasiSlider();
        });
    </script>
@endpush
