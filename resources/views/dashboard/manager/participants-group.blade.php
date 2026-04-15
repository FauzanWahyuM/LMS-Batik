@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Data Validasi Kelompok</h2>
                <p class="text-xs text-slate-500 sm:text-sm">Pendaftaran kelompok baru otomatis masuk daftar validasi admin.
                </p>
            </div>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 sm:text-sm">Kembali
                ke dashboard</a>
        </div>

        <div class="mt-5 space-y-4">
            @forelse ($pendingGroups as $group)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $group['group_name'] }}</p>
                            <p class="text-xs text-slate-500">Tanggal daftar: {{ $group['registration_date'] }}</p>
                        </div>
                        <span
                            class="w-fit rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Menunggu
                            Validasi</span>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <th class="w-48 bg-slate-50 px-3 py-2 font-semibold text-slate-600">Nama Lembaga</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $group['group_name'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Alamat PIC</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $group['pic_address'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Email Resmi Lembaga/PIC
                                    </th>
                                    <td class="px-3 py-2 text-slate-800">{{ $group['pic_email'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">No. Handphone PIC</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $group['pic_phone'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Nama PIC</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $group['pic_name'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Jumlah Peserta</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $group['members'] }}</td>
                                </tr>
                                @if (($group['members'] ?? 0) > 20)
                                    <tr>
                                        <td colspan="2" class="px-3 py-2 text-sm font-medium text-amber-700">
                                            If you register more than 20 participants, credentials will be sent manually for
                                            efficiency. Please wait 1–5 minutes.
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Surat Resmi</th>
                                    <td class="px-3 py-2">
                                        @if (!empty($group['official_letter_path']))
                                            <a href="{{ asset('storage/' . $group['official_letter_path']) }}"
                                                target="_blank"
                                                class="text-blue-600 underline">{{ $group['official_letter'] }}</a>
                                        @else
                                            <span class="text-slate-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <form method="POST"
                                action="{{ route('dashboard.manager.participants.group.generate-credential', ['group' => $group['id']]) }}">
                                @csrf
                                <button type="submit"
                                    class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-700 sm:text-sm">Validasi
                                    dan Generate Kredensial</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-700">
                    Tidak ada pendaftaran kelompok yang menunggu validasi.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <h3 class="text-base font-bold text-slate-900 sm:text-lg">Pembuatan Username dan Password</h3>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Generate akun acak per anggota, export sebagai file Excel (CSV),
            lalu kirim ke PIC.</p>

        @if ($errors->has('group_credential'))
            <div
                class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 sm:text-sm">
                {{ $errors->first('group_credential') }}
            </div>
        @endif

        @if ($generatedGroupCredential)
            <article class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="grid gap-3 text-xs sm:grid-cols-2 sm:text-sm">
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-slate-500">Lembaga</p>
                        <p class="font-semibold text-slate-900">{{ $generatedGroupCredential['group_name'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-slate-500">PIC</p>
                        <p class="font-semibold text-slate-900">{{ $generatedGroupCredential['pic_name'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-slate-500">WhatsApp PIC</p>
                        <p class="font-semibold text-slate-900">{{ $generatedGroupCredential['pic_whatsapp'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-slate-500">Email PIC</p>
                        <p class="font-semibold text-slate-900">{{ $generatedGroupCredential['pic_email'] }}</p>
                    </div>
                </div>

                <div class="mt-4 overflow-x-auto rounded-lg border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600">
                                <th class="px-3 py-2 font-semibold">No Anggota</th>
                                <th class="px-3 py-2 font-semibold">Username</th>
                                <th class="px-3 py-2 font-semibold">Password</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($generatedGroupCredential['credentials'] as $credential)
                                <tr>
                                    <td class="px-3 py-2 text-slate-800">{{ $credential['member_no'] }}</td>
                                    <td class="px-3 py-2 text-slate-800">{{ $credential['username'] }}</td>
                                    <td class="px-3 py-2 text-slate-800">{{ $credential['password'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <form method="POST"
                    action="{{ route('dashboard.manager.participants.group.send-credential', ['group' => $generatedGroupCredential['group_id']]) }}"
                    class="mt-4 flex justify-end">
                    @csrf
                    <button type="submit"
                        class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700 sm:text-sm">Export
                        Excel dan Kirim ke PIC</button>
                </form>
            </article>
        @else
            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600 sm:text-sm">
                Belum ada kredensial kelompok yang digenerate.
            </div>
        @endif

    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 sm:text-lg">Unduh Excel Data Kelompok</h3>
                <p class="mt-1 text-xs text-slate-500 sm:text-sm">Ringkasan data grup berikut selalu diambil dari database
                    dan dapat diunduh per grup.</p>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-3 py-2 font-semibold">Lembaga</th>
                        <th class="px-3 py-2 font-semibold">Peserta</th>
                        <th class="px-3 py-2 font-semibold">Status Password</th>
                        <th class="px-3 py-2 font-semibold">Unduh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($groupPaginator as $group)
                        <tr>
                            <td class="px-3 py-2 text-slate-800">
                                <div class="font-medium">{{ $group['group_name'] }}</div>
                                <div class="text-xs text-slate-500">PIC: {{ $group['pic_name'] ?? '-' }}</div>
                            </td>
                            <td class="px-3 py-2 text-slate-800">{{ $group['members'] }}</td>
                            <td class="px-3 py-2 text-slate-800">{{ $group['password_change_summary'] ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <a href="{{ route('dashboard.manager.participants.group.download-export', ['group' => $group['id']]) }}"
                                    class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100 sm:text-sm">
                                    Unduh Excel
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-slate-500">Belum ada data kelompok untuk diunduh.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($groupPaginator->hasPages())
            <div
                class="mt-4 flex flex-col gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:text-sm">
                <span>
                    Menampilkan {{ $groupPaginator->firstItem() }}-{{ $groupPaginator->lastItem() }} dari
                    {{ $groupPaginator->total() }} grup
                </span>
                <div class="inline-flex items-center gap-2">
                    <a href="{{ $groupPaginator->previousPageUrl() ?? '#' }}"
                        class="inline-flex items-center rounded-md border border-slate-200 px-3 py-1.5 font-semibold {{ $groupPaginator->onFirstPage() ? 'pointer-events-none cursor-default text-slate-300' : 'text-slate-700 hover:bg-slate-100' }}"
                        aria-label="Previous page"
                        @if ($groupPaginator->onFirstPage()) aria-disabled="true" tabindex="-1" @endif>
                        &larr;
                    </a>
                    <span class="rounded-md bg-slate-50 px-3 py-1.5 font-semibold text-slate-700">
                        {{ $groupPaginator->currentPage() }} / {{ $groupPaginator->lastPage() }}
                    </span>
                    <a href="{{ $groupPaginator->nextPageUrl() ?? '#' }}"
                        class="inline-flex items-center rounded-md border border-slate-200 px-3 py-1.5 font-semibold {{ $groupPaginator->hasMorePages() ? 'text-slate-700 hover:bg-slate-100' : 'pointer-events-none cursor-default text-slate-300' }}"
                        aria-label="Next page" @if (!$groupPaginator->hasMorePages()) aria-disabled="true" tabindex="-1" @endif>
                        &rarr;
                    </a>
                </div>
            </div>
        @endif
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <h3 class="text-base font-bold text-slate-900 sm:text-lg">Kelola Peserta Kelompok</h3>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Data lembaga yang sudah tervalidasi akan tampil di sini.
        </p>

        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($groupPaginator as $group)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="text-sm font-bold text-slate-900">{{ $group['group_name'] }}</h3>
                    <p class="mt-1 text-sm text-slate-700">Nama PIC: <span
                            class="font-semibold">{{ $group['pic_name'] ?? '-' }}</span></p>
                    <p class="mt-1 text-sm text-slate-700">No. HP PIC: <span
                            class="font-semibold">{{ $group['pic_phone'] ?? '-' }}</span></p>
                    <p class="mt-1 text-sm text-slate-700">Total Peserta: <span
                            class="font-semibold">{{ $group['members'] }}</span></p>
                    <p class="mt-1 text-sm text-slate-700">Status: <span
                            class="font-semibold">{{ $group['status_label'] ?? $group['status'] }}</span></p>
                    <p class="mt-1 text-sm text-slate-700">Password Peserta: <span
                            class="font-semibold">{{ $group['password_change_summary'] ?? '-' }}</span></p>

                    <button type="button" data-toggle="group-detail-{{ $group['id'] }}"
                        class="mt-3 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Lihat
                        Detail Akun</button>

                    <div id="group-detail-{{ $group['id'] }}"
                        class="mt-3 hidden overflow-x-auto rounded-lg border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-slate-600">
                                    <th class="px-3 py-2 font-semibold">Username Peserta</th>
                                    <th class="px-3 py-2 font-semibold">Password Saat Ini</th>
                                    <th class="px-3 py-2 font-semibold">Status Password</th>
                                </tr>
                            </thead>
                            @php
                                $initialCredentials = array_slice($group['participant_credentials'] ?? [], 0, 5);
                            @endphp
                            <tbody id="group-detail-body-{{ $group['id'] }}" class="divide-y divide-slate-100">
                                @forelse ($initialCredentials as $credential)
                                    <tr>
                                        <td class="px-3 py-2 text-slate-800">{{ $credential['username'] ?? '-' }}</td>
                                        <td class="px-3 py-2 text-slate-800">{{ $credential['current_password'] ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-slate-800">{{ $credential['password_status'] ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-2 text-slate-500">Tidak ada data akun peserta.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <script type="application/json" id="group-detail-data-{{ $group['id'] }}">
                        @json($group['participant_credentials'] ?? [])
                    </script>

                    <div class="mt-3 flex items-center justify-between gap-2 text-xs text-slate-500 sm:text-sm"
                        data-detail-pagination data-group-id="{{ $group['id'] }}">
                        <span data-detail-range>0-0 dari 0 akun</span>
                        <div class="inline-flex items-center gap-2">
                            <button type="button" data-detail-prev aria-label="Previous page"
                                class="inline-flex items-center rounded-md border border-slate-200 px-2.5 py-1 font-semibold text-slate-700 hover:bg-slate-100">
                                &larr;
                            </button>
                            <span data-detail-page class="rounded-md bg-slate-50 px-2.5 py-1 font-semibold text-slate-700">
                                1 / 1
                            </span>
                            <button type="button" data-detail-next aria-label="Next page"
                                class="inline-flex items-center rounded-md border border-slate-200 px-2.5 py-1 font-semibold text-slate-700 hover:bg-slate-100">
                                &rarr;
                            </button>
                        </div>
                    </div>

                    <form method="POST"
                        action="{{ route('dashboard.manager.participants.group.update', ['group' => $group['id']]) }}"
                        class="mt-4 flex flex-col gap-2">
                        @csrf
                        <div class="flex items-center gap-2">
                            <select name="status"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
                                <option value="active" @selected(($group['status'] ?? '') === 'active')>Aktif</option>
                                <option value="graduated" @selected(($group['status'] ?? '') === 'graduated')>Lulus</option>
                                <option value="non-active" @selected(($group['status'] ?? '') === 'non-active')>Nonaktif</option>
                            </select>
                            <button type="submit"
                                class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                        </div>
                    </form>
                </article>
            @empty
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                    Belum ada lembaga tervalidasi.
                </div>
            @endforelse
        </div>

    </section>

    <script>
        (function() {
            const toggles = document.querySelectorAll('[data-toggle]');
            toggles.forEach(function(button) {
                button.addEventListener('click', function() {
                    const targetId = button.getAttribute('data-toggle');
                    const target = document.getElementById(targetId);
                    if (target) {
                        target.classList.toggle('hidden');
                    }
                });
            });

            const detailPageSize = 5;
            const detailPaginators = document.querySelectorAll('[data-detail-pagination]');

            const renderDetailRows = function(groupId, credentials, currentPage, container) {
                const tbody = document.getElementById('group-detail-body-' + groupId);
                if (!tbody) {
                    return;
                }

                const totalItems = credentials.length;
                const totalPages = Math.max(1, Math.ceil(totalItems / detailPageSize));
                const safePage = Math.min(Math.max(1, currentPage), totalPages);
                const startIndex = (safePage - 1) * detailPageSize;
                const endIndex = Math.min(startIndex + detailPageSize, totalItems);
                const pageItems = credentials.slice(startIndex, endIndex);

                if (pageItems.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="3" class="px-3 py-2 text-slate-500">Tidak ada data akun peserta.</td></tr>';
                } else {
                    tbody.innerHTML = pageItems.map(function(item) {
                        const username = item.username || '-';
                        const currentPassword = item.current_password || '-';
                        const passwordStatus = item.password_status || '-';

                        return '<tr>' +
                            '<td class="px-3 py-2 text-slate-800">' + username + '</td>' +
                            '<td class="px-3 py-2 text-slate-800">' + currentPassword + '</td>' +
                            '<td class="px-3 py-2 text-slate-800">' + passwordStatus + '</td>' +
                            '</tr>';
                    }).join('');
                }

                const range = container.querySelector('[data-detail-range]');
                const page = container.querySelector('[data-detail-page]');
                const prev = container.querySelector('[data-detail-prev]');
                const next = container.querySelector('[data-detail-next]');

                if (range) {
                    const from = totalItems === 0 ? 0 : startIndex + 1;
                    const to = totalItems === 0 ? 0 : endIndex;
                    range.textContent = from + '-' + to + ' dari ' + totalItems + ' akun';
                }

                if (page) {
                    page.textContent = safePage + ' / ' + totalPages;
                }

                if (prev) {
                    const disablePrev = safePage <= 1;
                    prev.disabled = disablePrev;
                    prev.classList.toggle('pointer-events-none', disablePrev);
                    prev.classList.toggle('cursor-default', disablePrev);
                    prev.classList.toggle('text-slate-300', disablePrev);
                    prev.classList.toggle('text-slate-700', !disablePrev);
                }

                if (next) {
                    const disableNext = safePage >= totalPages;
                    next.disabled = disableNext;
                    next.classList.toggle('pointer-events-none', disableNext);
                    next.classList.toggle('cursor-default', disableNext);
                    next.classList.toggle('text-slate-300', disableNext);
                    next.classList.toggle('text-slate-700', !disableNext);
                }

                container.dataset.currentPage = String(safePage);
                container.classList.toggle('hidden', totalItems <= detailPageSize);
            };

            detailPaginators.forEach(function(container) {
                const groupId = container.getAttribute('data-group-id');
                const dataNode = document.getElementById('group-detail-data-' + groupId);
                if (!groupId || !dataNode) {
                    return;
                }

                let credentials = [];
                try {
                    credentials = JSON.parse(dataNode.textContent || '[]');
                } catch (error) {
                    credentials = [];
                }

                renderDetailRows(groupId, credentials, 1, container);

                const prev = container.querySelector('[data-detail-prev]');
                const next = container.querySelector('[data-detail-next]');

                if (prev) {
                    prev.addEventListener('click', function() {
                        const currentPage = Number(container.dataset.currentPage || '1');
                        renderDetailRows(groupId, credentials, currentPage - 1, container);
                    });
                }

                if (next) {
                    next.addEventListener('click', function() {
                        const currentPage = Number(container.dataset.currentPage || '1');
                        renderDetailRows(groupId, credentials, currentPage + 1, container);
                    });
                }
            });
        })();
    </script>
@endsection
