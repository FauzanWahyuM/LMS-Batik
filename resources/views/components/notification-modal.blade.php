@if (session('modal_success') || session('modal_error') || $errors->any())
    <div id="notification-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-xl sm:max-w-md max-w-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full {{ session('modal_success') ? 'bg-emerald-100' : 'bg-rose-100' }}">
                        @if (session('modal_success'))
                            <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold {{ session('modal_success') ? 'text-emerald-900' : 'text-rose-900' }}">
                            {{ session('modal_success') ? 'Sukses' : 'Error' }}
                        </h3>
                        <p class="mt-1 text-sm {{ session('modal_success') ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ session('modal_success') ?: $errors->first() ?? 'Terjadi kesalahan.' }}
                        </p>
                    </div>
                </div>
                <button type="button" id="close-modal" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" id="modal-confirm-btn"
                    class="rounded-lg {{ session('modal_success') ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }} px-4 py-2 text-sm font-semibold text-white transition">
                    OK
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('notification-modal');
            const closeButton = document.getElementById('close-modal');
            const confirmButton = document.getElementById('modal-confirm-btn');

            if (modal) {
                // Show modal
                setTimeout(() => modal.classList.remove('opacity-0', 'pointer-events-none'), 10);

                // Hide modal on close
                const hideModal = () => {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => modal.remove(), 300);
                };

                if (closeButton) closeButton.addEventListener('click', hideModal);
                if (confirmButton) confirmButton.addEventListener('click', hideModal);

                // Auto-hide after 5 seconds
                setTimeout(hideModal, 5000);
            }
        });
    </script>
@endif
