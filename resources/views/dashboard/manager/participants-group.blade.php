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
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Surat Resmi</th>
                                    <td class="px-3 py-2 text-blue-600 underline">{{ $group['official_letter'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <form method="POST"
                            action="{{ route('dashboard.manager.participants.group.generate-credential', ['group' => $group['id']]) }}">
                            @csrf
                            <button type="submit"
                                class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-700 sm:text-sm">Validasi
                                dan Generate Kredensial</button>
                        </form>
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

        @if ($groupExportMeta)
            <div
                class="mt-4 flex flex-col gap-2 rounded-lg border border-blue-200 bg-blue-50 p-3 text-xs sm:flex-row sm:items-center sm:justify-between sm:text-sm">
                <p class="text-blue-800">File export terakhir: <span
                        class="font-semibold">{{ $groupExportMeta['filename'] }}</span>
                    untuk {{ $groupExportMeta['group_name'] }}.</p>
                <a href="{{ route('dashboard.manager.participants.group.download-export') }}"
                    class="w-fit rounded-lg bg-blue-600 px-3 py-2 font-semibold text-white hover:bg-blue-700">Download
                    Export</a>
            </div>
        @endif
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <h3 class="text-base font-bold text-slate-900 sm:text-lg">Kelola Peserta Kelompok</h3>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Data kelompok yang sudah menerima kredensial akan tampil di sini.
        </p>

        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($groups as $group)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="text-sm font-bold text-slate-900">{{ $group['group_name'] }}</h3>
                    <p class="mt-1 text-xs text-slate-500">Program: {{ $group['program'] }}</p>
                    <p class="mt-2 text-sm text-slate-700">Anggota: <span
                            class="font-semibold">{{ $group['members'] }}</span></p>
                    <p class="mt-1 text-sm text-slate-700">Status: <span
                            class="font-semibold">{{ $group['status'] }}</span></p>

                    <form method="POST"
                        action="{{ route('dashboard.manager.participants.group.update', ['group' => $group['id']]) }}"
                        class="mt-4 flex items-center gap-2">
                        @csrf
                        <select name="status"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
                            <option value="Aktif" @selected($group['status'] === 'Aktif')>Aktif</option>
                            <option value="Lulus" @selected($group['status'] === 'Lulus')>Lulus</option>
                            <option value="Nonaktif" @selected($group['status'] === 'Nonaktif')>Nonaktif</option>
                        </select>
                        <button type="submit"
                            class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                    </form>
                </article>
            @endforeach
        </div>
    </section>
@endsection
