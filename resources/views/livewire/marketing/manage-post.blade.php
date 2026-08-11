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
            {{ $form->post ? 'تعديل البوست' : 'بوست جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="mp_topic" value="الموضوع" />
                    <x-text-input id="mp_topic" wire:model="form.topic" type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.topic')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="mp_platform" value="المنصّة" />
                    <x-text-input id="mp_platform" wire:model="form.platform" type="text" class="mt-1 block w-full" placeholder="LinkedIn / X / …" />
                </div>
            </div>

            <div>
                <x-input-label for="mp_content" value="المحتوى" />
                <textarea id="mp_content" wire:model="form.content" rows="5" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="نص البوست…"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="mp_status" value="الحالة" />
                    <select id="mp_status" wire:model.live="form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="mp_date" value="موعد النشر" />
                    <x-text-input id="mp_date" wire:model="form.scheduled_for" type="date" class="mt-1 block w-full" />
                </div>
            </div>

            @if ($form->status === 'published')
                <div>
                    <x-input-label for="mp_link" value="رابط البوست المنشور" />
                    <x-text-input id="mp_link" wire:model="form.link" type="url" class="mt-1 block w-full" placeholder="https://…" dir="ltr" />
                    <x-input-error :messages="$errors->get('form.link')" class="mt-1" />
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
