<div class="py-12 px-4">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">انضم لساحة التحديات 🏟️</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">اعمل حساب وشارك أصحابك في تحديات الصلاة والورد.</p>
        </div>

        <form wire:submit="register" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 space-y-4">
            <div>
                <x-input-label for="ar_name" value="الاسم" />
                <x-text-input id="ar_name" wire:model="name" type="text" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="ar_email" value="البريد الإلكتروني" />
                <x-text-input id="ar_email" wire:model="email" type="email" class="mt-1 block w-full" dir="ltr" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="ar_pw" value="كلمة المرور" />
                <x-text-input id="ar_pw" wire:model="password" type="password" class="mt-1 block w-full" dir="ltr" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="ar_pw2" value="تأكيد كلمة المرور" />
                <x-text-input id="ar_pw2" wire:model="password_confirmation" type="password" class="mt-1 block w-full" dir="ltr" />
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">إنشاء الحساب</button>
            <p class="text-center text-sm text-ink-soft dark:text-ink-dark-soft">
                عندك حساب؟ <a href="{{ route('arena.login', $code ? ['code' => $code] : []) }}" wire:navigate class="text-primary dark:text-primary-dark hover:underline">دخول</a>
            </p>
        </form>
    </div>
</div>
