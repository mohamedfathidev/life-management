<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-2xl rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">تعديل الخطأ</h2>

        <form wire:submit="save" x-data="{ noteValue: @entangle('note') }" class="space-y-4">
            <div>
                <x-input-label for="mm_title" value="عنوان الخطأ" />
                <x-text-input id="mm_title" wire:model="title" type="text" class="mt-1 block w-full" placeholder="مثال: السهر لوحدي على الموبايل بالليل" />
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div x-data="{ w: @entangle('weight') }">
                <div class="flex justify-between items-center">
                    <x-input-label value="قد إيه بيعطّلك ويبقيك في السجن؟" />
                    <span class="text-sm font-extrabold text-danger px-2.5 py-0.5 rounded-full bg-danger/10 border border-danger/20" x-text="w + '%'"></span>
                </div>
                <input type="range" min="0" max="100" step="5" x-model.number="w" class="mt-2 block w-full accent-danger cursor-pointer" dir="ltr" />
                <x-input-error :messages="$errors->get('weight')" class="mt-1" />
            </div>

            <div>
                <x-input-label value="الملاحظات والتفاصيل (إزاي أواجهه وأتجنّبه)" />
                <div wire:ignore class="mt-1"
                     x-data="{
                        init() {
                            const el = this.$refs.trix;
                            const load = () => { if (el.editor && el.value !== (noteValue || '')) el.editor.loadHTML(noteValue || ''); };
                            el.addEventListener('trix-initialize', load);
                            el.addEventListener('trix-change', () => { noteValue = el.value; });
                            this.$watch('noteValue', () => load());
                        }
                     }">
                    <input id="mistake_modal_note_input" type="hidden">
                    <trix-editor x-ref="trix" input="mistake_modal_note_input" placeholder="اكتب هنا التفاصيل الكاملة، الأسباب، وخطوات لتجنب هذا الخطأ مستقبلاً…" class="trix-content"></trix-editor>
                </div>
                <x-input-error :messages="$errors->get('note')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-danger text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
