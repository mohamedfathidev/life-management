<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('recovery.index') }}" wire:navigate class="inline-block text-sm text-primary dark:text-primary-dark hover:underline mb-6">← العودة للتعافي</a>

        <div class="mb-8 text-center">
            <div class="inline-block px-4 py-1.5 rounded-full bg-ink/10 dark:bg-white/10 text-ink dark:text-ink-dark text-xs font-semibold mb-3 tracking-wide">
                🌍 فانية
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-ink dark:text-ink-dark mb-2">النعم اللي بغفل عنها</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft max-w-md mx-auto leading-relaxed">
                الدنيا دي فانية، ونعمها بتزول — اكتب كل نعمة قبل ما تتعوّد عليها وتنساها
            </p>
        </div>

        {{-- Add / edit form --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mb-8">
            <form wire:submit="save" class="space-y-3">
                <textarea wire:model="text" rows="2"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="نعمة بتنساها... (زي: بيت يسترني، ناس بتحبني، عقل سليم...)"></textarea>
                <x-input-error :messages="$errors->get('text')" class="mt-1" />
                <div class="flex items-center justify-end gap-3">
                    @if ($editingId)
                        <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    @endif
                    <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">
                        {{ $editingId ? 'احفظ التعديل' : '+ إضافة' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- List --}}
        @if ($blessings->isEmpty())
            <div class="text-center py-16 rounded-3xl border-2 border-dashed border-primary/25 dark:border-primary-dark/25">
                <p class="text-5xl mb-3">🤍</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه ماكتبتش نعمة — ابدأ بواحدة فوق.</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($blessings as $b)
                    <div wire:key="bl-{{ $b->id }}" class="flex items-start gap-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                        <span class="text-lg shrink-0">🤍</span>
                        <p class="flex-1 text-sm text-ink dark:text-ink-dark leading-relaxed">{{ $b->text }}</p>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="edit({{ $b->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="delete({{ $b->id }})" wire:confirm="حذف النعمة دي؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
