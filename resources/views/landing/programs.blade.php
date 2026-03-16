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

    <div class="bg-white py-16 relative z-10">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-blue-950" style="font-family: 'Georgia', serif;">
            Pilih Program
        </h2>
    </div>

        <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">

                <div class="bg-blue-950 rounded-xl shadow-xl p-8 md:p-10 text-white">
                    <h3 class="text-xl md:text-2xl font-bold mb-6" style="font-family: 'Georgia', serif;">
                        Program Individu
                    </h3>
                    <p class="text-sm md:text-base text-gray-200 leading-relaxed text-justify">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                        aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                        culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur
                        adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                        veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    </p>
                </div>

                <div class="bg-blue-950 rounded-xl shadow-xl p-8 md:p-10 text-white">
                    <h3 class="text-xl md:text-2xl font-bold mb-6" style="font-family: 'Georgia', serif;">
                        Program Kelompok
                    </h3>
                    <p class="text-sm md:text-base text-gray-200 leading-relaxed text-justify">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                        aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                        culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur
                        adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim
                        veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    </p>
                </div>

            </div>
        </section>


        <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <h2 class="text-3xl md:text-4xl font-bold text-center text-blue-950 mb-12"
                style="font-family: 'Georgia', serif;">
                Paket Program
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">

                <div class="bg-blue-950 rounded-xl shadow-xl p-8 md:p-10 text-white flex flex-col">
                    <h3 class="text-xl md:text-2xl font-bold mb-2" style="font-family: 'Georgia', serif;">Paket Individu
                    </h3>
                    <p class="text-sm text-gray-300 border-b border-gray-600 pb-4 mb-4">Durasi : 20 Hari</p>

                    <ul class="space-y-4 mb-10 flex-grow text-sm md:text-base">
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Lorem ipsum</span>
                        </li>
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Lorem ipsum</span>
                        </li>
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Lorem ipsum</span>
                        </li>
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Lorem ipsum</span>
                        </li>
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="leading-snug">Lorem ipsum dolor sit amet,<br>consectetur adipiscing elit</span>
                        </li>
                    </ul>

                    <div class="text-center mt-auto">
                        <p class="font-bold text-lg mb-6">Rp. xxx.xxx.xx / Orang</p>
                        <a href="/pendaftaran"
                            class="inline-block w-full sm:w-auto bg-amber-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-700 transition shadow-lg">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>

                <div class="bg-blue-950 rounded-xl shadow-xl p-8 md:p-10 text-white flex flex-col">
                    <h3 class="text-xl md:text-2xl font-bold mb-2" style="font-family: 'Georgia', serif;">Paket Kelompok
                    </h3>
                    <p class="text-sm text-gray-300 border-b border-gray-600 pb-4 mb-4">Durasi : Sesuai Pemesanan</p>

                    <ul class="space-y-4 mb-10 flex-grow text-sm md:text-base">
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Lorem ipsum</span>
                        </li>
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Lorem ipsum</span>
                        </li>
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Lorem ipsum</span>
                        </li>
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Lorem ipsum</span>
                        </li>
                        <li class="flex items-start border-b border-gray-600 pb-3">
                            <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="leading-snug">Lorem ipsum dolor sit amet,<br>consectetur adipiscing elit</span>
                        </li>
                    </ul>

                    <div class="text-center mt-auto">
                        <p class="font-bold text-lg mb-6">Rp. xxx.xxx.xx / Kelompok</p>
                        <a href="/pendaftaran"
                            class="inline-block w-full sm:w-auto bg-amber-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-700 transition shadow-lg">
                            Daftar Sekarang
                        </a>
                    </div>
                </div>

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
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Kelas Kecil</h3>
                    <p class="text-gray-600 text-sm">Maksimal 10 peserta per kelas untuk pembelajaran optimal</p>
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
