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
            @endphp

            @if ($programCount === 1)
                <div class="max-w-2xl mx-auto">
                    @foreach ($programCollection as $program)
                        <article
                            class="rounded-2xl border border-slate-200 bg-blue-950 shadow-lg p-8 transition hover:-translate-y-1 hover:shadow-xl">
                            <h3 class="text-2xl font-bold text-white mb-3" style="font-family: 'Georgia', serif;">
                                {{ $program->name }}
                            </h3>
                            <p class="text-sm text-slate-300 leading-relaxed mb-6">
                                {{ \Illuminate\Support\Str::limit($program->description, 180) }}
                            </p>
                            <a href="{{ route('landing.programs') }}"
                                class="inline-flex items-center rounded-full bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">
                                Lihat Program
                            </a>
                        </article>
                    @endforeach
                </div>
            @elseif ($programCount > 1)
                <div class="relative max-w-6xl mx-auto">
                    <div class="overflow-hidden">
                        <div id="program-slider" class="flex transition-transform duration-500 ease-in-out">
                            @foreach ($programCollection as $program)
                                <div class="min-w-full md:min-w-[50%] lg:min-w-[33.333%] shrink-0 px-3">
                                    <article
                                        class="h-full rounded-2xl border border-slate-200 bg-white shadow-lg p-8 transition hover:-translate-y-1 hover:shadow-xl">
                                        <div
                                            class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800 mb-4">
                                            Program
                                        </div>
                                        <h3 class="text-2xl font-bold text-slate-900 mb-3"
                                            style="font-family: 'Georgia', serif;">
                                            {{ $program->name }}
                                        </h3>
                                        <p class="text-sm text-slate-600 leading-relaxed mb-6">
                                            {{ \Illuminate\Support\Str::limit($program->description, 180) }}
                                        </p>
                                        <a href="{{ route('landing.programs') }}"
                                            class="inline-flex items-center rounded-full bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">
                                            Lihat Program
                                        </a>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button id="prev-program"
                        class="hidden md:flex absolute -left-5 top-1/2 -translate-y-1/2 bg-white text-blue-950 hover:bg-amber-500 hover:text-white border border-gray-200 p-3 rounded-full shadow-lg transition z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>
                    <button id="next-program"
                        class="hidden md:flex absolute -right-5 top-1/2 -translate-y-1/2 bg-white text-blue-950 hover:bg-amber-500 hover:text-white border border-gray-200 p-3 rounded-full shadow-lg transition z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <div id="program-dots" class="flex justify-center mt-6 space-x-2"></div>
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-600">
                    Program belum tersedia.
                </div>
            @endif
            <div class="sr-only" id="program-slider-meta" data-total-programs="{{ $programCount }}"></div>
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

                            <div class="min-w-full">
                                <div class="text-center space-y-4">
                                    <p class="text-sm md:text-lg italic leading-relaxed px-2">
                                        "Pengalaman belajar yang luar biasa! Instrukturnya sangat sabar dan materi yang
                                        diajarkan sangat lengkap. Sekarang saya sudah bisa membuat batik sendiri."
                                    </p>
                                    <div>
                                        <p class="font-semibold text-base text-amber-300">Rina Wijaya</p>
                                        <p class="text-xs md:text-sm text-gray-300">Peserta Program Batik Dasar</p>
                                    </div>
                                </div>
                            </div>

                            <div class="min-w-full">
                                <div class="text-center space-y-4">
                                    <p class="text-sm md:text-lg italic leading-relaxed px-2">
                                        "Platform yang sangat membantu untuk belajar batik. Jadwal kelasnya fleksibel dan
                                        bisa disesuaikan dengan waktu saya."
                                    </p>
                                    <div>
                                        <p class="font-semibold text-base text-amber-300">Ahmad Fauzi</p>
                                        <p class="text-xs md:text-sm text-gray-300">Peserta Program Batik Lanjutan</p>
                                    </div>
                                </div>
                            </div>

                            <div class="min-w-full">
                                <div class="text-center space-y-4">
                                    <p class="text-sm md:text-lg italic leading-relaxed px-2">
                                        "Saya sangat puas dengan program desain motif batik. Sekarang saya bisa membuat
                                        desain sendiri."
                                    </p>
                                    <div>
                                        <p class="font-semibold text-base text-amber-300">Siti Nurhaliza</p>
                                        <p class="text-xs md:text-sm text-gray-300">Peserta Program Desain Batik</p>
                                    </div>
                                </div>
                            </div>

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
                    }, 5000);
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

            const programSlider = document.getElementById('program-slider');
            const programDots = document.getElementById('program-dots');
            const prevProgramBtn = document.getElementById('prev-program');
            const nextProgramBtn = document.getElementById('next-program');

            if (programSlider && programDots) {
                let currentProgram = 0;
                const slides = programSlider.children;
                const totalPrograms = slides.length;

                if (totalPrograms > 1) {
                    programDots.innerHTML = '';

                    for (let index = 0; index < totalPrograms; index++) {
                        const dot = document.createElement('button');
                        dot.className = index === 0 ?
                            'h-3 w-8 rounded-full bg-amber-500 transition-all duration-300' :
                            'h-3 w-3 rounded-full bg-gray-300 transition-all duration-300 hover:bg-gray-400';
                        dot.setAttribute('aria-label', `Buka program ${index + 1}`);
                        dot.addEventListener('click', () => goToProgram(index));
                        programDots.appendChild(dot);
                    }

                    const dots = programDots.children;

                    function goToProgram(index) {
                        if (index < 0) {
                            currentProgram = totalPrograms - 1;
                        } else if (index >= totalPrograms) {
                            currentProgram = 0;
                        } else {
                            currentProgram = index;
                        }

                        programSlider.style.transform = `translateX(-${currentProgram * 100}%)`;

                        Array.from(dots).forEach((dot, dotIndex) => {
                            dot.className = dotIndex === currentProgram ?
                                'h-3 w-8 rounded-full bg-amber-500 transition-all duration-300' :
                                'h-3 w-3 rounded-full bg-gray-300 transition-all duration-300 hover:bg-gray-400';
                        });
                    }

                    if (prevProgramBtn) {
                        prevProgramBtn.addEventListener('click', () => goToProgram(currentProgram - 1));
                    }

                    if (nextProgramBtn) {
                        nextProgramBtn.addEventListener('click', () => goToProgram(currentProgram + 1));
                    }

                    window.addEventListener('resize', () => goToProgram(currentProgram));
                }
            }

        });
    </script>
@endpush
