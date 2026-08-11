<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">رمز الحماية (PIN)</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            رمز إضافي يُطلب قبل فتح المذكرات والتعافي، حتى بعد تسجيل الدخول.
        </p>
    </header>

    <div x-data="{ shown: false }" x-on:pin-updated.window="shown = true; setTimeout(() => shown = false, 2500)">
        <form wire:submit="setPin" class="mt-6 space-y-6">
            @if ($hasPin)
                <div>
                    <x-input-label for="current_pin" value="رمز الحماية الحالي" />
                    <x-text-input id="current_pin" wire:model="current_pin" type="password" inputmode="numeric" class="mt-1 block w-full" autocomplete="off" />
                    <x-input-error :messages="$errors->get('current_pin')" class="mt-2" />
                </div>
            @endif

            <div>
                <x-input-label for="pin" :value="$hasPin ? 'رمز الحماية الجديد' : 'رمز الحماية'" />
                <x-text-input id="pin" wire:model="pin" type="password" inputmode="numeric" class="mt-1 block w-full" autocomplete="off" placeholder="٤ إلى ١٢ رقمًا" />
                <x-input-error :messages="$errors->get('pin')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="pin_confirmation" value="تأكيد رمز الحماية" />
                <x-text-input id="pin_confirmation" wire:model="pin_confirmation" type="password" inputmode="numeric" class="mt-1 block w-full" autocomplete="off" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ $hasPin ? 'تغيير الرمز' : 'تعيين الرمز' }}</x-primary-button>

                @if ($hasPin)
                    <button type="button" wire:click="removePin" class="text-sm text-danger hover:underline">إزالة الرمز</button>
                @endif

                <span x-show="shown" x-transition class="text-sm text-success">تم الحفظ.</span>
            </div>
        </form>
    </div>
</section>
