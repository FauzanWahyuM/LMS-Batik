@extends('layouts.landing')

@section('title', 'Pendaftaran - LMS Batik')

@push('styles')
    <style>
        /* Latar belakang kontainer tab (dibuat lebih gelap dari form agar kontras) */
        .tab-slider-bg {
            background-color: #78350f;
            /* amber-900 */
        }

        /* Tombol tab yang aktif warnanya sama dengan warna form */
        .tab-btn-active {
            background-color: #92400e;
            /* amber-800 */
            color: #fff;
        }

        /* Tombol tab yang tidak aktif */
        .tab-btn-inactive {
            background-color: transparent;
            color: #d1d5db;
            /* gray-300 */
        }

        /* Form dibuat sewarna dengan tab yang aktif dan tanpa garis tepi (menyatu) */
        .form-card-bg {
            background-color: #92400e;
            /* amber-800 */
        }

        .slider-track {
            display: flex;
            align-items: flex-start;
            /* Mencegah form yang pendek ikut memanjang */
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform, height;
        }

        .slide-panel {
            min-width: 100%;
            flex-shrink: 0;
            overflow-x: hidden;
        }

        @media (max-width: 767px) {
            #panel-kelompok>.p-8 {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .kelompok-form-mobile-center {
                width: calc(100% - 0.5rem);
                max-width: 36rem;
                margin-left: auto;
                margin-right: auto;
                padding-left: 0.25rem;
                padding-right: 0.25rem;
            }
        }
    </style>
@endpush

@section('content')

    {{-- Hero Section --}}
    <section class="relative bg-cover bg-center flex items-center justify-center"
        style="min-height: 100vh; background-image: linear-gradient(to bottom, rgba(20, 15, 40, 0.55), rgba(20, 15, 40, 0.85)), url('{{ asset('img/Batik5.jpg') }}');">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-20 mt-4">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight tracking-wide drop-shadow-lg"
                style="font-family: 'Georgia', serif;">
                Pendaftaran<br />LPK Kama Praja Madiun
            </h1>
            <div class="w-20 h-1 bg-amber-500 mx-auto mt-6 rounded-full opacity-90"></div>
        </div>
    </section>

    {{-- Registration Form Section --}}
    @if ($errors->any())
        <div class="fixed top-20 left-1/2 z-50 w-full max-w-lg -translate-x-1/2 px-4" id="flash-errors">
            <div class="rounded-lg border border-rose-300 bg-rose-50 px-5 py-4 shadow-lg">
                <p class="text-sm font-semibold text-rose-800">Pendaftaran gagal disimpan</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <script>
            setTimeout(function() {
                var el = document.getElementById('flash-errors');
                if (el) el.remove();
            }, 7000);
        </script>
    @endif

    @if (session('success'))
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 px-4"
            id="registration-success-modal">
            <div class="w-full max-w-md rounded-2xl border border-emerald-200 bg-white p-6 shadow-2xl">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                    <svg class="h-7 w-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="mt-4 text-center text-xl font-bold text-slate-900">Pendaftaran Berhasil</h2>
                <p class="mt-2 text-center text-sm leading-6 text-slate-600">{{ session('success') }}</p>
                <div class="mt-6 flex justify-center">
                    <button type="button" onclick="closeRegistrationSuccessModal()"
                        class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    closeRegistrationSuccessModal();
                }, 7000);
            });

            function closeRegistrationSuccessModal() {
                var modal = document.getElementById('registration-success-modal');
                if (modal) {
                    modal.remove();
                }
            }
        </script>
    @endif

    <section class="py-16 bg-blue-900">
        {{-- Mengubah max-w-2xl menjadi max-w-4xl agar form lebih lebar --}}
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Wrapper Utama --}}
            <div class="rounded-xl shadow-2xl overflow-hidden flex flex-col">

                {{-- Tab Switcher --}}
                <div class="tab-slider-bg p-1.5 flex z-10">
                    <button id="tab-individu" onclick="switchTab('individu')"
                        class="flex-1 py-3 px-4 rounded-md text-sm font-semibold transition-all duration-300 tab-btn-active"
                        style="font-family: 'Poppins', sans-serif;">
                        Program Individu
                    </button>
                    <button id="tab-kelompok" onclick="switchTab('kelompok')"
                        class="flex-1 py-3 px-4 rounded-md text-sm font-semibold transition-all duration-300 tab-btn-inactive"
                        style="font-family: 'Poppins', sans-serif;">
                        Program Kelompok
                    </button>
                </div>

                {{-- Slider Container --}}
                <div class="overflow-hidden form-card-bg">
                    <div class="slider-track" id="slider-track">

                        {{-- Slide 1: Individu (Tambahkan ID panel-individu) --}}
                        <div class="slide-panel" id="panel-individu">
                            <div class="p-8 md:p-10">
                                <form action="{{ route('landing.registration.individu') }}" method="POST"
                                    id="form-individu">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-white mb-1">Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap Anda"
                                                class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-white mb-1">Email</label>
                                            <input type="email" name="email" placeholder="email@contoh.com"
                                                class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-white mb-1">No Handphone</label>
                                            <input type="tel" name="no_handphone" placeholder="08xxxxxxxxxx"
                                                class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-white mb-1">Alamat</label>
                                            <input type="text" name="alamat" placeholder="Alamat Lengkap Anda"
                                                class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-white mb-1">Pendidikan
                                                Terakhir</label>
                                            <input type="text" name="pendidikan_terakhir"
                                                placeholder="Contoh: SMA, D3, S1"
                                                class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-white mb-1">Motivasi Singkat Anda
                                                Mengikuti Program!</label>
                                            <textarea name="motivasi" rows="4" placeholder="Tuliskan motivasi singkat Anda..."
                                                class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition resize-none"
                                                required></textarea>
                                        </div>
                                        <div class="md:col-span-2 pt-2">
                                            <button type="submit"
                                                class="w-full bg-amber-500 hover:bg-amber-400 text-amber-950 font-bold py-3 rounded-md transition duration-300 text-sm tracking-wide shadow-md">
                                                Daftar Sekarang
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Slide 2: Kelompok (Tambahkan ID panel-kelompok) --}}
                        <div class="slide-panel" id="panel-kelompok">
                            <div class="p-8 md:p-10">
                                <div class="kelompok-form-mobile-center">
                                    <form action="{{ route('landing.registration.kelompok') }}" method="POST"
                                        enctype="multipart/form-data" id="form-kelompok">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-white mb-1">Nama
                                                    Lembaga/Kelompok</label>
                                                <input type="text" name="nama_lembaga"
                                                    placeholder="Nama Lembaga atau Kelompok"
                                                    class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                    required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-white mb-1">Alamat PIC</label>
                                                <input type="text" name="alamat_pic" placeholder="Alamat Lengkap PIC"
                                                    class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-white mb-1">Email PIC</label>
                                                <input type="email" name="email_pic" placeholder="email@lembaga.com"
                                                    class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-white mb-1">No. Handphone
                                                    PIC</label>
                                                <input type="tel" name="no_handphone_pic"
                                                    placeholder="08xxxxxxxxxx | No. Handphone PIC"
                                                    class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-white mb-1">Nama PIC</label>
                                                <input type="text" name="nama_pic"
                                                    placeholder="Nama PIC (Person in Charge)"
                                                    class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-white mb-1">Jumlah
                                                    Peserta</label>
                                                <input type="number" name="jumlah_peserta" placeholder="Jumlah Peserta"
                                                    min="1"
                                                    class="w-full border border-gray-300 text-gray-900 rounded-md px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition"
                                                    required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-white mb-1">Upload surat resmi
                                                    <span class="text-gray-300 font-normal">(opsional!)</span></label>
                                                <div class="border-2 border-dashed border-white/50 bg-white rounded-md p-6 text-center hover:border-amber-400 transition cursor-pointer relative"
                                                    id="drop-zone"
                                                    ondragover="event.preventDefault(); this.classList.add('border-amber-500','bg-amber-50')"
                                                    ondragleave="this.classList.remove('border-amber-500','bg-amber-50')"
                                                    ondrop="handleDrop(event)">
                                                    <input type="file" name="surat_resmi" id="surat-input"
                                                        accept=".pdf,.jpg,.jpeg,.png"
                                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                        onchange="handleFileChange(this)">
                                                    <div id="upload-placeholder">
                                                        <svg class="mx-auto h-10 w-10 text-gray-400 mb-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                        </svg>
                                                        <p class="text-xs text-gray-500">Seret & lepas file di sini, atau
                                                            klik
                                                            untuk memilih</p>
                                                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, PNG (maks. 5MB)</p>
                                                    </div>
                                                    <div id="upload-preview"
                                                        class="hidden items-center justify-center space-x-2">
                                                        <svg class="h-5 w-5 text-amber-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span id="file-name"
                                                            class="text-sm text-gray-700 truncate max-w-xs"></span>
                                                        <button type="button" onclick="clearFile()"
                                                            class="text-red-500 hover:text-red-700 ml-1 text-xs font-bold">Hapus</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="md:col-span-2 pt-2">
                                                <button type="submit"
                                                    class="w-full bg-amber-500 hover:bg-amber-400 text-amber-950 font-bold py-3 rounded-md transition duration-300 text-sm tracking-wide shadow-md">
                                                    Daftar Sekarang
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end slider-track --}}
                </div>{{-- end overflow-hidden --}}

            </div>{{-- end main wrapper --}}
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        let currentTab = 'individu';

        // Fungsi untuk menyesuaikan tinggi kotak agar tidak ada "space" kosong
        function updateHeight(tab) {
            const track = document.getElementById('slider-track');
            const panel = document.getElementById(tab === 'individu' ? 'panel-individu' : 'panel-kelompok');
            track.style.height = panel.offsetHeight + 'px';
        }

        function switchTab(tab) {
            currentTab = tab;
            const track = document.getElementById('slider-track');
            const btnIndividu = document.getElementById('tab-individu');
            const btnKelompok = document.getElementById('tab-kelompok');

            if (tab === 'individu') {
                track.style.transform = 'translateX(0%)';
                btnIndividu.classList.add('tab-btn-active');
                btnIndividu.classList.remove('tab-btn-inactive');
                btnKelompok.classList.add('tab-btn-inactive');
                btnKelompok.classList.remove('tab-btn-active');
            } else {
                track.style.transform = 'translateX(-100%)';
                btnKelompok.classList.add('tab-btn-active');
                btnKelompok.classList.remove('tab-btn-inactive');
                btnIndividu.classList.add('tab-btn-inactive');
                btnIndividu.classList.remove('tab-btn-active');
            }

            // Update tingginya setiap kali ganti tab
            updateHeight(tab);
        }

        // Jalankan penyesuaian tinggi saat halaman pertama kali diload
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => updateHeight('individu'), 50);
        });

        // Sesuaikan kembali tingginya jika layar diubah ukurannya (responsive)
        window.addEventListener('resize', () => {
            updateHeight(currentTab);
        });

        function handleFileChange(input) {
            if (input.files && input.files[0]) {
                showFilePreview(input.files[0].name);
            }
        }

        function handleDrop(event) {
            event.preventDefault();
            const dropZone = document.getElementById('drop-zone');
            dropZone.classList.remove('border-amber-500', 'bg-amber-50');
            const files = event.dataTransfer.files;
            if (files && files[0]) {
                const input = document.getElementById('surat-input');
                const dt = new DataTransfer();
                dt.items.add(files[0]);
                input.files = dt.files;
                showFilePreview(files[0].name);
            }
        }

        function showFilePreview(name) {
            document.getElementById('upload-placeholder').classList.add('hidden');
            const preview = document.getElementById('upload-preview');
            preview.classList.remove('hidden');
            preview.classList.add('flex');
            document.getElementById('file-name').textContent = name;
        }

        function clearFile() {
            document.getElementById('surat-input').value = '';
            document.getElementById('upload-preview').classList.add('hidden');
            document.getElementById('upload-preview').classList.remove('flex');
            document.getElementById('upload-placeholder').classList.remove('hidden');
        }
    </script>
@endpush
