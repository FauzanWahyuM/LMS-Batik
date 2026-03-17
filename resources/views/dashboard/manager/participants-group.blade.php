@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Data Peserta Kelompok</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali
                ke dashboard</a>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($groups as $group)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="text-sm font-bold text-slate-900">{{ $group['group_name'] }}</h3>
                    <p class="mt-1 text-xs text-slate-500">Program: {{ $group['program'] }}</p>
                    <p class="mt-2 text-sm text-slate-700">Anggota: <span
                            class="font-semibold">{{ $group['members'] }}</span></p>
                    <p class="mt-1 text-sm text-slate-700">Status: <span class="font-semibold">{{ $group['status'] }}</span>
                    </p>

                    <form method="POST"
                        action="{{ route('dashboard.manager.participants.group.update', ['group' => $group['id']]) }}"
                        class="mt-4 flex items-center gap-2">
                        @csrf
                        <select name="status"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
                            <option value="Aktif" @selected($group['status'] === 'Aktif')>Aktif</option>
                            <option value="Perlu Verifikasi" @selected($group['status'] === 'Perlu Verifikasi')>Perlu Verifikasi</option>
                            <option value="Selesai" @selected($group['status'] === 'Selesai')>Selesai</option>
                        </select>
                        <button type="submit"
                            class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                    </form>
                </article>
            @endforeach
        </div>
    </section>
@endsection
