@extends('layouts.landing')

@section('title', 'Beranda - LMS Batik')

@section('content')

    <section class="relative bg-cover bg-center h-screen flex items-center justify-center"
        style="background-image: linear-gradient(to bottom, rgba(40, 25, 15, 0.3), rgba(40, 25, 15, 0.8)), url('{{ asset('img/Batik2.jpg') }}');">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-10">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-tight tracking-wide drop-shadow-lg"
                style="font-family: 'Georgia', serif;">
                Selamat Datang di<br />
                LPK Kama Praja Madiun
            </h1>
            <div class="w-24 h-1 bg-amber-500 mx-auto mt-8 rounded-full opacity-90"></div>
        </div>

    </section>

    <section class="py-6 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-2" style="font-family: 'Georgia', serif;">
                Mengapa Belajar Membatik?
            </h2>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="flex flex-col items-center text-center">
                    <div
                        class="w-32 h-32 md:w-40 md:h-40 rounded-full flex items-center justify-center mb-4 shadow-lg relative overflow-hidden">
                        <div class="absolute inset-0 bg-center bg-cover scale-125"
                            style="background-image: url('{{ asset('img/Motif1.jpg') }}');">
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed px-2">
                        Batik mengajarkan ketelitian, kesabaran, dan karakter berkarya yang kuat dari proses mencanting
                        hingga
                        pewarnaan akhir.
                    </p>
                </div>

                <div class="flex flex-col items-center text-center">
                    <div
                        class="w-32 h-32 md:w-40 md:h-40 rounded-full flex items-center justify-center mb-4 shadow-lg relative overflow-hidden">
                        <div class="absolute inset-0 bg-center bg-cover scale-125"
                            style="background-image: url('{{ asset('img/Motif2.jpg') }}');">
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed px-2">
                        Setiap motif batik memuat cerita budaya lokal yang memperkaya wawasan sekaligus menumbuhkan rasa
                        cinta
                        pada warisan bangsa.
                    </p>
                </div>

                <div class="flex flex-col items-center text-center">
                    <div
                        class="w-32 h-32 md:w-40 md:h-40 rounded-full flex items-center justify-center mb-4 shadow-lg relative overflow-hidden">
                        <div class="absolute inset-0 bg-center bg-cover scale-125"
                            style="background-image: url('{{ asset('img/Motif3.jpg') }}');">
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed px-2">
                        Keterampilan membatik membuka peluang wirausaha kreatif melalui produk fesyen, dekorasi, dan karya
                        seni bernilai ekonomi.
                    </p>
                </div>

                <div class="flex flex-col items-center text-center">
                    <div
                        class="w-32 h-32 md:w-40 md:h-40 rounded-full flex items-center justify-center mb-4 shadow-lg relative overflow-hidden">
                        <div class="absolute inset-0 bg-center bg-cover scale-125"
                            style="background-image: url('{{ asset('img/Motif4.jpg') }}');">
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 leading-relaxed px-2">
                        Pembelajaran batik melatih kolaborasi dan inovasi, sehingga peserta siap berkembang di industri
                        kreatif
                        berbasis budaya.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-3"
                style="font-family: 'Georgia', serif;">
                Daftar Program
            </h2>
            <p class="text-center text-gray-600 text-sm md:text-base mb-12 max-w-2xl mx-auto">
                Pilih program yang paling sesuai dan mulai perjalanan membatik Anda bersama kami.
            </p>

            @php
                $programCollection = $programs ?? collect();
                $programCount = $programCollection->count();
                $isSingleProgram = $programCount === 1;
                $isTwoPrograms = $programCount === 2;
            @endphp

            @if ($programCount > 0)
                @if ($isSingleProgram)
                    <div class="flex justify-center">
                        @foreach ($programCollection as $program)
                            <article class="w-full max-w-2xl bg-blue-950 rounded-xl shadow-xl p-8 text-white flex flex-col">
                                <h3 class="text-2xl font-bold mb-4" style="font-family: 'Georgia', serif;">
                                    {{ $program->name }}</h3>

                                <p class="text-sm md:text-base leading-relaxed text-gray-100 mb-6">
                                    {{ $program->description }}</p>

                                <div class="mt-auto">
                                    <a href="{{ route('landing.programs') }}"
                                        class="inline-block w-full sm:w-auto bg-amber-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-700 transition shadow-lg">
                                        Lihat Program
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div
                        id="program-grid"
                        class="{{ $isTwoPrograms ? 'grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' }}">
                        @foreach ($programCollection as $program)
                            @php
                                $positionClass = '';

                                if ($programCount > 2) {
                                    $remainder = $programCount % 3;

                                    if ($remainder === 1 && $loop->last) {
                                        $positionClass = 'lg:col-start-2';
                                    }

                                    if ($remainder === 2 && $loop->iteration === $programCount - 1) {
                                        $positionClass = 'lg:col-start-1';
                                    }

                                    if ($remainder === 2 && $loop->last) {
                                        $positionClass = 'lg:col-start-3';
                                    }
                                }
                            @endphp

                            <article data-program-card data-page="{{ (int) ceil($loop->iteration / 3) }}"
                                class="bg-blue-950 rounded-xl shadow-xl p-8 text-white flex flex-col {{ $positionClass }}">
                                <h3 class="text-2xl font-bold mb-4" style="font-family: 'Georgia', serif;">
                                    {{ $program->name }}</h3>

                                <p class="text-sm md:text-base leading-relaxed text-gray-100 mb-6">
                                    {{ $program->description }}</p>

                                <div class="mt-auto">
                                    <a href="{{ route('landing.programs') }}"
                                        class="inline-block w-full sm:w-auto bg-amber-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-700 transition shadow-lg">
                                        Lihat Program
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($programCount > 3)
                        <div id="program-pagination" class="mt-10 flex flex-wrap items-center justify-center gap-2">
                            <button type="button" id="program-prev-page"
                                class="px-4 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition disabled:opacity-40 disabled:cursor-not-allowed">
                                Sebelumnya
                            </button>

                            <div id="program-page-buttons" class="flex flex-wrap items-center justify-center gap-2"></div>

                            <button type="button" id="program-next-page"
                                class="px-4 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition disabled:opacity-40 disabled:cursor-not-allowed">
                                Berikutnya
                            </button>
                        </div>
                    @endif
                @endif
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-600">
                    Program belum tersedia.
                </div>
            @endif
        </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">

            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900" style="font-family: Georgia, serif;">
                    Testimoni
                </h2>
                <p class="mt-2 text-gray-600 text-sm md:text-base max-w-md mx-auto">
                    Pengalaman peserta setelah mengikuti pelatihan di LPK Kama Praja Madiun
                </p>
            </div>

            <div class="relative max-w-2xl mx-auto">
                <div class="bg-blue-950 rounded-xl shadow-xl px-6 md:px-10 py-8 md:py-10 text-white">
                    <div class="overflow-hidden">
                        <div id="testimonial-slider" class="flex transition-transform duration-500 ease-in-out">
                            @forelse (($testimonials ?? collect()) as $testimonial)
                                <div class="min-w-full">
                                    <div class="text-center space-y-4">
                                        <p class="text-sm md:text-lg italic leading-relaxed px-2">
                                            "{{ $testimonial->quote }}"
                                        </p>
                                        <div>
                                            <p class="font-semibold text-base text-amber-300">{{ $testimonial->name }}</p>
                                            <p class="text-xs md:text-sm text-gray-300">
                                                {{ $testimonial->role_label ?: 'Peserta LPK Kama Praja' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="min-w-full">
                                    <div class="text-center space-y-4">
                                        <p class="text-sm md:text-lg italic leading-relaxed px-2">
                                            "Belum ada testimoni yang ditampilkan saat ini."
                                        </p>
                                        <div>
                                            <p class="font-semibold text-base text-amber-300">LPK Kama Praja Madiun</p>
                                            <p class="text-xs md:text-sm text-gray-300">Tim Pengelola</p>
                                        </div>
                                    </div>
                                </div>
                            @endforelse

                        </div>
                    </div>

                    <div id="testimonial-dots" class="flex justify-center mt-6 space-x-2"></div>
                </div>

                <button id="prev-testimonial"
                    class="hidden md:flex absolute -left-12 top-1/2 -translate-y-1/2 bg-amber-600 hover:bg-amber-700 text-white p-2 rounded-full shadow-lg z-10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button id="next-testimonial"
                    class="hidden md:flex absolute -right-12 top-1/2 -translate-y-1/2 bg-amber-600 hover:bg-amber-700 text-white p-2 rounded-full shadow-lg z-10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // Fungsi Slider yang Sama dan Reusable
            function createSlider(sliderId, dotsContainerId, prevBtnId, nextBtnId, visibleDesktopItems = 1) {
                const slider = document.getElementById(sliderId);
                const dotsContainer = document.getElementById(dotsContainerId);
                const prevBtn = document.getElementById(prevBtnId);
                const nextBtn = document.getElementById(nextBtnId);

                if (!slider || !dotsContainer) return; // Mencegah error jika elemen tidak ada

                let current = 0;
                const slides = slider.children;
                const totalSlides = slides.length;
                let autoSlideInterval;

                // 1. Generate Dots Otomatis
                dotsContainer.innerHTML = '';
                for (let i = 0; i < totalSlides; i++) {
                    const dot = document.createElement('button');
                    dot.className =
                        `h-3 rounded-full transition-all duration-300 ${i === 0 ? 'bg-amber-500 w-8' : 'bg-gray-300 hover:bg-gray-400 w-3'}`;
                    dot.setAttribute('aria-label', `Buka slide ${i + 1}`);
                    dot.addEventListener('click', () => {
                        goToSlide(i);
                        resetAutoSlide(); // Reset timer saat diklik manual
                    });
                    dotsContainer.appendChild(dot);
                }
                const dots = dotsContainer.children;

                // 2. Fungsi Geser Slide
                function goToSlide(index) {
                    const isDesktop = window.innerWidth >= 768;
                    const visibleItems = isDesktop ? visibleDesktopItems : 1;

                    if (index < 0) {
                        current = totalSlides - 1;
                    } else if (index >= totalSlides) {
                        current = 0;
                    } else {
                        current = index;
                    }

                    let maxIndex = totalSlides - visibleItems;
                    if (maxIndex < 0) maxIndex = 0;

                    let slidePosition = current;
                    if (slidePosition > maxIndex && isDesktop) {
                        slidePosition = maxIndex;
                    }

                    const slidePercentage = isDesktop ? (100 / visibleDesktopItems) : 100;
                    slider.style.transform = `translateX(-${slidePosition * slidePercentage}%)`;

                    // Update UI Dots
                    Array.from(dots).forEach((dot, i) => {
                        if (i === current) {
                            dot.className = 'h-3 rounded-full transition-all duration-300 bg-amber-500 w-8';
                        } else {
                            dot.className =
                                'h-3 rounded-full transition-all duration-300 bg-gray-300 hover:bg-gray-400 w-3';
                        }
                    });
                }

                // 3. Fungsi Auto Slide
                function startAutoSlide() {
                    autoSlideInterval = setInterval(() => {
                        goToSlide(current + 1);
                    }, 30000);
                }

                function resetAutoSlide() {
                    clearInterval(autoSlideInterval);
                    startAutoSlide();
                }

                // 4. Event Listeners Buttons
                if (nextBtn) {
                    nextBtn.addEventListener("click", () => {
                        goToSlide(current + 1);
                        resetAutoSlide();
                    });
                }

                if (prevBtn) {
                    prevBtn.addEventListener("click", () => {
                        goToSlide(current - 1);
                        resetAutoSlide();
                    });
                }

                // Pastikan layout tidak rusak saat layar di-resize
                window.addEventListener('resize', () => goToSlide(current));

                // Mulai Auto Slide
                startAutoSlide();
            }

            // Inisialisasi Slider Testimoni (tampil 1 per slide baik di HP maupun Laptop)
            createSlider("testimonial-slider", "testimonial-dots", "prev-testimonial", "next-testimonial", 1);

            function initProgramPagination() {
                const grid = document.getElementById("program-grid");
                const pagination = document.getElementById("program-pagination");

                if (!grid || !pagination) return;

                const cards = Array.from(grid.querySelectorAll("[data-program-card]"));
                if (cards.length <= 3) return;

                const pageButtonsContainer = document.getElementById("program-page-buttons");
                const prevButton = document.getElementById("program-prev-page");
                const nextButton = document.getElementById("program-next-page");

                if (!pageButtonsContainer || !prevButton || !nextButton) return;

                const totalPages = Math.ceil(cards.length / 3);
                let currentPage = 1;

                function renderPageButtons() {
                    pageButtonsContainer.innerHTML = "";

                    for (let page = 1; page <= totalPages; page++) {
                        const button = document.createElement("button");
                        button.type = "button";
                        button.textContent = page;
                        button.className =
                            "program-page-btn px-3 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition";

                        button.addEventListener("click", () => {
                            currentPage = page;
                            updateProgramPage();
                        });

                        pageButtonsContainer.appendChild(button);
                    }
                }

                function updateProgramPage() {
                    cards.forEach((card) => {
                        const page = Number(card.getAttribute("data-page"));
                        card.classList.toggle("hidden", page !== currentPage);
                    });

                    const pageButtons = pageButtonsContainer.querySelectorAll(".program-page-btn");
                    pageButtons.forEach((button, index) => {
                        const isActive = index + 1 === currentPage;
                        button.className = isActive ?
                            "program-page-btn px-3 py-2 rounded-full border border-amber-600 bg-amber-600 text-white transition" :
                            "program-page-btn px-3 py-2 rounded-full border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition";
                    });

                    prevButton.disabled = currentPage === 1;
                    nextButton.disabled = currentPage === totalPages;
                }

                prevButton.addEventListener("click", () => {
                    if (currentPage > 1) {
                        currentPage -= 1;
                        updateProgramPage();
                    }
                });

                nextButton.addEventListener("click", () => {
                    if (currentPage < totalPages) {
                        currentPage += 1;
                        updateProgramPage();
                    }
                });

                renderPageButtons();
                updateProgramPage();
            }

            initProgramPagination();

        });
    </script>
@endpush
