<div class="py-16">
    <div class="max-w-sm mx-auto px-4">
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-8 text-center">
            <div class="text-4xl mb-3">🔒</div>
            <h1 class="text-xl font-bold text-ink dark:text-ink-dark">قسم محمي</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 mb-6">أدخل رمز الحماية لفتح المذكرات والتعافي.</p>

            <form wire:submit="unlock" class="space-y-4">
                <div>
                    <input type="password" wire:model="pin" autofocus inputmode="numeric" autocomplete="off"
                           class="block w-full text-center tracking-[0.5em] rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary"
                           placeholder="••••" />
                    <x-input-error :messages="$errors->get('pin')" class="mt-2 text-center" />
                </div>
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">
                    فتح
                </button>
            </form>
        </div>
    </div>
</div>
