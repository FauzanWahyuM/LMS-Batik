@extends('dashboard.layouts.app')

@section('dashboard-content')
    <!-- Statistics Cards Section -->
    <section class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 mb-6 sm:mb-8">
        <!-- Individual Participants Card -->
        <a href="{{ route('dashboard.manager.participants.individual') }}"
            class="group rounded-lg sm:rounded-xl border border-slate-200 bg-white p-3 sm:p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                <div
                    class="flex h-8 sm:h-10 w-8 sm:w-10 items-center justify-center rounded-lg bg-blue-50 group-hover:bg-blue-100 transition">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peserta Aktif</p>
            <p class="mt-2 sm:mt-3 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['individualParticipants'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Individu</p>
        </a>

        <!-- Group Participants Card -->
        <a href="{{ route('dashboard.manager.participants.group') }}"
            class="group rounded-lg sm:rounded-xl border border-slate-200 bg-white p-3 sm:p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                <div
                    class="flex h-8 sm:h-10 w-8 sm:w-10 items-center justify-center rounded-lg bg-purple-50 group-hover:bg-purple-100 transition">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-purple-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM12.338 5.338l4.586-4.586a2 2 0 012.828 0c.781.781.781 2.047 0 2.828l-4.586 4.586m0 0a2 2 0 002.828 2.828l4.586-4.586a2 2 0 000-2.828 2 2 0 00-2.828 0l-4.586 4.586z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Peserta Grub</p>
            <p class="mt-2 sm:mt-3 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['groupParticipants'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Lembaga</p>
        </a>

        <!-- Active Instructors Card -->
        <a href="{{ route('dashboard.manager.instructors') }}"
            class="group rounded-lg sm:rounded-xl border border-slate-200 bg-white p-3 sm:p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                <div
                    class="flex h-8 sm:h-10 w-8 sm:w-10 items-center justify-center rounded-lg bg-green-50 group-hover:bg-green-100 transition">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pengajar Aktif</p>
            <p class="mt-2 sm:mt-3 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['activeInstructors'] }}</p>
        </a>

        <!-- Active Programs Card -->
        <a href="{{ route('dashboard.manager.programs') }}"
            class="group rounded-lg sm:rounded-xl border border-slate-200 bg-white p-3 sm:p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                <div
                    class="flex h-8 sm:h-10 w-8 sm:w-10 items-center justify-center rounded-lg bg-orange-50 group-hover:bg-orange-100 transition">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5 text-orange-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Program</p>
            <p class="mt-2 sm:mt-3 text-2xl sm:text-3xl font-bold text-slate-900">{{ $stats['activePrograms'] }}</p>
            <p class="mt-1 text-xs text-slate-500">Tersedia</p>
        </a>
    </section>

    <!-- Registration Statistics Section -->
    <section class="rounded-lg sm:rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        @php
            $registrationPoints = $registrationStats['points'] ?? [];
            $registrationSummary = $registrationStats['summary'] ?? [];
            $registrationStartDefault = $registrationPoints[0]['key'] ?? '';
            $registrationEndDefault = $registrationPoints[count($registrationPoints) - 1]['key'] ?? '';
        @endphp
        <div class="mb-6">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 mb-2">Statistik Pendaftaran</h2>
            <p class="text-xs sm:text-sm text-slate-600">Jumlah peserta yang terdaftar per bulan</p>
        </div>

        <!-- Date Range Filter -->
        <div class="grid grid-cols-2 gap-2 sm:gap-3 mb-6 sm:grid-cols-4 lg:grid-cols-5">
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold text-slate-700 mb-1 sm:mb-2">Dari Bulan/Tahun</label>
                <select id="startMonth"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @foreach ($registrationPoints as $point)
                        <option value="{{ $point['key'] }}"
                            {{ $point['key'] === $registrationStartDefault ? 'selected' : '' }}>
                            {{ $point['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold text-slate-700 mb-1 sm:mb-2">Sampai Bulan/Tahun</label>
                <select id="endMonth"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    @foreach ($registrationPoints as $point)
                        <option value="{{ $point['key'] }}"
                            {{ $point['key'] === $registrationEndDefault ? 'selected' : '' }}>
                            {{ $point['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button id="filterBtn"
                class="col-span-2 sm:col-span-1 lg:col-span-1 rounded-lg bg-blue-600 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Terapkan Filter
            </button>
        </div>

        <!-- Chart Container -->
        <div class="space-y-4">
            <div class="overflow-x-auto">
                <canvas id="chartRegistrasi" class="min-h-80 sm:min-h-96"></canvas>
            </div>

            <!-- Chart Statistics -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 pt-4 sm:pt-6 border-t border-slate-200">
                <div>
                    <p class="text-xs text-slate-600 mb-1">Total Pendaftaran</p>
                    <p class="text-lg sm:text-xl font-bold text-slate-900" id="totalRegistration">
                        {{ $registrationSummary['total'] ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-600 mb-1">Rata-rata/Bulan</p>
                    <p class="text-lg sm:text-xl font-bold text-slate-900" id="avgRegistration">
                        {{ $registrationSummary['average'] ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-600 mb-1">Peak Month</p>
                    <p class="text-lg sm:text-xl font-bold text-blue-600" id="peakMonth">
                        {{ $registrationSummary['peakMonth'] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-600 mb-1">Pertumbuhan</p>
                    <p class="text-lg sm:text-xl font-bold text-green-600" id="growthRegistration">
                        {{ isset($registrationSummary['growth']) ? ($registrationSummary['growth'] >= 0 ? '+' : '') . $registrationSummary['growth'] . '%' : '+0%' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions Section -->
    <section class="mt-6 sm:mt-8 grid gap-4 sm:gap-6 grid-cols-1 md:grid-cols-3">
        <!-- Alumni Achievements -->
        <article class="rounded-lg sm:rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm md:col-span-2">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Prestasi Alumni</h3>
                <a href="{{ route('dashboard.manager.achievements') }}"
                    class="text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700 transition">Lihat
                    semua →</a>
            </div>
            <div class="space-y-3">
                @forelse ($alumniAchievements ?? [] as $achievement)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 sm:p-4 hover:bg-slate-100 transition">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-900">{{ $achievement['title'] }}</p>
                            <span class="text-xs text-slate-500 whitespace-nowrap">{{ $achievement['year'] }}</span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600">
                            {{ $achievement['winner'] }} • {{ $achievement['event'] }} • {{ $achievement['rank'] }}
                        </p>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600">
                        Belum ada data prestasi alumni.
                    </div>
                @endforelse
            </div>
        </article>

        <!-- Quick Actions -->
        <article class="rounded-lg sm:rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-4">Aksi Cepat</h3>
            <div class="space-y-2 sm:space-y-3">
                <a href="{{ route('dashboard.manager.participants.individual') }}"
                    class="block rounded-lg border border-slate-200 p-3 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Peserta Individu</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.manager.participants.group') }}"
                    class="block rounded-lg border border-slate-200 p-3 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-purple-50 hover:border-purple-300 hover:text-purple-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM12 14a7 7 0 00-7 7" />
                        </svg>
                        <span>Peserta Grub</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.manager.instructors') }}"
                    class="block rounded-lg border border-slate-200 p-3 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-green-50 hover:border-green-300 hover:text-green-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0112 20.055a12.083 12.083 0 01-6.16-9.477L12 14z" />
                        </svg>
                        <span>Kelola Pengajar</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.manager.programs') }}"
                    class="block rounded-lg border border-slate-200 p-3 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-teal-50 hover:border-teal-300 hover:text-teal-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Kelola Program</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.manager.settings') }}"
                    class="block rounded-lg border border-slate-200 p-3 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-orange-50 hover:border-orange-300 hover:text-orange-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317a1.724 1.724 0 013.35 0 1.724 1.724 0 002.573 1.066 1.724 1.724 0 012.347 2.347 1.724 1.724 0 001.065 2.572 1.724 1.724 0 010 3.35 1.724 1.724 0 00-1.065 2.573 1.724 1.724 0 01-2.347 2.347 1.724 1.724 0 00-2.573 1.065 1.724 1.724 0 01-3.35 0 1.724 1.724 0 00-2.572-1.065 1.724 1.724 0 01-2.347-2.347 1.724 1.724 0 00-1.066-2.573 1.724 1.724 0 010-3.35 1.724 1.724 0 001.066-2.572 1.724 1.724 0 012.347-2.347 1.724 1.724 0 002.572-1.066z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pengaturan</span>
                    </div>
                </a>
            </div>
        </article>
    </section>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const registrationPoints = @json($registrationPoints);

        function buildChartData(points) {
            return {
                labels: points.map((point) => point.label),
                datasets: [{
                    label: 'Pendaftaran',
                    data: points.map((point) => point.total),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                }]
            };
        }

        const chartData = buildChartData(registrationPoints);

        const chartConfig = {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            color: '#64748b'
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                family: "'Plus Jakarta Sans', sans-serif"
                            },
                            color: '#64748b'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                size: 13,
                                family: "'Plus Jakarta Sans', sans-serif",
                                weight: 600
                            },
                            color: '#1e293b',
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        padding: 12,
                        titleFont: {
                            size: 13,
                            family: "'Plus Jakarta Sans', sans-serif",
                            weight: 600
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Plus Jakarta Sans', sans-serif"
                        },
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Pendaftaran: ' + context.parsed.y + ' peserta';
                            }
                        }
                    }
                }
            }
        };

        // Initialize chart
        const ctx = document.getElementById('chartRegistrasi').getContext('2d');
        let chart = new Chart(ctx, chartConfig);

        // Update statistics
        function updateStats(points) {
            if (!points.length) {
                document.getElementById('totalRegistration').textContent = '0';
                document.getElementById('avgRegistration').textContent = '0';
                document.getElementById('peakMonth').textContent = '-';
                document.getElementById('growthRegistration').textContent = '+0%';
                return;
            }

            const total = points.reduce((sum, point) => sum + point.total, 0);
            const avg = Math.round(total / points.length);
            const peakPoint = points.reduce((highest, point) => point.total > highest.total ? point : highest, points[0]);
            const firstTotal = points[0].total;
            const lastTotal = points[points.length - 1].total;
            const growth = firstTotal > 0 ? Math.round(((lastTotal - firstTotal) / firstTotal) * 1000) / 10 : 0;

            document.getElementById('totalRegistration').textContent = total;
            document.getElementById('avgRegistration').textContent = avg;
            document.getElementById('peakMonth').textContent = peakPoint.label;
            document.getElementById('growthRegistration').textContent = (growth >= 0 ? '+' : '') + growth + '%';
        }

        function filterRegistrationPoints(startMonth, endMonth) {
            if (!registrationPoints.length) {
                return [];
            }

            const startIndex = registrationPoints.findIndex((point) => point.key === startMonth);
            const endIndex = registrationPoints.findIndex((point) => point.key === endMonth);

            if (startIndex === -1 || endIndex === -1) {
                return registrationPoints;
            }

            const from = Math.min(startIndex, endIndex);
            const to = Math.max(startIndex, endIndex);

            return registrationPoints.slice(from, to + 1);
        }

        // Filter button functionality
        document.getElementById('filterBtn').addEventListener('click', function() {
            const startMonth = document.getElementById('startMonth').value;
            const endMonth = document.getElementById('endMonth').value;

            const filteredPoints = filterRegistrationPoints(startMonth, endMonth);

            chart.data = buildChartData(filteredPoints);
            chart.update();
            updateStats(filteredPoints);

            // Show feedback
            this.textContent = 'Data diperbarui!';
            setTimeout(() => {
                this.textContent = 'Terapkan Filter';
            }, 1500);
        });

        // Initialize stats on page load
        updateStats(registrationPoints);

        // Responsive chart adjustment
        window.addEventListener('resize', function() {
            if (chart) {
                chart.resize();
            }
        });
    </script>
@endsection
