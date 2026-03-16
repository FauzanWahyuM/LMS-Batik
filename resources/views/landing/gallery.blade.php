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

    <div class="bg-white py-20 relative z-10">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-blue-950" style="font-family: 'Georgia', serif;">
            Hasil Karya Peserta
        </h2>
    </div>

    <section>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div id="gallery-container"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10 min-h-[400px]">
            </div>

            <div class="flex justify-center mt-16">
                <div class="bg-amber-700 rounded-full px-4 py-2 flex items-center space-x-2 md:space-x-3 shadow-xl">

                    <button id="btn-prev-page"
                        class="w-8 h-8 flex items-center justify-center bg-blue-950 hover:bg-black rounded-full transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>

                    <div id="pagination-numbers" class="flex space-x-2"></div>

                    <button id="btn-next-page"
                        class="w-8 h-8 flex items-center justify-center bg-blue-950 hover:bg-black rounded-full transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                </div>
            </div>

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

                        <div class="min-w-full md:min-w-[50%] lg:min-w-[33.333%] flex-shrink-0 px-4">
                            <div
                                class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition h-full border border-gray-100">
                                <div
                                    class="bg-gradient-to-br from-yellow-400 to-yellow-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Juara 1</h3>
                                <p class="text-gray-600 mb-4 text-sm">Kompetisi Batik Nusantara 2025</p>
                                <p class="text-sm text-amber-600 font-semibold">Rina Wijaya - Batik Parang Modern</p>
                            </div>
                        </div>

                        <div class="min-w-full md:min-w-[50%] lg:min-w-[33.333%] flex-shrink-0 px-4">
                            <div
                                class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition h-full border border-gray-100">
                                <div
                                    class="bg-gradient-to-br from-gray-300 to-gray-500 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Juara 2</h3>
                                <p class="text-gray-600 mb-4 text-sm">Festival Batik Indonesia 2025</p>
                                <p class="text-sm text-amber-600 font-semibold">Ahmad Fauzi - Batik Kawung Fusion</p>
                            </div>
                        </div>

                        <div class="min-w-full md:min-w-[50%] lg:min-w-[33.333%] flex-shrink-0 px-4">
                            <div
                                class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition h-full border border-gray-100">
                                <div
                                    class="bg-gradient-to-br from-amber-600 to-amber-800 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Juara 3</h3>
                                <p class="text-gray-600 mb-4 text-sm">Pameran Batik Internasional 2024</p>
                                <p class="text-sm text-amber-600 font-semibold">Siti Nurhaliza - Batik Kontemporer</p>
                            </div>
                        </div>

                        <div class="min-w-full md:min-w-[50%] lg:min-w-[33.333%] flex-shrink-0 px-4">
                            <div
                                class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-xl transition h-full border border-gray-100">
                                <div
                                    class="bg-gradient-to-br from-blue-400 to-blue-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Karya Favorit</h3>
                                <p class="text-gray-600 mb-4 text-sm">Pekan Raya Daerah 2024</p>
                                <p class="text-sm text-amber-600 font-semibold">Budi Santoso - Batik Sekar Jagad</p>
                            </div>
                        </div>

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

    <section class="py-20 bg-gradient-to-r from-blue-950 to-blue-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6" style="font-family: 'Georgia', serif;">Ciptakan
                Karya Batik Anda Sendiri</h2>
            <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                Bergabunglah dengan program kami dan wujudkan karya batik impian Anda menjadi nyata.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/pendaftaran"
                    class="bg-amber-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-amber-700 transition shadow-lg">
                    Daftar Sekarang
                </a>
                <a href="{{ route('landing.programs') }}"
                    class="border border-gray-400 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white hover:text-blue-950 transition">
                    Lihat Program
                </a>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // ==========================================
            // 1. LOGIKA FRONTEND PAGINATION UNTUK GALERI
            // ==========================================

            // Dummy Data Galeri (9 Item agar bisa dibuat 3 halaman x 3 item)
            const galleryData = [{
                    title: "Mega Mendung",
                    img: "https://i.pinimg.com/736x/2f/bb/1d/2fbb1d58f262130c7d4e6e38fe1d5010.jpg",
                    desc: "Motif awan yang melambangkan kebebasan dan kehidupan dinamis.",
                    author: "Budi Santoso"
                },
                {
                    title: "Sekar Jagad",
                    img: "https://i.pinimg.com/1200x/18/7a/35/187a359f3677c88eb7249e8e82cc2f6c.jpg",
                    desc: "Menggambarkan keindahan dan keragaman bunga-bunga di dunia.",
                    author: "Rina Melati"
                },
                {
                    title: "Kawung Klasik",
                    img: "https://i.pinimg.com/736x/e6/ab/0b/e6ab0b79e97e6dec75e127b5ccfa6273.jpg",
                    desc: "Motif geometris tertua yang melambangkan kesucian.",
                    author: "Siti Aminah"
                },
                {
                    title: "Parang Rusak",
                    img: "https://i.pinimg.com/736x/de/d6/80/ded680eb81bfa76521b5eead9b60f95f.jpg",
                    desc: "Simbol ombak laut yang bermakna perjuangan tanpa lelah.",
                    author: "Andi Wijaya"
                },
                {
                    title: "Truntum",
                    img: "https://i.pinimg.com/1200x/00/9d/b0/009db02ea64eb4848f77900534477483.jpg",
                    desc: "Motif bintang yang bermakna cinta kasih yang tulus abadi.",
                    author: "Endang Lestari"
                },
                {
                    title: "Sido Mukti",
                    img: "https://i.pinimg.com/736x/66/1e/ff/661effc16992b3503e83158123518ab3.jpg",
                    desc: "Harapan akan kehidupan yang sejahtera di masa depan.",
                    author: "Joko Susilo"
                },
                {
                    title: "Batik Tujuh Rupa",
                    img: "https://i.pinimg.com/1200x/22/91/16/229116f9e51807aad7d280ffb781b766.jpg",
                    desc: "Perpaduan unik flora fauna pesisir utara.",
                    author: "Hasanuddin"
                },
                {
                    title: "Pring Sedapur",
                    img: "https://i.pinimg.com/736x/90/87/9c/90879c0f370291e1e2fd3f2b2d9caa7d.jpg",
                    desc: "Motif bambu yang menyimbolkan kekuatan dan ketahanan.",
                    author: "Sinta Maharani"
                },
                {
                    title: "Tambal",
                    img: "https://i.pinimg.com/736x/d2/82/5f/d2825f051a91dc6bf5039b77c8d1cc21.jpg",
                    desc: "Filosofi memperbaiki hal yang rusak menuju kesempurnaan.",
                    author: "Gatot P."
                },
                {
                    title: "Batik Ceplok",
                    img: "https://i.pinimg.com/1200x/3a/1e/a5/3a1ea5817b8a324e171368ec2ba4fd12.jpg",
                    desc: "pola geometris simetris, seperti lingkaran, segi empat, atau bintang, yang disusun berulang-ulang.",
                    author: "Husein Cahya"
                },
                {
                    title: "Batik Lasem",
                    img: "https://i.pinimg.com/1200x/df/f3/84/dff3845c56c984683f121b67aab4e8e1.jpg",
                    desc: "Motif hasil akulturasi budaya Jawa dan Tionghoa.",
                    author: "Sinta Dewi"
                },
                {
                    title: "Batik Betawi",
                    img: "https://i.pinimg.com/736x/9e/96/f2/9e96f2b9bd5c11810c9971f065f4aeeb.jpg",
                    desc: "Batik dengan warna-warna cerah (merah, kuning, biru, hijau) dan pengaruh budaya Tiongkok.",
                    author: "Purnomo."
                }
            ];

            const itemsPerPage = 6;
            let currentPage = 1;
            const totalPages = Math.ceil(galleryData.length / itemsPerPage);

            const container = document.getElementById('gallery-container');
            const paginationNumbers = document.getElementById('pagination-numbers');
            const btnPrev = document.getElementById('btn-prev-page');
            const btnNext = document.getElementById('btn-next-page');

            function renderGallery(page) {
                container.innerHTML = '';

                // Menentukan item mana yang tampil (Slice Array)
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const paginatedItems = galleryData.slice(start, end);

                paginatedItems.forEach(item => {
                    const cardHTML = `
                        <div class="group relative bg-blue-950 rounded-xl shadow-xl overflow-hidden cursor-pointer transition-shadow duration-300 hover:shadow-2xl">
                            <div class="aspect-[4/3] w-full overflow-hidden bg-gray-200">
                                <img src="${item.img}" alt="${item.title}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </div>
                            <div class="absolute inset-0 bg-blue-950/90 flex flex-col justify-center p-6 md:p-8 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <div class="flex-grow flex flex-col justify-center transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                    <h3 class="text-2xl font-bold text-white mb-3 text-center drop-shadow-md" style="font-family: 'Georgia', serif;">${item.title}</h3>
                                    <p class="text-sm text-gray-300 text-center leading-relaxed mb-4">${item.desc}</p>
                                </div>
                                <div class="flex justify-center mt-auto transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">
                                    <span class="bg-amber-600 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-lg">Karya: ${item.author}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', cardHTML);
                });

                updatePaginationUI();
            }

            function updatePaginationUI() {
                // Update Tombol Navigasi Bawah
                paginationNumbers.innerHTML = '';

                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement('button');
                    btn.innerText = i;

                    // Styling Aktif VS Tidak Aktif
                    if (i === currentPage) {
                        btn.className =
                            "w-8 h-8 flex items-center justify-center bg-blue-950 rounded-full text-white font-bold shadow-inner";
                    } else {
                        btn.className =
                            "w-8 h-8 flex items-center justify-center rounded-full text-white hover:bg-amber-600 transition font-semibold";
                    }

                    btn.addEventListener('click', () => {
                        currentPage = i;
                        renderGallery(currentPage);
                    });

                    paginationNumbers.appendChild(btn);
                }

                // Matikan panah jika di ujung
                btnPrev.disabled = currentPage === 1;
                btnNext.disabled = currentPage === totalPages;
            }

            // Event Listener Panah
            btnPrev.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderGallery(currentPage);
                }
            });
            btnNext.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderGallery(currentPage);
                }
            });

            // Initial Load Galeri
            renderGallery(currentPage);


            // ==========================================
            // 2. LOGIKA SLIDER UNTUK PRESTASI ALUMNI
            // ==========================================
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
