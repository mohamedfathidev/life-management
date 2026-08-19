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
            {{ $form->topic ? 'تعديل الموضوع' : 'موضوع جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div>
                <x-input-label for="topic_title" value="العنوان" />
                <x-text-input id="topic_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: أصعب اللحظات في اليوم" />
                <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
            </div>

            {{-- Rich-text editor (Trix) --}}
            <div>
                <x-input-label value="المحتوى" />
                <div wire:ignore class="mt-1"
                     x-data="{
                        value: @entangle('form.content'),
                        init() {
                            const el = this.$refs.trix;
                            const load = () => { if (el.editor && el.value !== (this.value || '')) el.editor.loadHTML(this.value || ''); };
                            el.addEventListener('trix-initialize', load);
                            el.addEventListener('trix-change', () => { this.value = el.value; });
                            this.$watch('value', () => load());
                        }
                     }">
                    <input id="topic_content_input" type="hidden">
                    <trix-editor x-ref="trix" input="topic_content_input" placeholder="اكتب اللي اتعلمته… (تقدر تستخدم عناوين ونقاط)" class="trix-content"></trix-editor>
                </div>
                <x-input-error :messages="$errors->get('form.content')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="topic_importance" value="مستوى الأهمية" />
                    <select id="topic_importance" wire:model="form.importance" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        @foreach ($importances as $importance)
                            <option value="{{ $importance->value }}">{{ $importance->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('form.importance')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="topic_tags" value="الوسوم (مثال: #السهر #افكار_الحب_الشهواني)" />
                    <x-text-input id="topic_tags" wire:model="form.tagsInput" type="text" class="mt-1 block w-full" placeholder="#السهر #محفزات #افكار" />
                    <x-input-error :messages="$errors->get('form.tagsInput')" class="mt-1" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
