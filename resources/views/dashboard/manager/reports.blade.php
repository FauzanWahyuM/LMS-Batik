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
                            {{ $monthNames[$m] ?? 'Tidak valid' }}
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
                Saring
            </button>
        </form>
    </section>

    <!-- Monthly Summary Cards -->
    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
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

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Calon Peserta / Belum Membayar</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">{{ $monthlyData['total_pending_registrations'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-slate-600">Masih menunggu validasi atau pembayaran</p>
        </article>

        <!-- Total Revenue -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pendapatan Diterima</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">Rp
                {{ number_format($monthlyData['total_profit'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-600">Hanya peserta yang sudah membayar</p>
        </article>

        <!-- Total Expenditure -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total Pengeluaran</p>
            <p class="mt-2 text-3xl font-bold text-rose-600">Rp
                {{ number_format($monthlyData['total_expenditure'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-600">Akumulasi gaji pengajar</p>
        </article>

        <!-- Total Net -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total Keseluruhan</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">Rp
                {{ number_format($monthlyData['total_overall'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-600">Pendapatan dikurangi pengeluaran</p>
        </article>

        <!-- Peak Registration Date -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Tanggal Pendaftaran Tertinggi</p>
            <p class="mt-2 text-lg font-bold text-slate-900">{{ $monthlyData['peak_registration_date'] }}</p>
            <p class="mt-1 text-xs text-slate-600">Hari dengan pendaftaran tertinggi</p>
        </article>

        <!-- Warehouse Summary -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total Barang Gudang</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $monthlyData['total_warehouse_items'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-slate-600">Jumlah jenis barang di gudang</p>
        </article>

        <!-- Low Stock Items -->
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Barang Stok Rendah</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">{{ $monthlyData['low_stock_items'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-slate-600">Barang dengan stok di bawah minimum</p>
        </article>
    </section>

    @php
        $pendingIndividuals = $monthlyData['pending_individual_participants'] ?? [];
        $pendingGroups = $monthlyData['pending_group_participants'] ?? [];
        $pendingRows = array_merge($pendingIndividuals, $pendingGroups);
        $warehouseMaterials = $monthlyData['warehouse_materials'] ?? [];
    @endphp

    @if (!empty($pendingRows))
        <section class="mb-6 rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Calon Peserta dan Belum Membayar</h2>
                    <p class="text-sm text-slate-500">Data yang masih pending atau belum divalidasi tidak dihitung ke
                        pendapatan.</p>
                </div>
                <span class="inline-flex w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                    {{ count($pendingRows) }} pendaftaran
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200">
                        <tr class="text-left">
                            <th class="px-3 py-3 font-semibold text-slate-600">Tipe</th>
                            <th class="px-3 py-3 font-semibold text-slate-600">Nama</th>
                            <th class="px-3 py-3 font-semibold text-slate-600">Status Validasi</th>
                            <th class="px-3 py-3 font-semibold text-slate-600">Status Pembayaran</th>
                            <th class="px-3 py-3 font-semibold text-slate-600 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($pendingRows as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ $row['type'] ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $row['name'] ?? ($row['group_name'] ?? '-') }}</td>
                                <td class="px-3 py-3 text-slate-700">
                                    {{ $row['validation_status'] ?? ($row['status'] ?? '-') }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $row['payment_status'] ?? '-' }}</td>
                                <td class="px-3 py-3 text-right font-semibold text-slate-900">Rp
                                    {{ number_format((int) ($row['revenue'] ?? 0), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <!-- Warehouse Inventory Report -->
    @if (!empty($warehouseMaterials))
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Laporan Kelola Gudang</h2>
                    <p class="text-sm text-slate-500">Daftar lengkap barang-barang di gudang dengan status stok.</p>
                </div>
                <span class="inline-flex w-fit rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                    {{ count($warehouseMaterials) }} barang
                </span>
            </div>

            <div class="mb-4 grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg bg-slate-50 p-3">
                    <p class="text-xs font-medium text-slate-600">Total Barang</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $monthlyData['total_warehouse_items'] }}</p>
                </div>
                <div class="rounded-lg bg-amber-50 p-3">
                    <p class="text-xs font-medium text-amber-600">Stok Rendah</p>
                    <p class="mt-1 text-2xl font-bold text-amber-600">{{ $monthlyData['low_stock_items'] }}</p>
                </div>
                <div class="rounded-lg bg-blue-50 p-3">
                    <p class="text-xs font-medium text-blue-600">Kategori</p>
                    <p class="mt-1 text-2xl font-bold text-blue-600">
                        {{ count($monthlyData['warehouse_categories'] ?? []) }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200">
                        <tr class="text-left">
                            <th class="px-3 py-3 font-semibold text-slate-600">Nama Barang</th>
                            <th class="px-3 py-3 font-semibold text-slate-600">Kategori</th>
                            <th class="px-3 py-3 font-semibold text-slate-600">Unit</th>
                            <th class="px-3 py-3 font-semibold text-slate-600 text-right">Stok Saat Ini</th>
                            <th class="px-3 py-3 font-semibold text-slate-600 text-right">Stok Minimum</th>
                            <th class="px-3 py-3 font-semibold text-slate-600 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($warehouseMaterials as $material)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ $material['name'] }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $material['category'] }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $material['unit'] }}</td>
                                <td class="px-3 py-3 text-right font-semibold text-slate-900">{{ $material['stock'] }}
                                </td>
                                <td class="px-3 py-3 text-right text-slate-600">{{ $material['minimum_stock'] }}</td>
                                <td class="px-3 py-3 text-center">
                                    <span
                                        class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $material['status_color'] === 'amber' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $material['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (!empty($monthlyData['warehouse_categories']))
                <div class="mt-5 border-t border-slate-200 pt-4">
                    <h3 class="mb-3 font-semibold text-slate-900">Ringkasan per Kategori</h3>
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($monthlyData['warehouse_categories'] as $category)
                            <div class="flex items-center justify-between rounded-lg border border-slate-200 p-3">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ $category['category'] }}</p>
                                    <p class="text-xs text-slate-600">{{ $category['count'] }} barang</p>
                                </div>
                                <p class="text-lg font-bold text-slate-900">{{ $category['total_stock'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif

    <!-- Download Section -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold text-slate-900">Unduh Laporan</h2>

        <div class="grid gap-3 sm:grid-cols-2">
            <form method="POST" action="{{ route('dashboard.manager.reports.export') }}" class="flex flex-col gap-2">
                @csrf
                <p class="text-sm font-medium text-slate-700">Laporan Bulan {{ $monthlyData['month_name'] }}
                    {{ $monthlyData['year'] }}</p>
                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                <button type="submit" name="export_type" value="pdf"
                    class="rounded-lg bg-rose-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">
                    Unduh Laporan Bulanan (PDF)
                </button>
            </form>

            <form method="POST" action="{{ route('dashboard.manager.reports.export') }}" class="flex flex-col gap-2">
                @csrf
                <p class="text-sm font-medium text-slate-700">Laporan Tahunan {{ $monthlyData['year'] }}</p>
                <input type="hidden" name="month" value="all">
                <input type="hidden" name="year" value="{{ $selectedYear }}">
                <button type="submit" name="export_type" value="pdf"
                    class="rounded-lg bg-violet-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-violet-700">
                    Unduh Laporan Tahunan (PDF)
                </button>
            </form>
        </div>
    </section>

    <!-- Instructor Salary Expense Report -->
    <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold text-slate-900">Laporan Pengeluaran Gaji Pengajar -
            {{ $monthlyData['month_name'] }}
            {{ $monthlyData['year'] }}</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-200">
                    <tr class="text-left">
                        <th class="px-3 py-3 font-semibold text-slate-600">Tipe</th>
                        <th class="px-3 py-3 font-semibold text-slate-600">Nama Pengajar</th>
                        <th class="px-3 py-3 font-semibold text-slate-600">Tanggal</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Status</th>
                        <th class="px-3 py-3 font-semibold text-slate-600 text-right">Gaji</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($monthlyData['instructor_salaries'] as $salary)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $salary['type'] ?? 'Pengajar' }}</td>
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $salary['name'] }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $salary['date'] ?? '-' }}</td>
                            <td class="px-3 py-3 text-right">
                                <span
                                    class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $salary['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $salary['status'] }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-right font-bold text-slate-900">Rp
                                {{ number_format($salary['salary'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-sm text-slate-500">Tidak ada data
                                tersedia.
                            </td>
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($allMonthsData as $monthData)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $monthData['month_name'] }}</td>
                            <td class="px-3 py-3 text-right text-slate-900">
                                {{ $monthData['total_registrations'] !== null ? $monthData['total_registrations'] . ' peserta' : '-' }}
                            </td>
                            <td class="px-3 py-3 text-right font-medium text-slate-900">
                                {{ $monthData['total_profit'] !== null ? 'Rp ' . number_format($monthData['total_profit'], 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-sm text-slate-500">Tidak ada data
                                tersedia.
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
