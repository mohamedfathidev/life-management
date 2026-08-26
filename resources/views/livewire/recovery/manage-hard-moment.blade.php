<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-2xl rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">تعديل اللحظة</h2>

        <form wire:submit="save" x-data="{ planValue: @entangle('plan') }" class="space-y-4">
            <div>
                <x-input-label for="hmm_title" value="اللحظة" />
                <x-text-input id="hmm_title" wire:model="title" type="text" class="mt-1 block w-full" placeholder="مثال: السهر لوحدي بالليل" />
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="hmm_description" value="امتى وليه بتحصل اللحظة دي؟" />
                <textarea id="hmm_description" wire:model="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-warning focus:ring-warning text-sm" placeholder="مثلًا: بعد يوم متعب، أو لما بكون لوحدي فاضي…"></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div>
                <x-input-label value="خطة المواجهة — لما اللحظة دي تيجي، هعمل إيه؟" />
                <div wire:ignore class="mt-1"
                     x-data="{
                        init() {
                            const el = this.$refs.trix;
                            const load = () => { if (el.editor && el.value !== (planValue || '')) el.editor.loadHTML(planValue || ''); };
                            el.addEventListener('trix-initialize', load);
                            el.addEventListener('trix-change', () => { planValue = el.value; });
                            this.$watch('planValue', () => load());
                        }
                     }">
                    <input id="hard_moment_modal_plan_input" type="hidden">
                    <trix-editor x-ref="trix" input="hard_moment_modal_plan_input" placeholder="مثال: 1) أقوم أعمل وضوء وأصلي ركعتين. 2) أكلم حد أثق فيه. 3) أخرج امشي 10 دقايق…" class="trix-content"></trix-editor>
                </div>
                <x-input-error :messages="$errors->get('plan')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-warning text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>
