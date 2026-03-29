@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-1 gap-2">
                <input type="text" placeholder="Cari judul karya"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <button
                    class="rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-300 transition">Cari</button>
            </div>
            <a href="{{ route('dashboard.participant.gallery.upload') }}"
                class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 transition">Upload
                Karya</a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @for ($i = 1; $i <= 6; $i++)
                <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div
                        class="h-36 rounded-lg border border-dashed border-slate-300 bg-slate-50 grid place-items-center text-slate-500">
                        Gambar</div>
                    <h3 class="mt-3 text-lg font-bold text-slate-800">Judul</h3>
                    <p class="mt-1 text-sm text-slate-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                        eiusmod tempor incididunt...</p>
                    <div class="mt-3 flex justify-end">
                        <span
                            class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700">Pembuat</span>
                    </div>
                </article>
            @endfor
        </div>
    </section>
@endsection
