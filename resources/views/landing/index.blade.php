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
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore
                        et dolore magna aliqua.
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
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore
                        et dolore magna aliqua.
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
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore
                        et dolore magna aliqua.
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
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore
                        et dolore magna aliqua.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12"
                style="font-family: 'Georgia', serif;">
                Program Kami
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <div class="relative rounded-lg overflow-hidden shadow-xl group">
                    <div class="h-64 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=600&h=400&fit=crop"
                            alt="Pelatihan Individu"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 bg-linear-to-t from-blue-950 via-blue-950/95 to-transparent p-6">
                        <h3 class="text-xl font-bold text-white mb-2">Pelatihan Individu</h3>
                        <p class="text-gray-200 text-sm mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed
                            do eiusmod tempor incididunt ut labore.</p>
                    </div>
                </div>

                <div class="relative rounded-lg overflow-hidden shadow-xl group">
                    <div class="h-64 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop"
                            alt="Pelatihan Kelompok"
                            class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 right-0 bg-linear-to-t from-blue-950 via-blue-950/95 to-transparent p-6">
                        <h3 class="text-xl font-bold text-white mb-2">Pelatihan Kelompok</h3>
                        <p class="text-gray-200 text-sm mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed
                            do eiusmod tempor incididunt ut labore.</p>
                    </div>
                </div>
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
                <div
                    class="bg-linear-to-br from-blue-950 to-blue-900 rounded-xl shadow-xl px-6 md:px-10 py-8 md:py-10 text-white">
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

    <section class="py-16 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900" style="font-family: 'Georgia', serif;">
                        Galeri Karya Peserta
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">Karya terbaru yang diunggah oleh peserta pelatihan.</p>
                </div>
                <a href="{{ route('landing.gallery') }}"
                    class="inline-flex items-center justify-center rounded-full bg-blue-950 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-900">
                    Lihat Semua Galeri
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse (($latestArtworks ?? collect()) as $artwork)
                    <article
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="h-56 overflow-hidden bg-slate-100">
                            <img src="{{ asset('storage/' . ltrim($artwork->image_path, '/')) }}"
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
                    dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
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

        });
    </script>
@endpush
