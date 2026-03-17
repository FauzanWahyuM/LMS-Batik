@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Manajemen Pengajar</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali
                ke dashboard</a>
        </div>

        <div class="mt-4 space-y-3">
            @foreach ($instructors as $instructor)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $instructor['name'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Keahlian: {{ $instructor['specialty'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Kelas aktif: {{ $instructor['active_classes'] }}</p>
                        </div>
                        <form method="POST"
                            action="{{ route('dashboard.manager.instructors.update', ['instructor' => $instructor['id']]) }}"
                            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                            @csrf
                            <select name="status"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
                                <option value="Aktif" @selected($instructor['status'] === 'Aktif')>Aktif</option>
                                <option value="Cuti" @selected($instructor['status'] === 'Cuti')>Cuti</option>
                                <option value="Nonaktif" @selected($instructor['status'] === 'Nonaktif')>Nonaktif</option>
                            </select>
                            <button type="submit"
                                class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Simpan</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
