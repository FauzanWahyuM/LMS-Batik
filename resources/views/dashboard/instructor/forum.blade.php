@extends('dashboard.layouts.app')

@section('dashboard-content')
    <section class="grid gap-6 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="text-lg font-bold text-slate-900">Thread Diskusi</h2>
            <div class="mt-4 space-y-3">
                @foreach ($threads as $thread)
                    <a href="{{ route('dashboard.instructor.forum', ['thread' => $thread['id']]) }}"
                        class="block rounded-xl border p-4 transition {{ $selectedThread === $thread['id'] ? 'border-sky-300 bg-sky-50' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $thread['title'] }}</h3>
                                <p class="mt-1 text-xs text-slate-500">Oleh {{ $thread['author'] }}</p>
                            </div>
                            <span class="text-xs text-slate-500">{{ $thread['replies'] }} balasan</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">{{ $thread['excerpt'] }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-500">Aktivitas terakhir: {{ $thread['last_message'] }}
                        </p>
                    </a>
                @endforeach
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            @php
                $activeThread = collect($threads)->firstWhere('id', $selectedThread) ?? $threads[0];
            @endphp
            <h2 class="text-lg font-bold text-slate-900">Balas Diskusi</h2>
            <p class="mt-3 text-sm font-semibold text-slate-800">{{ $activeThread['title'] }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $activeThread['excerpt'] }}</p>

            <form method="POST" action="{{ route('dashboard.instructor.forum.reply', ['thread' => $activeThread['id']]) }}"
                class="mt-4 space-y-3">
                @csrf
                <textarea name="reply" rows="5" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-slate-500 focus:outline-none"
                    placeholder="Tulis balasan untuk peserta...">{{ old('reply') }}</textarea>
                <button type="submit"
                    class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Kirim
                    Balasan</button>
            </form>
        </article>
    </section>
@endsection
