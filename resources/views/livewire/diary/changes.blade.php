<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <a href="{{ route('diary.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← المذكرات</a>

        {{-- Hero --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-success/15 via-primary/10 to-transparent dark:from-success-dark/20 dark:via-primary-dark/10 p-6 sm:p-8 text-center">
            <div class="absolute -top-10 -start-10 w-40 h-40 rounded-full bg-success/20 dark:bg-success-dark/20 blur-2xl"></div>
            <div class="absolute -bottom-14 -end-14 w-48 h-48 rounded-full bg-primary/20 dark:bg-primary-dark/20 blur-2xl"></div>
            <p class="relative text-2xl sm:text-3xl font-extrabold text-ink dark:text-ink-dark leading-snug">
                🌱 إيه اللي غيّرني؟
            </p>
            <p class="relative text-sm text-ink-soft dark:text-ink-dark-soft mt-3 max-w-md mx-auto leading-relaxed">
                مش كلام بس — حاجات حقيقية عملتها أو فهمتها وحسّيت إنها فرقت معاك. سجّلها هنا عشان ترجعلها.
            </p>
        </div>

        {{-- List --}}
        @if ($changes->isEmpty())
            <div class="text-center py-12 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش حاجة مكتوبة. ابدأ بأول حاجة فعلًا غيّرتك تحت.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($changes as $change)
                    @if ($change->is_important)
                        {{-- Starred = one of the most important changes: a standout circle instead of a row --}}
                        <div wire:key="change-{{ $change->id }}" class="group relative w-36 h-36 sm:w-40 sm:h-40 mx-auto rounded-full bg-gradient-to-br from-success/15 to-primary/10 dark:from-success-dark/20 dark:to-primary-dark/10 border-2 border-success/50 dark:border-success-dark/50 shadow-md flex items-center justify-center text-center p-4 overflow-hidden transition-transform hover:scale-105">
                            <span class="absolute top-2 end-3 text-lg">⭐</span>
                            <p class="text-xs sm:text-sm font-semibold text-ink dark:text-ink-dark leading-snug line-clamp-5">{{ $change->body }}</p>

                            <div class="absolute inset-0 rounded-full bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-3 text-white text-xs">
                                <button type="button" wire:click="toggleImportant({{ $change->id }})" title="شيلها من الأهم">☆</button>
                                <button type="button" wire:click="edit({{ $change->id }})" class="hover:underline">تعديل</button>
                                <button type="button" wire:click="delete({{ $change->id }})" wire:confirm="حذف الحاجة دي؟" class="hover:underline">حذف</button>
                            </div>
                        </div>
                    @else
                        <div wire:key="change-{{ $change->id }}" class="group flex items-start gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-success/20 dark:hover:border-success-dark/20 p-4 transition-all duration-200">
                            <span class="mt-0.5 text-success dark:text-success-dark shrink-0">🌿</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-ink dark:text-ink-dark leading-relaxed">{{ $change->body }}</p>
                                <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">{{ $change->created_at->translatedFormat('j M Y') }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition">
                                <button type="button" wire:click="toggleImportant({{ $change->id }})" class="text-sm text-warning hover:scale-110 transition" title="علّمها كأهم حاجة">☆</button>
                                <button type="button" wire:click="edit({{ $change->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="delete({{ $change->id }})" wire:confirm="حذف الحاجة دي؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Add / edit form --}}
        <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 space-y-3">
            <x-input-label :value="$editingId ? 'تعديل' : 'حاجة جديدة غيّرتك'" />
            <textarea wire:model="body" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="مثال: الأفعال حتى لو بسيطة بتدي ثقة أكتر بكتير من الكلام…"></textarea>
            <x-input-error :messages="$errors->get('body')" />

            <div class="flex items-center justify-end gap-3 pt-1">
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                @endif
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة' }}</button>
            </div>
        </form>
    </div>
</div>
