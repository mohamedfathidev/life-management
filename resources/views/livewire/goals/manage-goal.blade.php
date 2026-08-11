<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-black/40"
        wire:click="close"
    ></div>

    {{-- Dialog --}}
    <div
        x-show="open"
        x-transition
        class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto"
    >
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">
            {{ $form->goal ? 'تعديل الهدف' : ($form->parent_id ? 'هدف فرعي جديد' : 'هدف جديد') }}
        </h2>

        @if ($form->parent_id && ! $form->goal)
            <p class="text-xs text-primary dark:text-primary-dark bg-primary/10 rounded-lg px-3 py-2 mb-4">
                سيتم إضافة هذا كهدف فرعي داخل الهدف الرئيسي.
            </p>
        @endif

        <form wire:submit="save" class="space-y-4">
            {{-- Title --}}
            <div>
                <x-input-label for="title" value="العنوان" />
                <x-text-input id="title" wire:model="form.title" type="text" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Category --}}
                <div>
                    <x-input-label for="category" value="الفئة" />
                    <select id="category" wire:model="form.category" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($categories as $category)
                            <option value="{{ $category->value }}">{{ $category->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.category')" class="mt-1" />
                </div>

                {{-- Status --}}
                <div>
                    <x-input-label for="status" value="الحالة" />
                    <select id="status" wire:model="form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.status')" class="mt-1" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Start date --}}
                <div>
                    <x-input-label for="start_date" value="تاريخ البداية" />
                    <x-text-input id="start_date" wire:model="form.start_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.start_date')" class="mt-1" />
                </div>

                {{-- End date --}}
                <div>
                    <x-input-label for="target_date" value="تاريخ النهاية" />
                    <x-text-input id="target_date" wire:model="form.target_date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.target_date')" class="mt-1" />
                </div>
            </div>

            {{-- Color --}}
            <div>
                <x-input-label for="color" value="اللون" />
                <input id="color" wire:model="form.color" type="color" class="mt-1 block w-full h-10 rounded-md border border-gray-300 dark:border-gray-600 bg-transparent cursor-pointer" />
                <x-input-error :messages="$errors->get('form.color')" class="mt-1" />
            </div>

            {{-- Description --}}
            <div>
                <x-input-label for="description" value="الوصف (اختياري)" />
                <textarea id="description" wire:model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                <x-input-error :messages="$errors->get('form.description')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
