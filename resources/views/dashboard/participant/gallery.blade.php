@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @if (session('status'))
            <div
                class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('dashboard.participant.gallery') }}" class="flex flex-1 gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari judul karya"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                <button type="submit"
                    class="rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-300 transition">Cari</button>
            </form>
            <a href="{{ route('dashboard.participant.gallery.upload') }}"
                class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 transition">Upload
                Karya</a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse (($artworks ?? collect()) as $artwork)
                <article class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="h-36 rounded-lg overflow-hidden bg-slate-100">
                        <img src="{{ route('public-file', ['path' => ltrim($artwork->image_path, '/')]) }}"
                            alt="{{ $artwork->title }}" class="h-full w-full object-cover">
                    </div>
                    <h3 class="mt-3 text-lg font-bold text-slate-800">{{ $artwork->title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ \Illuminate\Support\Str::limit($artwork->description, 140) }}
                    </p>
                    <div class="mt-3 flex justify-end">
                        <span
                            class="rounded-full border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-700">{{ $artwork->creator_name }}</span>
                    </div>
                </article>
            @empty
                <div
                    class="col-span-full rounded-xl border border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-600">
                    Belum ada karya yang diunggah.
                </div>
            @endforelse
        </div>
    </section>
@endsection
