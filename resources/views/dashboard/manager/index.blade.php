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
                    <option value="2024-01">Januari 2024</option>
                    <option value="2024-02">Februari 2024</option>
                    <option value="2024-03">Maret 2024</option>
                    <option value="2024-04">April 2024</option>
                    <option value="2024-05">Mei 2024</option>
                    <option value="2024-06" selected>Juni 2024</option>
                </select>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-semibold text-slate-700 mb-1 sm:mb-2">Sampai Bulan/Tahun</label>
                <select id="endMonth"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="2024-06">Juni 2024</option>
                    <option value="2024-07">Juli 2024</option>
                    <option value="2024-08">Agustus 2024</option>
                    <option value="2024-09">September 2024</option>
                    <option value="2024-10">Oktober 2024</option>
                    <option value="2024-11" selected>November 2024</option>
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
                    <p class="text-lg sm:text-xl font-bold text-slate-900" id="totalRegistration">654</p>
                </div>
                <div>
                    <p class="text-xs text-slate-600 mb-1">Rata-rata/Bulan</p>
                    <p class="text-lg sm:text-xl font-bold text-slate-900" id="avgRegistration">109</p>
                </div>
                <div>
                    <p class="text-xs text-slate-600 mb-1">Peak Month</p>
                    <p class="text-lg sm:text-xl font-bold text-blue-600" id="peakMonth">Mei</p>
                </div>
                <div>
                    <p class="text-xs text-slate-600 mb-1">Pertumbuhan</p>
                    <p class="text-lg sm:text-xl font-bold text-green-600">+15%</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions Section -->
    <section class="mt-6 sm:mt-8 grid gap-4 sm:gap-6 grid-cols-1 md:grid-cols-3">
        <!-- Recent Activity -->
        <article class="rounded-lg sm:rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm md:col-span-2">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Aktivitas Terkini</h3>
                <a href="{{ route('dashboard.manager.reports') }}"
                    class="text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700 transition">Lihat
                    laporan →</a>
            </div>
            <div class="space-y-3">
                @foreach ($activities as $activity)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 sm:p-4 hover:bg-slate-100 transition">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-900">{{ $activity['title'] }}</p>
                            <span class="text-xs text-slate-500 whitespace-nowrap">{{ $activity['time'] }}</span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600">{{ $activity['description'] }}</p>
                    </div>
                @endforeach
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Peserta Individu</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.manager.participants.group') }}"
                    class="block rounded-lg border border-slate-200 p-3 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-purple-50 hover:border-purple-300 hover:text-purple-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Peserta Grub</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.manager.instructors') }}"
                    class="block rounded-lg border border-slate-200 p-3 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-green-50 hover:border-green-300 hover:text-green-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Kelola Pengajar</span>
                    </div>
                </a>
                <a href="{{ route('dashboard.manager.settings') }}"
                    class="block rounded-lg border border-slate-200 p-3 text-xs sm:text-sm font-semibold text-slate-700 transition hover:bg-orange-50 hover:border-orange-300 hover:text-orange-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 11l7-7 7 7M5 19l7-7 7 7" />
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
        // Sample chart data
        const chartData = {
            labels: ['Agustus 2018', 'September 2018', 'Oktober 2018', 'November 2018', 'Desember 2018', 'Januari 2019',
                'Februari 2019', 'Maret 2019'
            ],
            datasets: [{
                label: 'Pendaftaran',
                data: [92, 105, 114, 128, 134, 121, 98, 110],
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
        function updateStats() {
            const total = chartData.datasets[0].data.reduce((a, b) => a + b, 0);
            const avg = Math.round(total / chartData.datasets[0].data.length);
            const max = Math.max(...chartData.datasets[0].data);
            const maxIndex = chartData.datasets[0].data.indexOf(max);
            const peakLabel = chartData.labels[maxIndex].split(' ')[0];

            document.getElementById('totalRegistration').textContent = total;
            document.getElementById('avgRegistration').textContent = avg;
            document.getElementById('peakMonth').textContent = peakLabel;
        }

        // Filter button functionality
        document.getElementById('filterBtn').addEventListener('click', function() {
            const startMonth = document.getElementById('startMonth').value;
            const endMonth = document.getElementById('endMonth').value;

            // Update chart based on filters
            // This is a placeholder - in a real app, you'd fetch new data from the server
            console.log('Filter applied from', startMonth, 'to', endMonth);

            // Show feedback
            this.textContent = 'Data diperbarui!';
            setTimeout(() => {
                this.textContent = 'Terapkan Filter';
            }, 1500);
        });

        // Initialize stats on page load
        updateStats();

        // Responsive chart adjustment
        window.addEventListener('resize', function() {
            if (chart) {
                chart.resize();
            }
        });
    </script>
@endsection
