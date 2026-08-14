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
            {{ $form->task ? 'تعديل التاسك' : 'تاسك جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            {{-- Title --}}
            <div>
                <x-input-label for="task_title" value="عنوان التاسك" />
                <x-text-input id="task_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: مذاكرة فصل ٣، مشوار البنك…" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            {{-- Planned time (optional) → expected duration + day timeline --}}
            <div x-data="{
                    s: @entangle('form.start_time'),
                    e: @entangle('form.end_time'),
                    get dur() {
                        if (! this.s || ! this.e) return '';
                        const [sh, sm] = this.s.split(':').map(Number);
                        const [eh, em] = this.e.split(':').map(Number);
                        let mins = (eh * 60 + em) - (sh * 60 + sm);
                        if (mins <= 0) return '';
                        const h = Math.floor(mins / 60), m = mins % 60;
                        return (h ? h + ' س ' : '') + (m ? m + ' د' : '');
                    }
                 }">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="task_start" value="من (اختياري)" />
                        <input id="task_start" type="time" wire:model="form.start_time" x-model="s" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" dir="ltr" />
                        <x-input-error :messages="$errors->get('form.start_time')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="task_end" value="إلى (اختياري)" />
                        <input id="task_end" type="time" wire:model="form.end_time" x-model="e" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" dir="ltr" />
                        <x-input-error :messages="$errors->get('form.end_time')" class="mt-1" />
                    </div>
                </div>
                <p x-show="dur" x-cloak class="mt-2 text-xs text-primary dark:text-primary-dark" x-text="'⏱️ المدة المتوقعة: ' + dur"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Goal link (optional) --}}
                <div>
                    <x-input-label for="task_goal" value="مربوط بهدف (اختياري)" />
                    <select id="task_goal" wire:model="form.goal_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="">— بدون هدف —</option>
                        @foreach ($goals as $goal)
                            <option value="{{ $goal->id }}">{{ $goal->title }}</option>
                            @if ($goal->children->isNotEmpty())
                                <optgroup label="↳ أهداف فرعية لـ «{{ $goal->title }}»">
                                    @foreach ($goal->children as $child)
                                        <option value="{{ $child->id }}">&nbsp;&nbsp;↳ {{ $child->title }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">اختَر هدف فرعي مباشرةً ومش لازم تختار الكبير.</p>
                    <x-input-error :messages="$errors->get('form.goal_id')" class="mt-1" />
                </div>

                {{-- Kind --}}
                <div>
                    <x-input-label for="task_kind" value="النوع" />
                    <select id="task_kind" wire:model="form.kind" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($kinds as $kind)
                            <option value="{{ $kind->value }}">{{ $kind->emoji() }} {{ $kind->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.kind')" class="mt-1" />
                </div>
            </div>

            {{-- Most important task --}}
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="form.is_important" class="rounded border-gray-300 dark:border-gray-600 text-warning focus:ring-warning" />
                <span class="text-sm text-ink dark:text-ink-dark">⭐ أهم تاسك النهاردة (يتميّز بلون بارز)</span>
            </label>

            {{-- Progress --}}
            <div x-data="{ p: @entangle('form.progress') }">
                <div class="flex justify-between">
                    <x-input-label value="نسبة الإنجاز" />
                    <span class="text-sm text-ink-soft dark:text-ink-dark-soft" x-text="p + '%'"></span>
                </div>
                <input type="range" min="0" max="100" step="5" x-model.number="p" class="mt-2 block w-full accent-primary" dir="ltr" />
                <x-input-error :messages="$errors->get('form.progress')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
