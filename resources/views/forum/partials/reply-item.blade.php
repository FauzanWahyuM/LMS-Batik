@php
    $roleValue = strtolower((string) ($reply->user_role ?? 'peserta'));
    $isReplyInstructor = in_array($roleValue, ['pengajar', 'instructor', 'teacher'], true);
    $replyRoleText = $isReplyInstructor ? 'Pengajar' : 'Peserta';
@endphp

<div class="rounded-lg border border-slate-200 bg-white p-3">
    <div class="flex items-start justify-between gap-2">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <p class="font-semibold text-slate-900">{{ $reply->user_name }}</p>
                <span
                    class="rounded px-2 py-1 text-xs font-semibold {{ $isReplyInstructor ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ $replyRoleText }}
                </span>
            </div>
            <p class="mt-1 text-xs text-slate-500">{{ $reply->created_at->diffForHumans() }}</p>
            <p class="mt-2 text-sm text-slate-700">{{ $reply->content }}</p>
        </div>
    </div>

    @if ($user && !$discussion->is_closed)
        <div class="mt-3 flex flex-wrap gap-3">
            <button type="button" data-toggle-child-reply="{{ $reply->id }}"
                class="text-xs font-semibold text-slate-700 hover:text-slate-900">
                Balas
            </button>

            @if (($user['email'] ?? null) === $reply->user_email || $isCurrentUserInstructor)
                <button type="button" data-edit-reply="{{ $reply->id }}"
                    class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                    Edit
                </button>

                <form method="POST" action="{{ route('forum.reply.delete', ['reply' => $reply->id]) }}"
                    onsubmit="return confirm('Hapus balasan ini?')" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                        Hapus
                    </button>
                </form>
            @endif
        </div>

        <div id="child-reply-form-{{ $reply->id }}"
            class="mt-3 hidden rounded-lg border border-slate-300 bg-slate-50 p-3">
            <form method="POST" action="{{ route('forum.reply', ['discussion' => $discussion->id]) }}"
                class="space-y-2">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                <label class="block text-xs font-semibold text-slate-700">Balas {{ $reply->user_name }}</label>
                <textarea name="content" rows="3" required
                    class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm focus:border-slate-500 focus:outline-none"
                    placeholder="Tulis balasan Anda..."></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" data-cancel-child-reply="{{ $reply->id }}"
                        class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-slate-900 px-2 py-1 text-xs font-semibold text-white hover:bg-slate-700">
                        Kirim Balasan
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if ($user && (($user['email'] ?? null) === $reply->user_email || $isCurrentUserInstructor))
        <div id="edit-reply-{{ $reply->id }}"
            class="mt-3 hidden rounded-lg border border-slate-300 bg-slate-50 p-3">
            <form method="POST" action="{{ route('forum.reply.update', ['reply' => $reply->id]) }}" class="space-y-2">
                @csrf
                <textarea name="content" rows="3" required
                    class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm focus:border-slate-500 focus:outline-none">{{ $reply->content }}</textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" data-cancel-reply-edit="{{ $reply->id }}"
                        class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-slate-900 px-2 py-1 text-xs font-semibold text-white hover:bg-slate-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if ($reply->children && $reply->children->count())
        <div class="mt-3 space-y-3 border-l-2 border-slate-200 pl-4">
            @foreach ($reply->children as $reply)
                @include('forum.partials.reply-item', [
                    'reply' => $reply,
                    'discussion' => $discussion,
                    'user' => $user,
                    'isCurrentUserInstructor' => $isCurrentUserInstructor,
                ])
            @endforeach
        </div>
    @endif
</div>
