<!-- Forum Discussions Section -->
<section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    @php
        $roleLabel = function ($role) {
            $value = strtolower((string) $role);

            return in_array($value, ['pengajar', 'instructor', 'teacher'], true) ? 'Pengajar' : 'Peserta';
        };

        $discussionThemeLabel = function ($discussion) {
            $storedTheme = trim((string) ($discussion->theme ?? ''));
            if ($storedTheme !== '') {
                return $storedTheme;
            }

            if (!empty($discussion->module?->title)) {
                return (string) $discussion->module->title;
            }

            $fallbackTheme = trim((string) ($discussion->module_id ?? ''));

            return $fallbackTheme !== '' ? $fallbackTheme : 'Tanpa tema';
        };

        $isCurrentUserInstructor = in_array(
            strtolower((string) ($user['role'] ?? '')),
            ['pengajar', 'instructor', 'teacher'],
            true,
        );

        $modules = $modules ?? collect();
        $forumThemes = collect($forumThemes ?? []);
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

    @if ($showModuleFilter && $forumThemes->count())
        <form method="GET" action="{{ url()->current() }}"
            class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
            <label class="mb-1 block text-xs font-semibold text-slate-700">Filter Tema Modul</label>
            <div class="flex flex-col gap-2 sm:flex-row">
                <select name="module"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                    <option value="">Semua tema</option>
                    @foreach ($forumThemes as $theme)
                        <option value="{{ $theme['key'] }}" @selected($selectedModuleSlug === $theme['key'])>
                            {{ $theme['label'] }}
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
                <input type="hidden" name="theme_option" value="{{ $moduleSlug }}">
                <input type="hidden" name="module_slug" value="{{ $moduleSlug }}">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tema</label>
                    <input type="text" readonly value="{{ $moduleTitle ?? $moduleSlug }}"
                        class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-600">
                </div>
            @else
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Pilih Tema Terdaftar</label>
                    <select name="theme_option" id="discussion-theme-option"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">Pilih tema...</option>
                        @foreach ($forumThemes as $theme)
                            <option value="{{ $theme['key'] }}" @selected(old('theme_option', $selectedModuleSlug) === $theme['key'])>
                                {{ $theme['label'] }}
                            </option>
                        @endforeach
                        <option value="new" @selected(old('theme_option') === 'new')>+ Buat tema baru</option>
                    </select>
                    @error('theme_option')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="new-theme-wrapper" class="hidden">
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Tema Baru</label>
                    <input type="text" name="new_theme" value="{{ old('new_theme') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        placeholder="Contoh: Teknik Canting Dasar">
                    @error('new_theme')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Judul Pertanyaan</label>
                <input type="text" name="title" required value="{{ old('title') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                    placeholder="Tuliskan judul pertanyaan...">
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">Isi Pertanyaan / Pesan</label>
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
                            <p class="truncate font-semibold text-slate-900">{{ $discussion->user_name }}</p>
                            <span
                                class="rounded px-2 py-1 text-xs font-semibold {{ $roleLabel($discussion->user_role) === 'Pengajar' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $roleLabel($discussion->user_role) }}
                            </span>
                            @if ($discussion->is_pinned)
                                <span
                                    class="rounded bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">Penting</span>
                            @endif
                            @if ($discussion->is_closed)
                                <span
                                    class="rounded bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-500">Ditutup</span>
                            @endif
                        </div>

                        <h4 class="mt-2 text-base font-semibold text-slate-900">{{ $discussion->title }}</h4>

                        <p class="mt-1 text-xs text-slate-600">
                            Tema: {{ $discussionThemeLabel($discussion) }} •
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

                        @if (($user['email'] ?? null) === $discussion->user_email)
                            <button type="button" data-edit-discussion="{{ $discussion->id }}"
                                class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                Edit
                            </button>

                            <form method="POST"
                                action="{{ route('forum.delete', ['discussion' => $discussion->id]) }}"
                                id="delete-discussion-form-{{ $discussion->id }}" class="inline">
                                @csrf
                                <button type="button" data-delete-discussion="{{ $discussion->id }}"
                                    data-delete-title="{{ $discussion->title }}"
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                @if ($user && ($user['email'] ?? null) === $discussion->user_email)
                    <div id="edit-discussion-{{ $discussion->id }}"
                        class="mt-3 hidden rounded-lg border border-slate-300 bg-white p-3">
                        <form method="POST" action="{{ route('forum.update', ['discussion' => $discussion->id]) }}"
                            class="space-y-2">
                            @csrf
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">Tema</label>
                                <input type="text" name="theme"
                                    value="{{ $discussionThemeLabel($discussion) }}" required
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs focus:border-slate-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">Judul Pertanyaan</label>
                                <input type="text" name="title" value="{{ $discussion->title }}" required
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

            </article>
        @empty
            <div
                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-600">
                Belum ada diskusi. Jadilah yang pertama membuat diskusi!
            </div>
        @endforelse
    </div>
</section>

<!-- Delete Discussion Confirmation Modal -->
<div id="delete-discussion-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
            id="delete-discussion-backdrop">
        </div>

        <div
            class="inline-block transform overflow-hidden rounded-2xl bg-white px-4 pt-5 pb-4 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
            <div class="sm:flex sm:items-start">
                <div
                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg font-semibold leading-6 text-slate-900" id="delete-discussion-modal-title">
                        Hapus Pertanyaan
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-slate-500" id="delete-discussion-modal-message">
                            Apakah Anda yakin ingin menghapus pertanyaan ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-3">
                <button type="button" id="confirm-delete-discussion-btn"
                    class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                    Hapus
                </button>
                <button type="button" id="cancel-delete-discussion-btn"
                    class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('open-create-discussion');
        const cancelBtn = document.getElementById('cancel-create-discussion');
        const createForm = document.getElementById('create-discussion-form');
        const themeOption = document.getElementById('discussion-theme-option');
        const newThemeWrapper = document.getElementById('new-theme-wrapper');
        const deleteDiscussionModal = document.getElementById('delete-discussion-modal');
        const deleteDiscussionBackdrop = document.getElementById('delete-discussion-backdrop');
        const confirmDeleteDiscussionBtn = document.getElementById('confirm-delete-discussion-btn');
        const cancelDeleteDiscussionBtn = document.getElementById('cancel-delete-discussion-btn');
        const deleteDiscussionModalTitle = document.getElementById('delete-discussion-modal-title');
        const deleteDiscussionModalMessage = document.getElementById('delete-discussion-modal-message');

        let activeDeleteDiscussionForm = null;

        const showDeleteDiscussionModal = (title, formElement) => {
            if (!deleteDiscussionModal || !deleteDiscussionModalTitle || !deleteDiscussionModalMessage) {
                return;
            }

            const safeTitle = title && title.trim() !== '' ? title.trim() : 'pertanyaan ini';
            deleteDiscussionModalTitle.textContent = `Hapus Pertanyaan: ${safeTitle}`;
            deleteDiscussionModalMessage.textContent =
                `Apakah Anda yakin ingin menghapus pertanyaan "${safeTitle}"? Tindakan ini tidak dapat dibatalkan.`;
            activeDeleteDiscussionForm = formElement;
            deleteDiscussionModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        const hideDeleteDiscussionModal = () => {
            if (!deleteDiscussionModal) {
                return;
            }

            deleteDiscussionModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            activeDeleteDiscussionForm = null;
        };

        const syncNewThemeVisibility = () => {
            if (!themeOption || !newThemeWrapper) {
                return;
            }

            if (themeOption.value === 'new') {
                newThemeWrapper.classList.remove('hidden');
            } else {
                newThemeWrapper.classList.add('hidden');
            }
        };

        if (openBtn) {
            openBtn.addEventListener('click', () => createForm?.classList.remove('hidden'));
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => createForm?.classList.add('hidden'));
        }

        if (themeOption) {
            themeOption.addEventListener('change', syncNewThemeVisibility);
            syncNewThemeVisibility();
        }

        document.querySelectorAll('[data-delete-discussion]').forEach((button) => {
            button.addEventListener('click', function() {
                const discussionId = this.getAttribute('data-delete-discussion');
                const title = this.getAttribute('data-delete-title') || '';
                const formElement = document.getElementById(
                    `delete-discussion-form-${discussionId}`);

                if (formElement) {
                    showDeleteDiscussionModal(title, formElement);
                }
            });
        });

        if (confirmDeleteDiscussionBtn) {
            confirmDeleteDiscussionBtn.addEventListener('click', function() {
                if (activeDeleteDiscussionForm) {
                    activeDeleteDiscussionForm.submit();
                }
            });
        }

        if (cancelDeleteDiscussionBtn) {
            cancelDeleteDiscussionBtn.addEventListener('click', hideDeleteDiscussionModal);
        }

        if (deleteDiscussionBackdrop) {
            deleteDiscussionBackdrop.addEventListener('click', hideDeleteDiscussionModal);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && deleteDiscussionModal && !deleteDiscussionModal.classList
                .contains('hidden')) {
                hideDeleteDiscussionModal();
            }
        });

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
