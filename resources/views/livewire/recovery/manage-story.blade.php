<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-2xl rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">
            {{ $form->story ? 'تعديل الحكاية' : 'حكاية جديدة' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rs_date" value="التاريخ" />
                    <x-text-input id="rs_date" wire:model="form.date" type="date" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.date')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="rs_mood" value="المزاج (١–١٠)" />
                    <x-text-input id="rs_mood" wire:model="form.mood" type="number" min="1" max="10" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.mood')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="rs_recovery" value="فترة التعافي (اختياري)" />
                <select id="rs_recovery" wire:model="form.recovery_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">بدون ربط بفترة</option>
                    @foreach ($recoveries as $recovery)
                        <option value="{{ $recovery->id }}">
                            {{ $recovery->title }}
                            (من {{ $recovery->start_date->translatedFormat('j M') }} @if ($recovery->end_date) إلى {{ $recovery->end_date->translatedFormat('j M Y') }} @else - مفتوحة @endif)
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('form.recovery_id')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="rs_title" value="العنوان (اختياري)" />
                <x-text-input id="rs_title" wire:model="form.title" type="text" class="mt-1 block w-full" />
            </div>

            <div>
                <x-input-label for="rs_brief" value="الحكاية دي بتتكلم عن ايه؟ (ملخص مختصر)" />
                <x-text-input id="rs_brief" wire:model="form.brief" type="text" maxlength="300" class="mt-1 block w-full" placeholder="سطر واحد يلخّص فكرة الحكاية…" />
                <x-input-error :messages="$errors->get('form.brief')" class="mt-1" />
            </div>

            {{-- Rich-text editor (Trix) --}}
            <div>
                <x-input-label value="الحكاية" />
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
                    <input id="story_content_input" type="hidden">
                    <trix-editor x-ref="trix" input="story_content_input" placeholder="اكتب حكايتك… (تقدر تستخدم عناوين ونقاط)" class="trix-content"></trix-editor>
                </div>
                <x-input-error :messages="$errors->get('form.content')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="rs_tags" value="الوسوم (مثال: #أسبوعي_الأول #أمل)" />
                <x-text-input id="rs_tags" wire:model="form.tagsInput" type="text" class="mt-1 block w-full" placeholder="#أسبوعي_الأول #أمل #صراع" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>