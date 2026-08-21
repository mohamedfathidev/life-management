<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->project ? 'تعديل المشروع' : 'فكرة جديدة' }}</h2>

        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="pj_title" value="اسم المشروع / الفكرة" />
                <x-text-input id="pj_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: تطبيق يعرض أماكن للناس" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="pj_pitch" value="الفكرة في سطرين (اختياري)" />
                <textarea id="pj_pitch" wire:model="form.pitch" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div>
                <x-input-label for="pj_why" value="ليه عايز تعملها؟ (اختياري)" />
                <textarea id="pj_why" wire:model="form.why" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="pj_status" value="الحالة" />
                    <select id="pj_status" wire:model="form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->emoji() }} {{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="pj_goal" value="مربوطة بهدف (اختياري)" />
                    <select id="pj_goal" wire:model="form.goal_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="">— بدون —</option>
                        @foreach ($goals as $goal)
                            <option value="{{ $goal->id }}">{{ $goal->parent_id ? '— ' : '' }}{{ $goal->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <x-input-label for="pj_url" value="الرابط لما تطلع للنور (اختياري)" />
                <x-text-input id="pj_url" wire:model="form.url" type="url" class="mt-1 block w-full" placeholder="https://…" dir="ltr" />
                <x-input-error :messages="$errors->get('form.url')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
