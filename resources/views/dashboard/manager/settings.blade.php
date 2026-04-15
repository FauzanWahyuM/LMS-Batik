@extends('dashboard.layouts.app')

@section('dashboard-content')
    <!-- Organization Settings -->
    <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:mb-6 sm:p-5">
        <h2 class="text-base font-bold text-slate-900 sm:text-lg">Pengaturan Organisasi</h2>

        <form method="POST" action="{{ route('dashboard.manager.settings.update') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
            @csrf

            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nama
                    Organisasi</label>
                <input type="text" name="organization_name" required
                    value="{{ old('organization_name', $settingsData['organization_name']) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Alamat
                    Organisasi</label>
                <input type="text" name="organization_address" required
                    value="{{ old('organization_address', $settingsData['organization_address'] ?? 'Kantor LPK Kama Praja Madiun') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email Support</label>
                <input type="email" name="support_email" required
                    value="{{ old('support_email', $settingsData['support_email']) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Timezone</label>
                <select name="timezone" required
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                    <option value="Asia/Jakarta" @selected(old('timezone', $settingsData['timezone']) === 'Asia/Jakarta')>Asia/Jakarta</option>
                    <option value="Asia/Makassar" @selected(old('timezone', $settingsData['timezone']) === 'Asia/Makassar')>Asia/Makassar</option>
                    <option value="Asia/Jayapura" @selected(old('timezone', $settingsData['timezone']) === 'Asia/Jayapura')>Asia/Jayapura</option>
                </select>
            </div>

            <!-- Logo Management -->
            <div class="sm:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-semibold text-slate-900 sm:text-base">Manajemen Logo</h3>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <!-- Current Logo Display -->
                    <div class="sm:col-span-1">
                        <p class="mb-2 text-xs font-semibold text-slate-600">Logo Saat Ini</p>
                        <div
                            class="flex h-28 items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-white sm:h-32">
                            @if ($settingsData['logo_filename'] ?? false)
                                <img src="{{ asset('img/' . $settingsData['logo_filename']) }}" alt="Logo"
                                    style="object-fit: {{ old('logo_fit', $settingsData['logo_fit'] ?? 'contain') }}"
                                    class="h-full w-full rounded-lg">
                            @else
                                <span class="text-xs text-slate-500">Tidak ada logo</span>
                            @endif
                        </div>
                    </div>

                    <!-- Logo Upload & Settings -->
                    <div class="sm:col-span-1 flex flex-col gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Upload Logo Baru</label>
                            <input type="file" name="logo" accept="image/jpeg,image/png,image/jpg,image/webp"
                                class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs text-slate-700 file:mr-2 file:rounded-lg file:border-0 file:bg-slate-200 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-slate-700 sm:px-3 sm:py-2 sm:text-sm sm:file:mr-3 sm:file:px-3 sm:file:py-1.5">
                            <p class="mt-1 text-xs text-slate-500">Maks: 2 MB (JPG, PNG, WebP)</p>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs font-semibold text-slate-600">Tata Letak Logo</label>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                <label
                                    class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-2 py-2 cursor-pointer hover:bg-slate-50 sm:px-3 sm:py-2">
                                    <input type="radio" name="logo_fit" value="contain"
                                        {{ old('logo_fit', $settingsData['logo_fit'] ?? 'contain') === 'contain' ? 'checked' : '' }}
                                        class="h-4 w-4">
                                    <span class="text-xs font-medium text-slate-700">Sesuai</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-2 py-2 cursor-pointer hover:bg-slate-50 sm:px-3 sm:py-2">
                                    <input type="radio" name="logo_fit" value="cover"
                                        {{ old('logo_fit', $settingsData['logo_fit'] ?? 'contain') === 'cover' ? 'checked' : '' }}
                                        class="h-4 w-4">
                                    <span class="text-xs font-medium text-slate-700">Potong</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-2 py-2 cursor-pointer hover:bg-slate-50 sm:px-3 sm:py-2">
                                    <input type="radio" name="logo_fit" value="fill"
                                        {{ old('logo_fit', $settingsData['logo_fit'] ?? 'contain') === 'fill' ? 'checked' : '' }}
                                        class="h-4 w-4">
                                    <span class="text-xs font-medium text-slate-700">Penuh</span>
                                </label>
                            </div>
                            <details class="mt-2">
                                <summary class="cursor-pointer text-xs font-semibold text-slate-600 hover:text-slate-700">?
                                    Bantuan</summary>
                                <p class="mt-1 text-xs text-slate-500">
                                    <strong>Sesuai:</strong> Tampilkan seluruh logo •
                                    <strong>Potong:</strong> Isi area •
                                    <strong>Penuh:</strong> Panjang & lebar penuh
                                </p>
                            </details>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="sm:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 sm:text-base">Informasi Kontak</h3>
                    <button type="button" id="toggle-contact-edit"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 sm:w-auto sm:px-3 sm:py-1.5">
                        ✎ Edit Kontak
                    </button>
                </div>

                <!-- Contact Display View (Default) -->
                <div id="contact-display" class="mt-4 grid gap-2 sm:gap-3 grid-cols-1 sm:grid-cols-2">
                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                        <p class="text-xs font-semibold text-slate-600">🗺️ Maps</p>
                        <p class="mt-2 truncate text-xs text-slate-900 sm:text-sm">
                            @if ($settingsData['contacts']['googlemaps'] ?? false)
                                <a href="{{ $settingsData['contacts']['googlemaps'] }}" target="_blank"
                                    class="text-blue-600 hover:underline">
                                    Lihat Lokasi
                                </a>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                        <p class="text-xs font-semibold text-slate-600">💬 WhatsApp</p>
                        <p class="mt-2 truncate text-xs text-slate-900 sm:text-sm">
                            @if ($settingsData['contacts']['whatsapp'] ?? false)
                                <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $settingsData['contacts']['whatsapp']) }}"
                                    target="_blank" class="text-green-600 hover:underline">
                                    {{ $settingsData['contacts']['whatsapp'] }}
                                </a>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                        <p class="text-xs font-semibold text-slate-600">f️ Facebook</p>
                        <p class="mt-2 truncate text-xs text-slate-900 sm:text-sm">
                            @if ($settingsData['contacts']['facebook'] ?? false)
                                <a href="{{ $settingsData['contacts']['facebook'] }}" target="_blank"
                                    class="text-blue-600 hover:underline">
                                    Halaman
                                </a>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                        <p class="text-xs font-semibold text-slate-600">📷 Instagram</p>
                        <p class="mt-2 truncate text-xs text-slate-900 sm:text-sm">
                            @if ($settingsData['contacts']['instagram'] ?? false)
                                <a href="{{ $settingsData['contacts']['instagram'] }}" target="_blank"
                                    class="text-pink-600 hover:underline">
                                    Akun
                                </a>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-white p-3 sm:col-span-2">
                        <p class="text-xs font-semibold text-slate-600">▶️ YouTube</p>
                        <p class="mt-2 truncate text-xs text-slate-900 sm:text-sm">
                            @if ($settingsData['contacts']['youtube'] ?? false)
                                <a href="{{ $settingsData['contacts']['youtube'] }}" target="_blank"
                                    class="text-red-600 hover:underline">
                                    Channel
                                </a>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Contact Edit Form (Hidden by default) -->
                <div id="contact-form" class="mt-4 hidden">
                    <div class="grid gap-3 sm:gap-4">
                        <!-- Google Maps -->
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                🗺️ Google Maps
                            </label>
                            <input type="url" name="contact_googlemaps"
                                value="{{ old('contact_googlemaps', $settingsData['contacts']['googlemaps'] ?? '') }}"
                                placeholder="https://maps.google.com/?q=..."
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                        </div>

                        <!-- WhatsApp -->
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                💬 WhatsApp
                            </label>
                            <input type="text" name="contact_whatsapp"
                                value="{{ old('contact_whatsapp', $settingsData['contacts']['whatsapp'] ?? '') }}"
                                placeholder="+62 8787654321"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                            <p class="mt-1 text-xs text-slate-500">Format: +62 diikuti 7-15 digit</p>
                        </div>

                        <!-- Facebook -->
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                f️ Facebook
                            </label>
                            <input type="url" name="contact_facebook"
                                value="{{ old('contact_facebook', $settingsData['contacts']['facebook'] ?? '') }}"
                                placeholder="https://facebook.com/..."
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                📷 Instagram
                            </label>
                            <input type="url" name="contact_instagram"
                                value="{{ old('contact_instagram', $settingsData['contacts']['instagram'] ?? '') }}"
                                placeholder="https://instagram.com/..."
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                        </div>

                        <!-- YouTube -->
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">
                                ▶️ YouTube
                            </label>
                            <input type="url" name="contact_youtube"
                                value="{{ old('contact_youtube', $settingsData['contacts']['youtube'] ?? '') }}"
                                placeholder="https://youtube.com/@..."
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                        </div>

                        <!-- Close Edit Button -->
                        <button type="button" id="close-contact-edit"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 sm:w-auto sm:py-1.5">
                            Close Edit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="sm:col-span-2 flex flex-col gap-2 sm:flex-row sm:justify-end sm:gap-3">
                <a href="{{ route('dashboard.manager.home') }}"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-center text-xs font-semibold text-slate-700 transition hover:bg-slate-50 sm:text-sm">
                    Batal
                </a>
                <button type="submit"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-700 sm:text-sm">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </section>

    <!-- Settings Summary -->
    <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4 shadow-sm sm:p-5">
        <h3 class="mb-3 text-sm font-semibold text-slate-900 sm:mb-4 sm:text-base">Ringkasan Pengaturan</h3>

        <div class="grid gap-2 sm:gap-3 grid-cols-1 sm:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-3">
                <p class="text-xs font-semibold text-slate-500">Organisasi</p>
                <p class="mt-1 truncate text-xs text-slate-900 sm:text-sm">{{ $settingsData['organization_name'] }}</p>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-3">
                <p class="text-xs font-semibold text-slate-500">Email Support</p>
                <p class="mt-1 truncate text-xs text-slate-900 sm:text-sm">{{ $settingsData['support_email'] }}</p>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-3">
                <p class="text-xs font-semibold text-slate-500">Timezone</p>
                <p class="mt-1 text-xs text-slate-900 sm:text-sm">{{ $settingsData['timezone'] }}</p>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-3">
                <p class="text-xs font-semibold text-slate-500">Tata Letak Logo</p>
                <p class="mt-1 text-xs text-slate-900 sm:text-sm">
                    @switch($settingsData['logo_fit'] ?? 'contain')
                        @case('contain')
                            Sesuai Ukuran
                        @break

                        @case('cover')
                            Potong Tepi
                        @break

                        @case('fill')
                            Isi Penuh
                        @break

                        @default
                            Sesuai Ukuran
                    @endswitch
                </p>
            </div>

            @if ($settingsData['contacts']['googlemaps'] ?? false)
                <div class="rounded-lg border border-slate-200 bg-white p-3 sm:col-span-2">
                    <p class="text-xs font-semibold text-slate-500">🗺️ Google Maps</p>
                    <p class="mt-1 break-all text-xs text-slate-600">{{ $settingsData['contacts']['googlemaps'] }}</p>
                </div>
            @endif

            @if ($settingsData['contacts']['whatsapp'] ?? false)
                <div class="rounded-lg border border-slate-200 bg-white p-3">
                    <p class="text-xs font-semibold text-slate-500">💬 WhatsApp</p>
                    <p class="mt-1 text-xs text-slate-900 sm:text-sm">{{ $settingsData['contacts']['whatsapp'] }}</p>
                </div>
            @endif
        </div>
    </section>

    <script>
        // Preview logo fit changes in real-time
        const logoFitButtons = document.querySelectorAll('input[name="logo_fit"]');
        const logoDisplay = document.querySelector('[style*="object-fit"]');

        if (logoFitButtons.length && logoDisplay) {
            logoFitButtons.forEach(button => {
                button.addEventListener('change', function() {
                    logoDisplay.style.objectFit = this.value;
                });
            });
        }

        // Toggle contact editing mode
        const toggleContactEditBtn = document.getElementById('toggle-contact-edit');
        const closeContactEditBtn = document.getElementById('close-contact-edit');
        const contactDisplay = document.getElementById('contact-display');
        const contactForm = document.getElementById('contact-form');

        if (toggleContactEditBtn && contactDisplay && contactForm) {
            toggleContactEditBtn.addEventListener('click', function(e) {
                e.preventDefault();
                contactDisplay.classList.add('hidden');
                contactForm.classList.remove('hidden');
                toggleContactEditBtn.classList.add('hidden');
            });
        }

        if (closeContactEditBtn && contactDisplay && contactForm) {
            closeContactEditBtn.addEventListener('click', function(e) {
                e.preventDefault();
                contactForm.classList.add('hidden');
                contactDisplay.classList.remove('hidden');
                toggleContactEditBtn.classList.remove('hidden');
            });
        }

        // Auto-close sidebar after form submission
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                const overlay = document.getElementById('sidebar-overlay');
                const sidebar = document.getElementById('dashboard-sidebar');
                if (overlay && sidebar) {
                    overlay.classList.add('hidden');
                    sidebar.classList.add('-translate-x-full');
                }
            });
        }
    </script>
@endsection
