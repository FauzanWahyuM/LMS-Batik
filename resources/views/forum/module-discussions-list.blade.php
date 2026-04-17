<!-- Module Discussions Section -->
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

        $moduleSlug = (string) ($moduleSlug ?? '');
        $moduleTitle = (string) ($moduleTitle ?? '');
    @endphp

    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Diskusi Modul</h3>
            @if ($moduleTitle !== '')
                <p class="mt-1 text-xs text-slate-500">{{ $moduleTitle }}</p>
            @endif
        </div>
        @if ($user)
            <button type="button" id="open-module-discussion"
                class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">
                Kirim Pertanyaan
            </button>
        @endif
    </div>

    @if ($user)
        <div id="module-discussion-form" class="mb-6 hidden rounded-lg border border-slate-200 bg-slate-50 p-4">
            <h4 class="mb-3 font-semibold text-slate-900">Pesan Baru</h4>
            <form method="POST" action="{{ route('forum.module-discussion.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="module_slug" value="{{ $moduleSlug }}">

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Judul Pertanyaan</label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        placeholder="Tuliskan judul pertanyaan...">
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700">Isi Pertanyaan / Pesan</label>
                    <textarea name="content" rows="4" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        placeholder="Tuliskan isi pertanyaan atau pesan Anda..."></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" id="cancel-module-discussion"
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
    @endif

    <div class="space-y-4">
        @forelse (($discussions ?? collect()) as $discussion)
            @php
                $canReply =
                    $user &&
                    !$discussion->is_closed &&
                    ($isCurrentUserInstructor || ($user['email'] ?? null) === $discussion->user_email);
                $canManageDiscussion = $user && ($user['email'] ?? null) === $discussion->user_email;
            @endphp
            <article class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="truncate font-semibold text-slate-900">{{ $discussion->user_name }}</p>
                    <span
                        class="rounded px-2 py-1 text-xs font-semibold {{ $roleLabel($discussion->user_role) === 'Pengajar' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $roleLabel($discussion->user_role) }}
                    </span>
                    @if ($discussion->is_pinned)
                        <span class="rounded bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">Penting</span>
                    @endif
                    @if ($discussion->is_closed)
                        <span class="rounded bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-500">Ditutup</span>
                    @endif
                </div>

                <h4 class="mt-2 text-base font-semibold text-slate-900">{{ $discussion->title }}</h4>
                <p class="mt-1 text-xs text-slate-600">{{ $discussion->created_at->diffForHumans() }}</p>

                <div
                    class="mt-2 rounded-lg border border-slate-200 bg-white p-3 text-sm leading-relaxed text-slate-700">
                    {{ $discussion->content }}
                </div>

                @if ($user)
                    <div class="mt-3 flex flex-wrap gap-3">
                        @if ($canReply)
                            <button type="button" data-toggle-module-reply="{{ $discussion->id }}"
                                class="text-xs font-semibold text-slate-700 hover:text-slate-900">
                                Balas
                            </button>
                        @endif

                        @if ($canManageDiscussion)
                            <button type="button" data-edit-module-discussion="{{ $discussion->id }}"
                                class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                Edit
                            </button>

                            <form method="POST"
                                action="{{ route('forum.module-discussion.delete', ['discussion' => $discussion->id]) }}"
                                id="delete-module-discussion-form-{{ $discussion->id }}" class="inline">
                                @csrf
                                <button type="button" data-delete-module-discussion="{{ $discussion->id }}"
                                    data-delete-title="{{ $discussion->title }}"
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>

                    @if ($canManageDiscussion)
                        <div id="edit-module-discussion-{{ $discussion->id }}"
                            class="mt-3 hidden rounded-lg border border-slate-300 bg-white p-3">
                            <form method="POST"
                                action="{{ route('forum.module-discussion.update', ['discussion' => $discussion->id]) }}"
                                class="space-y-2">
                                @csrf
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Judul
                                        Pertanyaan</label>
                                    <input type="text" name="title" value="{{ $discussion->title }}" required
                                        class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs focus:border-slate-500 focus:outline-none">
                                </div>
                                <div>
                                    <textarea name="content" rows="3" required
                                        class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs focus:border-slate-500 focus:outline-none">{{ $discussion->content }}</textarea>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-cancel-module-discussion-edit="{{ $discussion->id }}"
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

                    @if ($canReply)
                        <div id="module-reply-form-{{ $discussion->id }}"
                            class="mt-3 hidden rounded-lg border border-slate-300 bg-white p-3">
                            <form method="POST"
                                action="{{ route('forum.module-discussion.reply', ['discussion' => $discussion->id]) }}"
                                class="space-y-2">
                                @csrf
                                <label class="block text-xs font-semibold text-slate-700">Balasan</label>
                                <textarea name="content" rows="3" required
                                    class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm focus:border-slate-500 focus:outline-none"
                                    placeholder="Tulis balasan Anda..."></textarea>
                                <div class="flex justify-end gap-2">
                                    <button type="button" data-cancel-module-reply="{{ $discussion->id }}"
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
                @endif

                @if ($discussion->replies && $discussion->replies->count())
                    <div class="mt-4 space-y-3 border-t border-slate-200 pt-4">
                        @foreach ($discussion->replies as $reply)
                            @php
                                $replyRole = in_array(
                                    strtolower((string) ($reply->user_role ?? 'peserta')),
                                    ['pengajar', 'instructor', 'teacher'],
                                    true,
                                )
                                    ? 'Pengajar'
                                    : 'Peserta';
                            @endphp
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-slate-900">{{ $reply->user_name }}</p>
                                    <span
                                        class="rounded px-2 py-1 text-xs font-semibold {{ $replyRole === 'Pengajar' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $replyRole }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">{{ $reply->created_at->diffForHumans() }}</p>
                                <p class="mt-2 text-sm text-slate-700">{{ $reply->content }}</p>

                                @if ($user && ($user['email'] ?? null) === $reply->user_email)
                                    <div class="mt-3 flex flex-wrap gap-3">
                                        <button type="button" data-edit-module-reply="{{ $reply->id }}"
                                            class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                            Edit
                                        </button>
                                        <form method="POST"
                                            action="{{ route('forum.module-reply.delete', ['reply' => $reply->id]) }}"
                                            id="delete-module-reply-form-{{ $reply->id }}" class="inline">
                                            @csrf
                                            <button type="button" data-delete-module-reply="{{ $reply->id }}"
                                                data-delete-user="{{ $reply->user_name }}"
                                                class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>

                                    <div id="edit-module-reply-{{ $reply->id }}"
                                        class="mt-3 hidden rounded-lg border border-slate-300 bg-slate-50 p-3">
                                        <form method="POST"
                                            action="{{ route('forum.module-reply.update', ['reply' => $reply->id]) }}"
                                            class="space-y-2">
                                            @csrf
                                            <textarea name="content" rows="3" required
                                                class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm focus:border-slate-500 focus:outline-none">{{ $reply->content }}</textarea>
                                            <div class="flex justify-end gap-2">
                                                <button type="button"
                                                    data-cancel-module-reply-edit="{{ $reply->id }}"
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
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <div
                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-600">
                Belum ada diskusi pada modul ini.
            </div>
        @endforelse
    </div>

    <div id="delete-module-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
                id="delete-module-backdrop">
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
                        <h3 class="text-lg font-semibold leading-6 text-slate-900" id="delete-module-modal-title">
                            Hapus Data Diskusi
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500" id="delete-module-modal-message">
                                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse sm:gap-3">
                    <button type="button" id="confirm-delete-module-btn"
                        class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                        Hapus
                    </button>
                    <button type="button" id="cancel-delete-module-btn"
                        class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('open-module-discussion');
            const cancelBtn = document.getElementById('cancel-module-discussion');
            const form = document.getElementById('module-discussion-form');
            const deleteModuleModal = document.getElementById('delete-module-modal');
            const deleteModuleBackdrop = document.getElementById('delete-module-backdrop');
            const confirmDeleteModuleBtn = document.getElementById('confirm-delete-module-btn');
            const cancelDeleteModuleBtn = document.getElementById('cancel-delete-module-btn');
            const deleteModuleModalTitle = document.getElementById('delete-module-modal-title');
            const deleteModuleModalMessage = document.getElementById('delete-module-modal-message');

            let activeDeleteForm = null;

            if (openBtn) {
                openBtn.addEventListener('click', function() {
                    form?.classList.remove('hidden');
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    form?.classList.add('hidden');
                });
            }

            const showDeleteModal = (title, message, formElement) => {
                if (!deleteModuleModal || !deleteModuleModalTitle || !deleteModuleModalMessage || !
                    formElement) {
                    return;
                }

                deleteModuleModalTitle.textContent = title;
                deleteModuleModalMessage.textContent = message;
                activeDeleteForm = formElement;
                deleteModuleModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const hideDeleteModal = () => {
                if (!deleteModuleModal) {
                    return;
                }

                deleteModuleModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                activeDeleteForm = null;
            };

            document.querySelectorAll('[data-delete-module-discussion]').forEach((button) => {
                button.addEventListener('click', function() {
                    const discussionId = this.getAttribute('data-delete-module-discussion');
                    const title = (this.getAttribute('data-delete-title') || '').trim();
                    const safeTitle = title !== '' ? title : 'pertanyaan ini';
                    const formElement = document.getElementById(
                        `delete-module-discussion-form-${discussionId}`);

                    showDeleteModal(
                        `Hapus Pertanyaan: ${safeTitle}`,
                        `Apakah Anda yakin ingin menghapus pertanyaan "${safeTitle}"? Tindakan ini tidak dapat dibatalkan.`,
                        formElement,
                    );
                });
            });

            document.querySelectorAll('[data-delete-module-reply]').forEach((button) => {
                button.addEventListener('click', function() {
                    const replyId = this.getAttribute('data-delete-module-reply');
                    const userName = (this.getAttribute('data-delete-user') || '').trim();
                    const safeUserName = userName !== '' ? userName : 'pengguna ini';
                    const formElement = document.getElementById(
                        `delete-module-reply-form-${replyId}`);

                    showDeleteModal(
                        `Hapus Balasan ${safeUserName}`,
                        `Apakah Anda yakin ingin menghapus balasan dari ${safeUserName}? Tindakan ini tidak dapat dibatalkan.`,
                        formElement,
                    );
                });
            });

            if (confirmDeleteModuleBtn) {
                confirmDeleteModuleBtn.addEventListener('click', function() {
                    if (activeDeleteForm) {
                        activeDeleteForm.submit();
                    }
                });
            }

            if (cancelDeleteModuleBtn) {
                cancelDeleteModuleBtn.addEventListener('click', hideDeleteModal);
            }

            if (deleteModuleBackdrop) {
                deleteModuleBackdrop.addEventListener('click', hideDeleteModal);
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && deleteModuleModal && !deleteModuleModal.classList.contains(
                        'hidden')) {
                    hideDeleteModal();
                }
            });

            document.querySelectorAll('[data-toggle-module-reply]').forEach((button) => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-toggle-module-reply');
                    const replyForm = document.getElementById(`module-reply-form-${id}`);
                    if (replyForm) replyForm.classList.toggle('hidden');
                });
            });

            document.querySelectorAll('[data-cancel-module-reply]').forEach((button) => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-cancel-module-reply');
                    const replyForm = document.getElementById(`module-reply-form-${id}`);
                    if (replyForm) replyForm.classList.add('hidden');
                });
            });

            document.querySelectorAll('[data-edit-module-discussion]').forEach((button) => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-edit-module-discussion');
                    const editForm = document.getElementById(`edit-module-discussion-${id}`);
                    if (editForm) editForm.classList.toggle('hidden');
                });
            });

            document.querySelectorAll('[data-cancel-module-discussion-edit]').forEach((button) => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-cancel-module-discussion-edit');
                    const editForm = document.getElementById(`edit-module-discussion-${id}`);
                    if (editForm) editForm.classList.add('hidden');
                });
            });

            document.querySelectorAll('[data-edit-module-reply]').forEach((button) => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-edit-module-reply');
                    const editForm = document.getElementById(`edit-module-reply-${id}`);
                    if (editForm) editForm.classList.toggle('hidden');
                });
            });

            document.querySelectorAll('[data-cancel-module-reply-edit]').forEach((button) => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-cancel-module-reply-edit');
                    const editForm = document.getElementById(`edit-module-reply-${id}`);
                    if (editForm) editForm.classList.add('hidden');
                });
            });
        });
    </script>
</section>
