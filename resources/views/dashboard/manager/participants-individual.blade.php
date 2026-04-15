@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Validasi Peserta Individu</h2>
                <p class="text-xs text-slate-500 sm:text-sm">Pendaftar baru otomatis masuk ke daftar ini untuk divalidasi
                    admin.</p>
            </div>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 sm:text-sm">Kembali
                ke dashboard</a>
        </div>

        <div class="mt-5 space-y-4">
            @forelse ($pendingParticipants as $participant)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $participant['name'] }}</p>
                            <p class="text-xs text-slate-500">Registrasi: {{ $participant['registration_date'] }}</p>
                        </div>
                        <span
                            class="w-fit rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Menunggu
                            Validasi</span>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-xs sm:text-sm">
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <th class="w-40 bg-slate-50 px-3 py-2 font-semibold text-slate-600">Nama Lengkap</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $participant['name'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Email</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $participant['email'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">No. Handphone</th>
                                    <td class="px-3 py-2 text-slate-800">
                                        {{ $participant['phone'] ?? ($participant['whatsapp'] ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Alamat</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $participant['address'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Pendidikan Terakhir</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $participant['education'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Motivasi Singkat</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $participant['motivation'] }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-slate-50 px-3 py-2 font-semibold text-slate-600">Program</th>
                                    <td class="px-3 py-2 text-slate-800">{{ $participant['program'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <form method="POST"
                            action="{{ route('dashboard.manager.participants.individual.generate-credential', ['participant' => $participant['id']]) }}">
                            @csrf
                            <button type="submit"
                                class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-700 sm:text-sm">Validasi
                                dan Generate Akun</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-700">
                    Tidak ada pendaftar baru yang menunggu validasi.
                </div>
            @endforelse
        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <h3 class="text-base font-bold text-slate-900 sm:text-lg">Pembuatan Username dan Password</h3>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Gunakan kartu ini untuk meninjau hasil generate akun, lalu kirim
            lewat WhatsApp untuk login pertama.</p>

        @if ($errors->has('credential'))
            <div
                class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 sm:text-sm">
                {{ $errors->first('credential') }}
            </div>
        @endif

        @if ($generatedCredential)
            <article class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="grid gap-3 text-xs sm:grid-cols-2 sm:text-sm">
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-slate-500">Peserta</p>
                        <p class="font-semibold text-slate-900">{{ $generatedCredential['participant_name'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-slate-500">WhatsApp</p>
                        <p class="font-semibold text-slate-900">{{ $generatedCredential['participant_whatsapp'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-slate-500">Username Acak</p>
                        <p class="font-semibold text-slate-900">{{ $generatedCredential['username'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-slate-500">Password Acak</p>
                        <p class="font-semibold text-slate-900">{{ $generatedCredential['password'] }}</p>
                    </div>
                </div>

                <form method="POST"
                    action="{{ route('dashboard.manager.participants.individual.send-credential', ['participant' => $generatedCredential['participant_id']]) }}"
                    class="mt-4 flex justify-end">
                    @csrf
                    <input type="hidden" name="username" value="{{ $generatedCredential['username'] }}">
                    <input type="hidden" name="password" value="{{ $generatedCredential['password'] }}">
                    <button type="submit"
                        class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700 sm:text-sm">Kirim
                        Kredensial via WhatsApp</button>
                </form>
            </article>
        @else
            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600 sm:text-sm">
                Belum ada akun yang digenerate. Klik tombol Validasi dan Generate Akun pada data pendaftar.
            </div>
        @endif
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <h3 class="text-base font-bold text-slate-900 sm:text-lg">Kelola Peserta Individu</h3>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Setelah kredensial terkirim, data peserta masuk ke sini dan status
            bisa diubah.</p>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-3 py-3">Nama</th>
                        <th class="px-3 py-3">Program</th>
                        <th class="px-3 py-3">Progres</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($participants as $participant)
                        <tr>
                            <td class="px-3 py-3 font-semibold text-slate-900">{{ $participant['name'] }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $participant['program'] }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $participant['progress'] }}%</td>
                            <td class="px-3 py-3">
                                <span
                                    class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $participant['status_label'] ?? $participant['status'] }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <form method="POST"
                                    action="{{ route('dashboard.manager.participants.individual.update', ['participant' => $participant['id']]) }}"
                                    class="flex flex-col gap-2 sm:flex-row">
                                    @csrf
                                    <select name="status"
                                        class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-700">
                                        <option value="active" @selected(($participant['status'] ?? 'active') === 'active')>active</option>
                                        <option value="graduated" @selected(($participant['status'] ?? '') === 'graduated')>graduated</option>
                                        <option value="non-active" @selected(($participant['status'] ?? '') === 'non-active')>non-active</option>
                                    </select>
                                    <label
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-700">
                                        <input type="hidden" name="forgot_password_enabled" value="0">
                                        <input type="checkbox" name="forgot_password_enabled" value="1"
                                            @checked(($participant['forgot_password_enabled'] ?? false) === true)>
                                        Forgot Password
                                    </label>
                                    <button type="submit"
                                        class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
