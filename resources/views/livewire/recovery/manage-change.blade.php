<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">
            {{ $form->change ? 'تعديل التغيير' : '🧭 تغيير جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-[auto_1fr] gap-3 items-end">
                <div>
                    <x-input-label for="rc_icon" value="أيقونة" />
                    <x-text-input id="rc_icon" wire:model="form.icon" type="text" class="mt-1 block w-16 text-center text-lg" maxlength="10" />
                </div>
                <div>
                    <x-input-label for="rc_title" value="التغيير" />
                    <x-text-input id="rc_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: أتحكم في انفعالي" />
                </div>
            </div>
            <x-input-error :messages="$errors->get('form.icon')" class="-mt-2" />
            <x-input-error :messages="$errors->get('form.title')" class="-mt-2" />

            <div>
                <x-input-label for="rc_why" value="ليه محتاج التغيير ده؟ (اختياري)" />
                <textarea id="rc_why" wire:model="form.why" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="إيه اللي بيكلفني الطبع/العادة دي؟"></textarea>
                <x-input-error :messages="$errors->get('form.why')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rc_started_at" value="تاريخ البداية" />
                    <x-text-input id="rc_started_at" wire:model="form.started_at" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.started_at')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="rc_target_date" value="تاريخ مستهدف (اختياري)" />
                    <x-text-input id="rc_target_date" wire:model="form.target_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.target_date')" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rc_status" value="الحالة" />
                    <select id="rc_status" wire:model="form.status" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach (\App\Enums\RecoveryStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="rc_recovery" value="فترة التعافي (اختياري)" />
                    <select id="rc_recovery" wire:model="form.recovery_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="">بدون ربط</option>
                        @foreach ($recoveries as $recovery)
                            <option value="{{ $recovery->id }}">{{ $recovery->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
