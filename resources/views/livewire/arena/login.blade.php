<div class="py-12 px-4">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">دخول الساحة 🏟️</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">ادخل عشان تشارك في التحديات.</p>
        </div>

        <form wire:submit="login" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 space-y-4">
            <div>
                <x-input-label for="al_email" value="البريد الإلكتروني" />
                <x-text-input id="al_email" wire:model="email" type="email" class="mt-1 block w-full" dir="ltr" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="al_pw" value="كلمة المرور" />
                <x-text-input id="al_pw" wire:model="password" type="password" class="mt-1 block w-full" dir="ltr" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
            <label class="flex items-center gap-2 text-sm text-ink-soft dark:text-ink-dark-soft">
                <input type="checkbox" wire:model="remember" class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" />
                افتكرني
            </label>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">دخول</button>
            <p class="text-center text-sm text-ink-soft dark:text-ink-dark-soft">
                لسه معندكش حساب؟ <a href="{{ route('arena.register', $code ? ['code' => $code] : []) }}" wire:navigate class="text-primary dark:text-primary-dark hover:underline">اعمل حساب</a>
            </p>
        </form>
    </div>
</div>
