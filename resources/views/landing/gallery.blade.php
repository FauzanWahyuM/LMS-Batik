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

            @php
                $galleryItems = collect($gallery ?? []);
                $galleryCount = $galleryItems->count();
                $galleryPages = $galleryItems->chunk(3);
            @endphp

            @if ($galleryItems->isNotEmpty())
                <div id="gallery-pages-wrapper">
                    @foreach ($galleryPages as $pageIndex => $galleryPage)
                        @php
                            $pageCount = $galleryPage->count();
                            $isSingleItem = $pageCount === 1;
                            $isTwoItems = $pageCount === 2;
                        @endphp

                        <div data-gallery-page="{{ $pageIndex + 1 }}" class="{{ $pageIndex === 0 ? '' : 'hidden' }}">
                            @if ($isSingleItem)
                                <div class="max-w-xl mx-auto">
                                    @foreach ($galleryPage as $artwork)
                                        <article
                                            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                            <div class="h-56 overflow-hidden bg-slate-100">
                                                <img src="{{ route('public-file', ['path' => ltrim($artwork->image_path, '/')]) }}"
                                                    alt="{{ $artwork->title }}"
                                                    class="h-full w-full object-cover transition-transform duration-500 hover:scale-105">
                                            </div>
                                            <div class="p-4">
                                                <h3 class="text-lg font-bold text-slate-900"
                                                    style="font-family: 'Georgia', serif;">
                                                    {{ $artwork->title }}</h3>
                                                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                                                    {{ \Illuminate\Support\Str::limit($artwork->description, 120) }}
                                                </p>
                                                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-amber-700">
                                                    Karya:
                                                    {{ $artwork->creator_name }}</p>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="{{ $isTwoItems ? 'grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto' : 'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3' }}">
                                    @foreach ($galleryPage as $artwork)
                                        <article
                                            class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                            <div class="h-56 overflow-hidden bg-slate-100">
                                                <img src="{{ route('public-file', ['path' => ltrim($artwork->image_path, '/')]) }}"
                                                    alt="{{ $artwork->title }}"
                                                    class="h-full w-full object-cover transition-transform duration-500 hover:scale-105">
                                            </div>
                                            <div class="p-4">
                                                <h3 class="text-lg font-bold text-slate-900"
                                                    style="font-family: 'Georgia', serif;">
                                                    {{ $artwork->title }}</h3>
                                                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                                                    {{ \Illuminate\Support\Str::limit($artwork->description, 120) }}
                                                </p>
                                                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-amber-700">
                                                    Karya:
                                                    {{ $artwork->creator_name }}</p>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($galleryCount > 3)
                    <div id="gallery-pagination" class="mt-10 flex flex-wrap items-center justify-center gap-2">
                        <button type="button" id="gallery-prev-page"
                            class="px-4 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Sebelumnya
                        </button>

                        <div id="gallery-page-buttons" class="flex flex-wrap items-center justify-center gap-2"></div>

                        <button type="button" id="gallery-next-page"
                            class="px-4 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Berikutnya
                        </button>
                    </div>
                @endif
            @else
                <div
                    class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-600">
                    Belum ada karya peserta yang dipublikasikan.
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

            @php
                $achievementItems = collect($achievements ?? []);
                $achievementCount = $achievementItems->count();
                $achievementPages = $achievementItems->chunk(3);
            @endphp

            @if ($achievementItems->isNotEmpty())
                <div id="achievement-pages-wrapper">
                    @foreach ($achievementPages as $pageIndex => $achievementPage)
                        @php
                            $pageCount = $achievementPage->count();
                            $isSingleItem = $pageCount === 1;
                            $isTwoItems = $pageCount === 2;
                        @endphp

                        <div data-achievement-page="{{ $pageIndex + 1 }}"
                            class="{{ $pageIndex === 0 ? '' : 'hidden' }}">
                            @if ($isSingleItem)
                                <div class="max-w-2xl mx-auto">
                                    @foreach ($achievementPage as $achievement)
                                        @php
                                            $rank = $achievement->rank;
                                            $rankLabel = match ($rank) {
                                                1 => '1st Place',
                                                2 => '2nd Place',
                                                3 => '3rd Place',
                                                default => null,
                                            };
                                            $badgeClass = match ($rank) {
                                                1 => 'bg-yellow-100',
                                                2 => 'bg-slate-200',
                                                3 => 'bg-amber-200',
                                                default => 'bg-blue-100',
                                            };
                                            $rankClass = match ($rank) {
                                                1 => 'text-yellow-700',
                                                2 => 'text-slate-600',
                                                3 => 'text-amber-700',
                                                default => 'text-blue-700',
                                            };
                                        @endphp

                                        <article
                                            class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition h-full border border-gray-100">
                                            <div
                                                class="{{ $badgeClass }} min-h-16 px-4 py-2 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                                                <span class="text-sm font-bold {{ $rankClass }}">
                                                    {{ $rankLabel ?? 'Achievement' }}
                                                </span>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                                {{ $achievement->title }}
                                            </h3>
                                            <p class="text-gray-600 mb-2 text-sm">
                                                {{ $achievement->event_name }}{{ $achievement->year ? ' - ' . $achievement->year : '' }}
                                            </p>
                                            <p class="text-sm text-amber-600 font-semibold">{{ $achievement->winner_name }}</p>
                                            @if (!empty($achievement->description))
                                                <p class="mt-3 text-xs text-slate-500">
                                                    {{ \Illuminate\Support\Str::limit($achievement->description, 90) }}</p>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="{{ $isTwoItems ? 'grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto' : 'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3' }}">
                                    @foreach ($achievementPage as $achievement)
                                        @php
                                            $rank = $achievement->rank;
                                            $rankLabel = match ($rank) {
                                                1 => '1st Place',
                                                2 => '2nd Place',
                                                3 => '3rd Place',
                                                default => null,
                                            };
                                            $badgeClass = match ($rank) {
                                                1 => 'bg-yellow-100',
                                                2 => 'bg-slate-200',
                                                3 => 'bg-amber-200',
                                                default => 'bg-blue-100',
                                            };
                                            $rankClass = match ($rank) {
                                                1 => 'text-yellow-700',
                                                2 => 'text-slate-600',
                                                3 => 'text-amber-700',
                                                default => 'text-blue-700',
                                            };
                                        @endphp

                                        <article
                                            class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition h-full border border-gray-100">
                                            <div
                                                class="{{ $badgeClass }} min-h-16 px-4 py-2 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                                                <span class="text-sm font-bold {{ $rankClass }}">
                                                    {{ $rankLabel ?? 'Achievement' }}
                                                </span>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                                {{ $achievement->title }}
                                            </h3>
                                            <p class="text-gray-600 mb-2 text-sm">
                                                {{ $achievement->event_name }}{{ $achievement->year ? ' - ' . $achievement->year : '' }}
                                            </p>
                                            <p class="text-sm text-amber-600 font-semibold">{{ $achievement->winner_name }}</p>
                                            @if (!empty($achievement->description))
                                                <p class="mt-3 text-xs text-slate-500">
                                                    {{ \Illuminate\Support\Str::limit($achievement->description, 90) }}</p>
                                            @endif
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($achievementCount > 3)
                    <div id="achievement-pagination" class="mt-10 flex flex-wrap items-center justify-center gap-2">
                        <button type="button" id="achievement-prev-page"
                            class="px-4 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Sebelumnya
                        </button>

                        <div id="achievement-page-buttons" class="flex flex-wrap items-center justify-center gap-2"></div>

                        <button type="button" id="achievement-next-page"
                            class="px-4 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition disabled:opacity-40 disabled:cursor-not-allowed">
                            Berikutnya
                        </button>
                    </div>
                @endif
            @else
                <div
                    class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-600">
                    Data prestasi belum tersedia.
                </div>
            @endif

        </div>
        </div>
    </section>

    <section class="relative overflow-hidden py-20">
        <div class="absolute inset-0 bg-linear-to-br from-blue-950 via-blue-900 to-slate-900"></div>
        <div class="absolute -left-16 top-0 h-48 w-48 rounded-full bg-amber-500/20 blur-2xl"></div>
        <div class="absolute -right-10 bottom-0 h-52 w-52 rounded-full bg-sky-400/20 blur-2xl"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-blue-950 border border-white/10 p-8 md:p-12 text-center backdrop-blur-sm shadow-2xl">
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
            function initSectionPagination(config) {
                const pagesWrapper = document.getElementById(config.wrapperId);
                const pagination = document.getElementById(config.paginationId);

                if (!pagesWrapper || !pagination) return;

                const pages = Array.from(pagesWrapper.querySelectorAll(config.pageSelector));
                if (pages.length <= 1) return;

                const prevButton = document.getElementById(config.prevButtonId);
                const nextButton = document.getElementById(config.nextButtonId);
                const pageButtonsContainer = document.getElementById(config.pageButtonsId);

                if (!prevButton || !nextButton || !pageButtonsContainer) return;

                let currentPage = 1;
                const totalPages = pages.length;

                function renderButtons() {
                    pageButtonsContainer.innerHTML = "";

                    for (let page = 1; page <= totalPages; page++) {
                        const button = document.createElement("button");
                        button.type = "button";
                        button.textContent = page;
                        button.className =
                            "px-3 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition";

                        button.addEventListener("click", () => {
                            currentPage = page;
                            updatePage();
                        });

                        pageButtonsContainer.appendChild(button);
                    }
                }

                function updatePage() {
                    pages.forEach((page, index) => {
                        page.classList.toggle("hidden", index + 1 !== currentPage);
                    });

                    const pageButtons = pageButtonsContainer.querySelectorAll("button");
                    pageButtons.forEach((button, index) => {
                        const isActive = index + 1 === currentPage;
                        button.className = isActive ?
                            "px-3 py-2 rounded-full border border-amber-600 bg-amber-600 text-white transition" :
                            "px-3 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition";
                    });

                    prevButton.disabled = currentPage === 1;
                    nextButton.disabled = currentPage === totalPages;
                }

                prevButton.addEventListener("click", () => {
                    if (currentPage > 1) {
                        currentPage -= 1;
                        updatePage();
                    }
                });

                nextButton.addEventListener("click", () => {
                    if (currentPage < totalPages) {
                        currentPage += 1;
                        updatePage();
                    }
                });

                renderButtons();
                updatePage();
            }

            initSectionPagination({
                wrapperId: "gallery-pages-wrapper",
                paginationId: "gallery-pagination",
                pageSelector: "[data-gallery-page]",
                prevButtonId: "gallery-prev-page",
                nextButtonId: "gallery-next-page",
                pageButtonsId: "gallery-page-buttons",
            });

            initSectionPagination({
                wrapperId: "achievement-pages-wrapper",
                paginationId: "achievement-pagination",
                pageSelector: "[data-achievement-page]",
                prevButtonId: "achievement-prev-page",
                nextButtonId: "achievement-next-page",
                pageButtonsId: "achievement-page-buttons",
            });
        });
    </script>
@endpush
