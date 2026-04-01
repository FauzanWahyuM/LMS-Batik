@extends('dashboard.layouts.app')

@section('dashboard-content')
    <!-- Filter Section -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold text-slate-900">Filter Laporan</h2>

        <form method="GET" action="{{ route('dashboard.manager.reports') }}"
            class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-4">
            <div class="flex-1">
                <label for="month" class="block text-xs sm:text-sm font-medium text-slate-700">Bulan</label>
                <select name="month" id="month"
                    class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 hover:border-slate-400 focus:border-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ $monthNames[$m] ?? 'Invalid' }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex-1">
                <label for="year" class="block text-xs sm:text-sm font-medium text-slate-700">Tahun</label>
                <select name="year" id="year"
                    class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-900 hover:border-slate-400 focus:border-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                    @foreach ($availableYears as $yr)
                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                class="w-full sm:w-auto rounded-lg bg-slate-900 px-6 py-2 text-xs sm:text-sm font-semibold text-white transition hover:bg-slate-700">
                Filter
            </button>
        </form>
    </section>

    <!-- Monthly Summary Cards -->
    <section class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Individual Registrations -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pendaftaran Individu</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $monthlyData['total_individual_registrations'] }}</p>
            <p class="mt-1 text-xs text-slate-600">
                Rp
                {{ number_format($monthlyData['total_individual_registrations'] * $monthlyData['individual_cost'], 0, ',', '.') }}
            </p>
        </article>

        <!-- Group Registrations -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pendaftaran Kelompok</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $monthlyData['total_group_registrations'] }}</p>
            <p class="mt-1 text-xs text-slate-600">
                {{ $monthlyData['total_group_members'] }} peserta
            </p>
        </article>

        <!-- Total Profit -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total Pendapatan</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">Rp
                {{ number_format($monthlyData['total_profit'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-600">{{ $monthlyData['total_participants'] }} peserta total</p>
        </article>

        <!-- Peak Registration Date -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Tanggal Pendaftaran Puncak</p>
            <p class="mt-2 text-lg font-bold text-slate-900">{{ $monthlyData['peak_registration_date'] }}</p>
            <p class="mt-1 text-xs text-slate-600">Hari dengan pendaftaran tertinggi</p>
        </article>
    </section>

    <!-- Download Section -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold text-slate-900">Unduh Laporan</h2>

        <div class="grid gap-3 sm:grid-cols-2">
            <form method="POST" action="{{ route('dashboard.manager.reports.export') }}" class="flex flex-col gap-2">
                @csrf
                <p class="text-sm font-medium text-slate-700">Laporan Bulan {{ $monthlyData['month_name'] }}
                    {{ $monthlyData['year'] }}</p>
                <div class="flex gap-2">
                    <input type="hidden" name="month" value="{{ $selectedMonth }}">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <button type="submit" name="export_type" value="pdf"
                        class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        📄 PDF
                    </button>
                    <button type="submit" name="export_type" value="csv"
                        class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        📊 CSV
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('dashboard.manager.reports.export') }}" class="flex flex-col gap-2">
                @csrf
                <p class="text-sm font-medium text-slate-700">Laporan Tahunan {{ $monthlyData['year'] }}</p>
                <div class="flex gap-2">
                    <input type="hidden" name="month" value="all">
                    <input type="hidden" name="year" value="{{ $selectedYear }}">
                    <button type="submit" name="export_type" value="pdf"
                        class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        📄 PDF Tahun
                    </button>
                    <button type="submit" name="export_type" value="csv"
                        class="flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        📊 CSV Tahun
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Instructor Salary Report -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold text-slate-900">Laporan Gaji Pengajar - {{ $monthlyData['month_name'] }}
            {{ $monthlyData['year'] }}</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200">
                    <tr class="text-left">
                        <th class="px-3 py-3 font-semibold text-slate-600">Nama Pengajar</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Status</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Gaji Pokok</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Kelas</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Komisi</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($monthlyData['instructor_salaries'] as $salary)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $salary['name'] }}</td>
                            <td class="px-3 py-3 text-right">
                                <span
                                    class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $salary['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $salary['status'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-right font-medium text-slate-900">Rp
                                {{ number_format($salary['base_salary'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right text-slate-600">{{ $salary['classes_handled'] }} kelas</td>
                            <td class="px-3 py-3 text-right font-medium text-slate-900">Rp
                                {{ number_format($salary['commission'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-right font-bold text-slate-900">Rp
                                {{ number_format($salary['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-sm text-slate-500">Tidak ada data
                                pengajar</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- All Months Summary Table -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold text-slate-900">Ringkasan Tahunan {{ $selectedYear }}</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200">
                    <tr class="text-left">
                        <th class="px-3 py-3 font-semibold text-slate-600">Bulan</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Total Pendaftar</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Pendapatan</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($allMonthsData as $monthData)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $monthData['month_name'] }}</td>
                            <td class="px-3 py-3 text-right text-slate-900">{{ $monthData['total_registrations'] }}
                                peserta</td>
                            <td class="px-3 py-3 text-right font-medium text-slate-900">Rp
                                {{ number_format($monthData['total_profit'], 0, ',', '.') }}</td>
                            <td class="px-3 py-3 text-center">
                                <a href="{{ route('dashboard.manager.reports', ['month' => $monthData['month'], 'year' => $selectedYear]) }}"
                                    class="inline-block rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-sm text-slate-500">Tidak ada data bulanan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <script>
        // Auto-close sidebar on mobile after filter
        document.querySelector('form')?.addEventListener('submit', function() {
            const overlay = document.getElementById('sidebar-overlay');
            const sidebar = document.getElementById('dashboard-sidebar');
            if (overlay && sidebar) {
                overlay.classList.add('hidden');
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
@endsection
