@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Daftar Modul</h2>
            <a href="{{ route('dashboard.instructor.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali
                ke dashboard</a>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($modules as $module)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ $module['title'] }}</h3>
                            <p class="mt-1 text-xs text-slate-500">Kategori: {{ $module['category'] }}</p>
                        </div>
                        <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">
                            {{ $module['status'] }}
                        </span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-600">
                        <p>Pertemuan: <span class="font-semibold text-slate-800">{{ $module['lessons'] }}</span></p>
                        <p>Peserta: <span class="font-semibold text-slate-800">{{ $module['participants'] }}</span></p>
                        <p class="col-span-2">Update terakhir: <span
                                class="font-semibold text-slate-800">{{ $module['updated_at'] }}</span></p>
                    </div>

                    <form method="POST"
                        action="{{ route('dashboard.instructor.modules.update', ['module' => $module['id']]) }}"
                        class="mt-4 flex items-center gap-2">
                        @csrf
                        <select name="status"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none">
                            <option value="Draf" @selected($module['status'] === 'Draf')>Draf</option>
                            <option value="Aktif" @selected($module['status'] === 'Aktif')>Aktif</option>
                            <option value="Revisi" @selected($module['status'] === 'Revisi')>Revisi</option>
                        </select>
                        <button type="submit"
                            class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                    </form>
                </article>
            @endforeach
        </div>
    </section>
@endsection
