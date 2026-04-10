@extends('layouts.landing')

@section('title', 'Tentang Kami - LMS Batik')

@section('content')

    <section class="relative bg-cover bg-center h-screen flex items-center justify-center"
        style="background-image: linear-gradient(to bottom, rgba(40, 25, 15, 0.3), rgba(40, 25, 15, 0.8)), url('{{ asset('img/Batik1.jpg') }}');">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-10">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-4 leading-tight tracking-wide drop-shadow-lg"
                style="font-family: 'Georgia', serif;">
                Tentang Kami
            </h1>
            <div class="w-20 h-1 bg-amber-500 mx-auto my-6 rounded-full opacity-90"></div>
            <p class="text-xl md:text-2xl text-gray-200 font-light tracking-widest drop-shadow-md"
                style="font-family: 'Georgia', serif;">
                LPK Kama Praja Madiun
            </p>
        </div>

    </section>

    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-10" style="font-family: Georgia, serif;">
                Tentang Kami
            </h2>
            <p class="text-gray-700 leading-relaxed text-base md:text-lg">
                LPK Kama Praja Madiun adalah lembaga pelatihan yang berfokus pada penguatan keterampilan membatik secara
                terstruktur, praktis, dan relevan dengan kebutuhan industri kreatif. Kami menggabungkan nilai tradisi batik
                nusantara dengan pendekatan pembelajaran modern agar peserta mampu berkarya, berwirausaha, dan menjaga
                kelestarian budaya lokal secara berkelanjutan.
            </p>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-10">
                <div class="bg-white rounded-xl shadow-md p-10 text-center space-y-6">
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900" style="font-family: Georgia, serif;">
                        Visi
                    </h3>
                    <p class="text-gray-700 leading-relaxed">
                        Menjadi pusat pelatihan batik unggulan di Madiun yang mencetak pembatik kreatif, berdaya saing, dan
                        berkarakter budaya.
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-10 text-center space-y-6">
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900" style="font-family: Georgia, serif;">
                        Misi
                    </h3>
                    <p class="text-gray-700 leading-relaxed">
                        Menyelenggarakan pelatihan berbasis praktik, membangun ekosistem pembelajaran kolaboratif, dan
                        mendampingi peserta agar mampu menghasilkan karya batik berkualitas serta bernilai ekonomi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-14" style="font-family: Georgia, serif;">
                Fasilitas Kami
            </h2>

            <div class="relative max-w-5xl mx-auto">
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-in-out" id="facilities-slider">

                        <div class="min-w-full md:min-w-[33.333%] px-4">
                            <div
                                class="bg-gradient-to-br from-blue-950 to-blue-900 rounded-xl shadow-lg overflow-hidden h-full flex flex-col">
                                <img src="../../img/kainputih.jpg" alt="Kain Batik Polos" class="w-full h-48 object-cover">
                                <div class="p-6 text-center grow flex items-center justify-center">
                                    <h3 class="text-xl font-semibold text-white">Kain Batik Polos</h3>
                                </div>
                            </div>
                        </div>

                        <div class="min-w-full md:min-w-[33.333%] px-4">
                            <div
                                class="bg-gradient-to-br from-blue-950 to-blue-900 rounded-xl shadow-lg overflow-hidden h-full flex flex-col">
                                <img src="../../img/canting.jpg" alt="Canting Batik" class="w-full h-48 object-cover">
                                <div class="p-6 text-center grow flex items-center justify-center">
                                    <h3 class="text-xl font-semibold text-white">Canting Batik</h3>
                                </div>
                            </div>
                        </div>

                        <div class="min-w-full md:min-w-[33.333%] px-4">
                            <div
                                class="bg-gradient-to-br from-blue-950 to-blue-900 rounded-xl shadow-lg overflow-hidden h-full flex flex-col">
                                <img src="../../img/pewarnabatik.jpg" alt="Pewarna Batik" class="w-full h-48 object-cover">
                                <div class="p-6 text-center grow flex items-center justify-center">
                                    <h3 class="text-xl font-semibold text-white">Pewarna Batik</h3>
                                </div>
                            </div>
                        </div>

                        <div class="min-w-full md:min-w-[33.333%] px-4">
                            <div
                                class="bg-gradient-to-br from-blue-950 to-blue-900 rounded-xl shadow-lg overflow-hidden h-full flex flex-col">
                                <img src="../../img/malambatik.jpg" alt="Malam (Lilin Batik)"
                                    class="w-full h-48 object-cover">
                                <div class="p-6 text-center grow flex items-center justify-center">
                                    <h3 class="text-xl font-semibold text-white">Malam (Lilin Batik)</h3>
                                </div>
                            </div>
                        </div>

                        <div class="min-w-full md:min-w-[33.333%] px-4">
                            <div
                                class="bg-gradient-to-br from-blue-950 to-blue-900 rounded-xl shadow-lg overflow-hidden h-full flex flex-col">
                                <img src="../../img/wajankompor.jpg" alt="Wajan & Kompor" class="w-full h-48 object-cover">
                                <div class="p-6 text-center grow flex items-center justify-center">
                                    <h3 class="text-xl font-semibold text-white">Wajan & Kompor Kecil</h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <button id="prev-facility"
                    class="hidden md:flex absolute -left-12 top-1/2 -translate-y-1/2 bg-amber-600 hover:bg-amber-700 text-white p-2 rounded-full shadow-lg transition z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button id="next-facility"
                    class="hidden md:flex absolute -right-12 top-1/2 -translate-y-1/2 bg-amber-600 hover:bg-amber-700 text-white p-2 rounded-full shadow-lg transition z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div id="facilities-dots" class="flex justify-center mt-8 space-x-2"></div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-14" style="font-family: Georgia, serif;">
                Mitra Kami
            </h2>

            <div class="relative max-w-4xl mx-auto">
                <div class="overflow-hidden rounded-xl">
                    <div id="partners-slider" class="flex transition-transform duration-500 ease-in-out">

                        <div class="min-w-full px-4">
                            <div class="bg-blue-950 rounded-xl shadow-lg py-12 px-6 flex flex-col items-center">
                                <div
                                    class="w-32 h-32 bg-white rounded-full flex items-center justify-center mb-6 overflow-hidden">
                                    <img src="../../img/pemkotmadiun.png" alt="Pemerintah Kota Madiun"
                                        class="w-20 h-20 object-contain">
                                </div>
                                <p class="text-white text-lg font-semibold text-center">Pemerintah Kota Madiun</p>
                            </div>
                        </div>

                        <div class="min-w-full px-4">
                            <div class="bg-blue-950 rounded-xl shadow-lg py-12 px-6 flex flex-col items-center">
                                <div
                                    class="w-32 h-32 bg-white rounded-full flex items-center justify-center mb-6 overflow-hidden">
                                    <img src="../../img/dinasindustri.jpg" alt="Dinas Perindustrian"
                                        class="w-20 h-20 object-contain">
                                </div>
                                <p class="text-white text-lg font-semibold text-center">Dinas Perindustrian</p>
                            </div>
                        </div>

                        <div class="min-w-full px-4">
                            <div class="bg-blue-950 rounded-xl shadow-lg py-12 px-6 flex flex-col items-center">
                                <div
                                    class="w-32 h-32 bg-white rounded-full flex items-center justify-center mb-6 overflow-hidden">
                                    <img src="../../img/komunitasbatik.png" alt="Komunitas Batik Nusantara"
                                        class="w-20 h-20 object-contain">
                                </div>
                                <p class="text-white text-lg font-semibold text-center">Komunitas Batik Nusantara</p>
                            </div>
                        </div>

                    </div>
                </div>

                <button id="prev-partner"
                    class="hidden md:flex absolute -left-12 top-1/2 -translate-y-1/2 bg-amber-600 hover:bg-amber-700 text-white p-2 rounded-full shadow-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button id="next-partner"
                    class="hidden md:flex absolute -right-12 top-1/2 -translate-y-1/2 bg-amber-600 hover:bg-amber-700 text-white p-2 rounded-full shadow-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div id="partners-dots" class="flex justify-center mt-8 space-x-2"></div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // Fungsi Master Slider yang Reusable (bisa dipakai untuk fasilitas & mitra)
            function createSlider(sliderId, dotsContainerId, prevBtnId, nextBtnId, visibleDesktopItems = 3) {
                const slider = document.getElementById(sliderId);
                const dotsContainer = document.getElementById(dotsContainerId);
                const prevBtn = document.getElementById(prevBtnId);
                const nextBtn = document.getElementById(nextBtnId);

                if (!slider || !dotsContainer) {
                    return;
                }

                let current = 0;
                const slides = slider.children;
                const totalSlides = slides.length;
                let dots = [];

                function getVisibleItems() {
                    return window.innerWidth >= 768 ? visibleDesktopItems : 1;
                }

                function getMaxIndex() {
                    return Math.max(0, totalSlides - getVisibleItems());
                }

                function renderDots() {
                    const dotCount = getMaxIndex() + 1;
                    dotsContainer.innerHTML = '';

                    for (let i = 0; i < dotCount; i++) {
                        const dot = document.createElement('button');
                        dot.className =
                            `h-3 rounded-full transition-all duration-300 ${i === current ? 'bg-amber-500 w-8' : 'bg-gray-300 hover:bg-gray-400 w-3'}`;
                        dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                        dot.addEventListener('click', () => goToSlide(i));
                        dotsContainer.appendChild(dot);
                    }

                    dots = Array.from(dotsContainer.children);
                }

                // 2. Fungsi Eksekusi Pergeseran
                function goToSlide(index) {
                    const maxIndex = getMaxIndex();

                    // Logika Looping: jika melebihi batas, kembali ke 0. Jika kurang dari 0, ke batas akhir.
                    if (index < 0) {
                        current = maxIndex;
                    } else if (index > maxIndex) {
                        current = 0;
                    } else {
                        current = index;
                    }

                    // Hitung persentase pergeseran
                    const slidePercentage = 100 / getVisibleItems();
                    slider.style.transform = `translateX(-${current * slidePercentage}%)`;

                    // Update UI Dots
                    dots.forEach((dot, i) => {
                        if (i === current) {
                            dot.className =
                                'h-3 rounded-full transition-all duration-300 bg-amber-500 w-8'; // Dot aktif
                        } else {
                            dot.className =
                                'h-3 rounded-full transition-all duration-300 bg-gray-300 hover:bg-gray-400 w-3'; // Dot tidak aktif
                        }
                    });
                }

                // 3. Event Listeners
                if (nextBtn) nextBtn.addEventListener("click", () => goToSlide(current + 1));
                if (prevBtn) prevBtn.addEventListener("click", () => goToSlide(current - 1));

                // Pastikan slider tetap rapi saat ukuran layar (HP <-> Laptop) diubah
                window.addEventListener('resize', () => {
                    current = Math.min(current, getMaxIndex());
                    renderDots();
                    goToSlide(current);
                });

                renderDots();
                goToSlide(0);

                // 4. Auto Loop tiap 5 detik
                setInterval(() => {
                    goToSlide(current + 1);
                }, 5000);
            }

            // Inisialisasi:
            // Slider fasilitas tampil 3 di desktop
            createSlider("facilities-slider", "facilities-dots", "prev-facility", "next-facility", 3);

            // Slider mitra tampil 1 di desktop (karena masing-masing di set min-w-full)
            createSlider("partners-slider", "partners-dots", "prev-partner", "next-partner", 1);
        });
    </script>
@endpush
