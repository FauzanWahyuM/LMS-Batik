@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">Manajemen Program</h2>
            <a href="{{ route('dashboard.manager.home') }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Kembali
                ke dashboard</a>
        </div>

        <div class="mt-4 space-y-3">
            @foreach ($programs as $program)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $program['title'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Kuota {{ $program['quota'] }} · Terdaftar
                                {{ $program['enrolled'] }}</p>
                        </div>
                        <form method="POST"
                            action="{{ route('dashboard.manager.programs.update', ['program' => $program['id']]) }}"
                            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                            @csrf
                            <select name="status"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
                                <option value="Aktif" @selected($program['status'] === 'Aktif')>Aktif</option>
                                <option value="Draf" @selected($program['status'] === 'Draf')>Draf</option>
                                <option value="Ditutup" @selected($program['status'] === 'Ditutup')>Ditutup</option>
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
