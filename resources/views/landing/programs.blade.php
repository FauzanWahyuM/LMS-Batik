@extends('layouts.landing')

@section('title', 'Program - LMS Batik')

@section('content')

    <section class="relative bg-cover bg-center h-screen flex items-center justify-center"
        style="background-image: linear-gradient(to bottom, rgba(40, 25, 15, 0.3), rgba(40, 25, 15, 0.8)), url('{{ asset('img/Batik3.jpg') }}');">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-10">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-4 leading-tight tracking-wide drop-shadow-lg"
                style="font-family: 'Georgia', serif;">
                Program Kami
            </h1>
            <div class="w-20 h-1 bg-amber-500 mx-auto my-6 rounded-full opacity-90"></div>
            <p class="text-xl md:text-2xl text-gray-200 font-light tracking-widest drop-shadow-md"
                style="font-family: 'Georgia', serif;">
                LPK Kama Praja Madiun
            </p>
        </div>

    </section>

    <section class="bg-white py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-950 mb-4" style="font-family: 'Georgia', serif;">
                    Daftar Program
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Dapatkan informasi lengkap tentang program-program yang tersedia
                    di LPK Kama Praja Madiun.</p>
            </div>

            @php
                $programCollection = collect($programs ?? []);
                $programCount = $programCollection->count();
                $programPages = $programCollection->chunk(3);
            @endphp

            @if ($programCollection->isNotEmpty())
                <div id="program-pages-wrapper">
                    @foreach ($programPages as $pageIndex => $programPage)
                        @php
                            $pageCount = $programPage->count();
                            $isSingleProgram = $pageCount === 1;
                            $isTwoPrograms = $pageCount === 2;
                        @endphp

                        <div data-program-page="{{ $pageIndex + 1 }}" class="{{ $pageIndex === 0 ? '' : 'hidden' }}">
                            @if ($isSingleProgram)
                                <div class="flex justify-center">
                                    @foreach ($programPage as $program)
                                        <article
                                            class="w-full max-w-2xl bg-blue-950 rounded-xl shadow-xl p-8 text-white flex flex-col">
                                            <h3 class="text-2xl font-bold mb-4" style="font-family: 'Georgia', serif;">
                                                {{ $program->name }}</h3>

                                            <div class="space-y-2 text-sm text-gray-200 mb-4">
                                                <p><span class="font-semibold text-white">Durasi:</span>
                                                    {{ number_format((float) $program->duration_hours, 1, ',', '.') }} jam
                                                </p>
                                                <p><span class="font-semibold text-white">Biaya:</span> Rp
                                                    {{ number_format((float) $program->fee_amount, 0, ',', '.') }} /
                                                    {{ $program->fee_unit }}
                                                </p>
                                            </div>

                                            <p class="text-sm md:text-base leading-relaxed text-gray-100 mb-4">
                                                {{ $program->description }}</p>

                                            <div
                                                class="rounded-lg border border-blue-800 bg-blue-900/40 p-4 text-sm text-gray-100 mb-6">
                                                <p class="font-semibold text-white mb-2">Benefit Program</p>
                                                <ul class="space-y-1">
                                                    @foreach ((array) $program->benefits as $benefit)
                                                        <li class="flex items-start gap-2">
                                                            <span class="text-amber-400 shrink-0">★</span>
                                                            <span>{{ $benefit }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div class="mt-auto">
                                                <a href="{{ route('landing.registration') }}"
                                                    class="inline-block w-full sm:w-auto bg-amber-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-700 transition shadow-lg">
                                                    Daftar Sekarang
                                                </a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="{{ $isTwoPrograms ? 'grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' }}">
                                    @foreach ($programPage as $program)
                                        <article class="bg-blue-950 rounded-xl shadow-xl p-8 text-white flex flex-col">
                                            <h3 class="text-2xl font-bold mb-4" style="font-family: 'Georgia', serif;">
                                                {{ $program->name }}</h3>

                                            <div class="space-y-2 text-sm text-gray-200 mb-4">
                                                <p><span class="font-semibold text-white">Durasi:</span>
                                                    {{ number_format((float) $program->duration_hours, 1, ',', '.') }} jam
                                                </p>
                                                <p><span class="font-semibold text-white">Biaya:</span> Rp
                                                    {{ number_format((float) $program->fee_amount, 0, ',', '.') }} /
                                                    {{ $program->fee_unit }}
                                                </p>
                                            </div>

                                            <p class="text-sm md:text-base leading-relaxed text-gray-100 mb-4">
                                                {{ $program->description }}</p>

                                            <div
                                                class="rounded-lg border border-blue-800 bg-blue-900/40 p-4 text-sm text-gray-100 mb-6">
                                                <p class="font-semibold text-white mb-2">Benefit Program</p>
                                                <ul class="space-y-1">
                                                    @foreach ((array) $program->benefits as $benefit)
                                                        <li class="flex items-start gap-2">
                                                            <span class="text-amber-400 shrink-0">★</span>
                                                            <span>{{ $benefit }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                            <div class="mt-auto">
                                                <a href="{{ route('landing.registration') }}"
                                                    class="inline-block w-full sm:w-auto bg-amber-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-700 transition shadow-lg">
                                                    Daftar Sekarang
                                                </a>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
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
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-slate-600">
                    Program belum tersedia.
                </div>
            @endif
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-950 mb-4" style="font-family: 'Georgia', serif;">
                    Keunggulan Program Kami</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Berbagai fasilitas dan dukungan untuk pengalaman belajar
                    terbaik</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-50 border border-gray-100 p-6 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="bg-amber-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-7 w-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Sertifikat Resmi</h3>
                    <p class="text-gray-600 text-sm">Dapatkan sertifikat resmi setelah menyelesaikan program</p>
                </div>

                <div class="bg-gray-50 border border-gray-100 p-6 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="bg-blue-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-7 w-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pendaftaran Fleksibel</h3>
                    <p class="text-gray-600 text-sm">Dapat mendaftar secara individu ataupun kelompok dimana saja dan kapan
                        saja</p>
                </div>

                <div class="bg-gray-50 border border-gray-100 p-6 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="bg-green-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-7 w-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Bahan Lengkap</h3>
                    <p class="text-gray-600 text-sm">Semua bahan dan alat praktik disediakan dari lembaga</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-amber-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6" style="font-family: 'Georgia', serif;">Siap Memulai
                Perjalanan Membatik Anda?</h2>
            <p class="text-lg md:text-xl text-white mb-8 max-w-2xl mx-auto">
                Daftarkan diri Anda sekarang dan pelajari warisan budaya bangsa langsung dari ahlinya.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/pendaftaran"
                    class="bg-blue-900 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white hover:text-blue-900 transition shadow-lg">
                    Daftar Sekarang
                </a>
                <a href="{{ route('landing.gallery') }}"
                    class="border border-blue-900 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-blue-900 hover:text-white transition">
                    Lihat Galeri Karya
                </a>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const pagesWrapper = document.getElementById("program-pages-wrapper");
            const pagination = document.getElementById("program-pagination");

            if (!pagesWrapper || !pagination) return;

            const pages = Array.from(pagesWrapper.querySelectorAll("[data-program-page]"));
            if (pages.length <= 1) return;

            const prevButton = document.getElementById("program-prev-page");
            const nextButton = document.getElementById("program-next-page");
            const pageButtonsContainer = document.getElementById("program-page-buttons");

            if (!prevButton || !nextButton || !pageButtonsContainer) return;

            let currentPage = 1;
            const totalPages = pages.length;

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
                        updatePage();
                    });

                    pageButtonsContainer.appendChild(button);
                }
            }

            function updatePage() {
                pages.forEach((page, index) => {
                    page.classList.toggle("hidden", index + 1 !== currentPage);
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
                    updatePage();
                }
            });

            nextButton.addEventListener("click", () => {
                if (currentPage < totalPages) {
                    currentPage += 1;
                    updatePage();
                }
            });

            renderPageButtons();
            updatePage();
        });
    </script>
@endpush
