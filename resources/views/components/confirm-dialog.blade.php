<div x-data="{
    open: false,
    title: 'Are you sure?',
    message: '',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    onConfirm: null
}" @confirm.window="
    open = true;
    title = $event.detail.title || 'Are you sure?';
    message = $event.detail.message || '';
    confirmText = $event.detail.confirmText || 'Confirm';
    cancelText = $event.detail.cancelText || 'Cancel';
    onConfirm = $event.detail.onConfirm || null;
">
    <!-- Modal Overlay -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/50 dark:bg-black/70" @click="open = false"></div>

        <div x-show="open" x-transition class="relative bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-gray-100 dark:border-slate-700 max-w-md w-full mx-4 p-6 z-10">
            <!-- Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>

            <!-- Content -->
            <div class="text-center">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="title"></h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-slate-400" x-text="message"></p>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-center gap-3">
                <button @click="open = false" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors" x-text="cancelText"></button>
                <button @click="if (onConfirm) { onConfirm(); } open = false;" type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors" x-text="confirmText"></button>
            </div>
        </div>
    </div>
</div>
