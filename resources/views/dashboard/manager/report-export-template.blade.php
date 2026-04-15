<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $reportData['title'] ?? 'Laporan' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 24px;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 16px;
            text-align: center;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            text-align: center;
        }

        .logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .org-name {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 4px;
        }

        .muted {
            color: #4b5563;
            font-size: 11px;
            line-height: 1.5;
            text-align: center;
        }

        .report-title {
            margin-top: 16px;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .summary td {
            border: 1px solid #d1d5db;
            padding: 7px 8px;
            text-align: center;
        }

        .summary .label {
            width: 38%;
            background: #f8fafc;
            font-weight: 600;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #d1d5db;
            padding: 7px 8px;
            text-align: center;
        }

        table.data th {
            background: #1e3a8a;
            color: white;
            font-size: 11px;
        }

        .empty {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 70px;">
                    @if (!empty($reportData['branding']['logo_data_uri']))
                        <img src="{{ $reportData['branding']['logo_data_uri'] }}" alt="Logo" class="logo">
                    @endif
                </td>
                <td>
                    <div class="org-name">{{ $reportData['branding']['organization_name'] ?? 'LPK Kama Praja Madiun' }}
                    </div>
                    <div class="muted">Alamat: Jl.Candi Boko No.9, Patihan, Manguhardjo, Kota Madiun</div>
                    <div class="muted">No. Telepon: +62 851-2425-5339</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">{{ $reportData['title'] ?? 'Laporan' }}</div>

    <table class="summary">
        @forelse ($reportData['summary'] ?? [] as $label => $value)
            <tr>
                <td class="label">{{ $label }}</td>
                <td>
                    @if (is_int($value))
                        {{ number_format($value, 0, ',', '.') }}
                    @else
                        {{ $value }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td class="label">Informasi</td>
                <td>Tidak ada data tersedia.</td>
            </tr>
        @endforelse
    </table>

    <table class="data">
        @if (($reportData['mode'] ?? '') === 'annual')
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Total Pendaftaran</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse (($reportData['rows'] ?? []) as $row)
                    <tr>
                        <td>{{ $row['month'] ?? '-' }}</td>
                        <td>{{ $row['registrations'] ?? 0 }}</td>
                        <td>Rp {{ number_format((int) ($row['revenue'] ?? 0), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty">Tidak ada data tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        @else
            <thead>
                <tr>
                    <th>Tipe</th>
                    <th>Nama</th>
                    <th>Program</th>
                    <th>Tanggal</th>
                    <th>Anggota</th>
                    <th>Status</th>
                    <th>Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse (($reportData['rows'] ?? []) as $row)
                    <tr>
                        <td>{{ $row['type'] ?? '-' }}</td>
                        <td>{{ $row['name'] ?? '-' }}</td>
                        <td>{{ $row['program'] ?? '-' }}</td>
                        <td>{{ $row['date'] ?? '-' }}</td>
                        <td>{{ $row['members'] ?? '-' }}</td>
                        <td>{{ $row['status'] ?? '-' }}</td>
                        <td>Rp {{ number_format((int) ($row['revenue'] ?? 0), 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">Tidak ada data tersedia.</td>
                    </tr>
                @endforelse
            </tbody>
        @endif
    </table>
</body>

</html>
