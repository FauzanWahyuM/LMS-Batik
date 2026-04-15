<!-- Forum Discussions Section -->
<section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    @php
        $roleLabel = function ($role) {
            $value = strtolower((string) $role);

            return in_array($value, ['pengajar', 'instructor', 'teacher'], true) ? 'Pengajar' : 'Peserta';
        };

        $isCurrentUserInstructor = in_array(
            strtolower((string) ($user['role'] ?? '')),
            ['pengajar', 'instructor', 'teacher'],
            true,
        );

        $modules = $modules ?? collect();
        $selectedModuleSlug = (string) ($selectedModuleSlug ?? '');
        $moduleContext = (bool) ($moduleContext ?? false);
        $showModuleFilter = (bool) ($showModuleFilter ?? true);
        $moduleSlug = $moduleSlug ?? null;
    @endphp

    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Forum Diskusi</h3>
            @if ($moduleContext && $moduleSlug)
                <p class="mt-1 text-xs text-slate-500">Diskusi tema modul: {{ $moduleTitle ?? $moduleSlug }}</p>
            @endif
        </div>
        <button type="button" id="open-create-discussion"
            class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">
            Kirim Pertanyaan
        </button>
    </div>

    @if ($showModuleFilter && $modules->count())
        <form method="GET" action="{{ url()->current() }}"
            class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
            <label class="mb-1 block text-xs font-semibold text-slate-700">Filter Tema Modul</label>
            <div class="flex flex-col gap-2 sm:flex-row">
                <select name="module"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                    <option value="">Semua tema</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module->slug }}" @selected($selectedModuleSlug === $module->slug)>
                            {{ $module->title }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                    class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-700">
                    Terapkan
                </button>
            </div>
        </form>
    @endif

    <div id="create-discussion-form" class="mb-6 hidden rounded-lg border border-slate-200 bg-slate-50 p-4">
        <h4 class="mb-3 font-semibold text-slate-900">Pesan Baru</h4>
        <form method="POST" action="{{ route('forum.store') }}" class="space-y-3">
            @csrf
            @if ($moduleContext && !empty($moduleSlug))
                <input type="hidden" name="module_id" value="{{ $moduleSlug }}">
            @elseif (!empty($selectedModuleSlug))
                <input type="hidden" name="module_id" value="{{ $selectedModuleSlug }}">
            @endif

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Tema</label>
                <input type="text" name="theme" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                    placeholder="Tuliskan tema diskusi...">
                @error('theme')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Pertanyaan / Pesan</label>
                <textarea name="content" rows="4" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                    placeholder="Tuliskan isi pertanyaan atau pesan Anda..."></textarea>
                @error('content')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" id="cancel-create-discussion"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit"
                    class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">
                    Posting
                </button>
            </div>
        </form>
    </div>

    <div class="max-h-[36rem] space-y-4 overflow-y-auto">
        @forelse (($discussions ?? collect()) as $discussion)
            <article class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="truncate font-semibold text-slate-900">{{ $discussion->theme }}</h4>
                            <span
                                class="rounded px-2 py-1 text-xs font-semibold {{ $roleLabel($discussion->user_role) === 'Pengajar' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $roleLabel($discussion->user_role) }}
                            </span>
                            @if (!empty($discussion->module))
                                <span class="rounded bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700">
                                    {{ $discussion->module->title }}
                                </span>
                            @endif
                            @if ($discussion->is_pinned)
                                <span
                                    class="rounded bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">Penting</span>
                            @endif
                            @if ($discussion->is_closed)
                                <span
                                    class="rounded bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-500">Ditutup</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-slate-600">
                            {{ $discussion->user_name }} • {{ $roleLabel($discussion->user_role) }} •
                            {{ $discussion->created_at->diffForHumans() }}
                        </p>
                        <div
                            class="mt-1 rounded-lg border border-slate-200 bg-white p-3 text-sm leading-relaxed text-slate-700">
                            {{ $discussion->content }}
                        </div>
                    </div>
                </div>

                @if ($user)
                    <div class="mt-4 flex flex-wrap gap-3">
                        @if (!$discussion->is_closed && (!$discussion->module_id || $isCurrentUserInstructor))
                            <button type="button" data-toggle-reply="{{ $discussion->id }}"
                                class="text-xs font-semibold text-slate-700 hover:text-slate-900">
                                Balas
                            </button>
                        @endif

                        @if (($user['email'] ?? null) === $discussion->user_email || $isCurrentUserInstructor)
                            @if ($isCurrentUserInstructor)
                                <form method="POST"
                                    action="{{ route('forum.toggle-pin', ['discussion' => $discussion->id]) }}"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs font-semibold {{ $discussion->is_pinned ? 'text-amber-700' : 'text-slate-600' }} hover:text-amber-700">
                                        {{ $discussion->is_pinned ? 'Lepas Pin' : 'Pin' }}
                                    </button>
                                </form>

                                <form method="POST"
                                    action="{{ route('forum.toggle-close', ['discussion' => $discussion->id]) }}"
                                    class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs font-semibold {{ $discussion->is_closed ? 'text-slate-500' : 'text-slate-600' }} hover:text-slate-800">
                                        {{ $discussion->is_closed ? 'Buka' : 'Tutup' }}
                                    </button>
                                </form>
                            @endif

                            <button type="button" data-edit-discussion="{{ $discussion->id }}"
                                class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                Edit
                            </button>

                            <form method="POST"
                                action="{{ route('forum.delete', ['discussion' => $discussion->id]) }}"
                                onsubmit="return confirm('Hapus diskusi ini?')" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                @if ($user && !$discussion->is_closed && (!$discussion->module_id || $isCurrentUserInstructor))
                    <div id="reply-form-{{ $discussion->id }}"
                        class="mt-4 hidden rounded-lg border border-slate-300 bg-white p-3">
                        <form method="POST" action="{{ route('forum.reply', ['discussion' => $discussion->id]) }}"
                            class="space-y-2">
                            @csrf
                            <label class="block text-xs font-semibold text-slate-700">Balasan</label>
                            <textarea name="content" rows="3" required
                                class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm focus:border-slate-500 focus:outline-none"
                                placeholder="Tulis balasan Anda..."></textarea>
                            <div class="flex justify-end gap-2">
                                <button type="button" data-cancel-reply="{{ $discussion->id }}"
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

                @if ($discussion->replies && $discussion->replies->count())
                    <div class="mt-4 space-y-3 border-t border-slate-200 pt-4">
                        @foreach ($discussion->replies as $reply)
                            @include('forum.partials.reply-item', [
                                'reply' => $reply,
                                'discussion' => $discussion,
                                'user' => $user,
                                'isCurrentUserInstructor' => $isCurrentUserInstructor,
                            ])
                        @endforeach
                    </div>
                @endif

                @if ($user && (($user['email'] ?? null) === $discussion->user_email || $isCurrentUserInstructor))
                    <div id="edit-discussion-{{ $discussion->id }}"
                        class="mt-3 hidden rounded-lg border border-slate-300 bg-white p-3">
                        <form method="POST" action="{{ route('forum.update', ['discussion' => $discussion->id]) }}"
                            class="space-y-2">
                            @csrf
                            <div>
                                <input type="text" name="theme" value="{{ $discussion->theme }}" required
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs focus:border-slate-500 focus:outline-none">
                            </div>
                            <div>
                                <textarea name="content" rows="3" required
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs focus:border-slate-500 focus:outline-none">{{ $discussion->content }}</textarea>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" data-cancel-edit="{{ $discussion->id }}"
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
            </article>
        @empty
            <div
                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-600">
                Belum ada diskusi. Jadilah yang pertama membuat diskusi!
            </div>
        @endforelse
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('open-create-discussion');
        const cancelBtn = document.getElementById('cancel-create-discussion');
        const createForm = document.getElementById('create-discussion-form');

        if (openBtn) {
            openBtn.addEventListener('click', () => createForm?.classList.remove('hidden'));
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => createForm?.classList.add('hidden'));
        }

        document.querySelectorAll('[data-toggle-reply]').forEach((button) => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-toggle-reply');
                const replyForm = document.getElementById(`reply-form-${id}`);
                if (replyForm) {
                    replyForm.classList.toggle('hidden');
                }
            });
        });

        document.querySelectorAll('[data-cancel-reply]').forEach((button) => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-cancel-reply');
                const replyForm = document.getElementById(`reply-form-${id}`);
                if (replyForm) {
                    replyForm.classList.add('hidden');
                }
            });
        });

        document.querySelectorAll('[data-toggle-child-reply]').forEach((button) => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-toggle-child-reply');
                const form = document.getElementById(`child-reply-form-${id}`);
                if (form) {
                    form.classList.toggle('hidden');
                }
            });
        });

        document.querySelectorAll('[data-cancel-child-reply]').forEach((button) => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-cancel-child-reply');
                const form = document.getElementById(`child-reply-form-${id}`);
                if (form) {
                    form.classList.add('hidden');
                }
            });
        });

        document.querySelectorAll('[data-edit-discussion]').forEach((button) => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-edit-discussion');
                const form = document.getElementById(`edit-discussion-${id}`);
                if (form) {
                    form.classList.remove('hidden');
                }
            });
        });

        document.querySelectorAll('[data-cancel-edit]').forEach((button) => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-cancel-edit');
                const form = document.getElementById(`edit-discussion-${id}`);
                if (form) {
                    form.classList.add('hidden');
                }
            });
        });

        document.querySelectorAll('[data-edit-reply]').forEach((button) => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-edit-reply');
                const form = document.getElementById(`edit-reply-${id}`);
                if (form) {
                    form.classList.remove('hidden');
                }
            });
        });

        document.querySelectorAll('[data-cancel-reply-edit]').forEach((button) => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-cancel-reply-edit');
                const form = document.getElementById(`edit-reply-${id}`);
                if (form) {
                    form.classList.add('hidden');
                }
            });
        });
    });
</script>
