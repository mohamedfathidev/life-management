<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>

        {{-- Hero --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-success/15 via-primary/10 to-transparent dark:from-success-dark/20 dark:via-primary-dark/10 p-6 sm:p-8 text-center">
            <div class="absolute -top-10 -end-10 w-40 h-40 rounded-full bg-success/20 dark:bg-success-dark/20 blur-2xl"></div>
            <div class="absolute -bottom-14 -start-14 w-48 h-48 rounded-full bg-primary/20 dark:bg-primary-dark/20 blur-2xl"></div>
            <p class="relative text-2xl sm:text-3xl font-extrabold text-ink dark:text-ink-dark leading-snug">
                📜 حاجات لازم تلتزم بيها
            </p>
            <p class="relative text-sm text-ink-soft dark:text-ink-dark-soft mt-3 max-w-md mx-auto leading-relaxed">
                قواعدك الشخصية اللي بتحمي تعافيك — ارجعلها كل ما تحتاج تفتكر نفسك بيها.
            </p>
        </div>

        {{-- List --}}
        @if ($commitments->isEmpty())
            <div class="text-center py-12 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش التزامات مكتوبة. ابدأ بأول قاعدة تحت.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($commitments as $commitment)
                    <div wire:key="commitment-{{ $commitment->id }}" class="group flex items-start gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-success/30 dark:hover:border-success-dark/30 p-4 transition-all duration-200">
                        <span class="mt-0.5 flex items-center justify-center w-6 h-6 rounded-full bg-success/15 text-success text-xs font-bold shrink-0">✓</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-ink dark:text-ink-dark leading-relaxed">{{ $commitment->title }}</p>
                            @if ($commitment->description)
                                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 leading-relaxed whitespace-pre-line">{{ $commitment->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition">
                            <button type="button" wire:click="edit({{ $commitment->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="delete({{ $commitment->id }})" wire:confirm="حذف الالتزام ده؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Add / edit form --}}
        <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 space-y-3">
            <x-input-label for="cm_title" :value="$editingId ? 'تعديل الالتزام' : 'التزام جديد'" />
            <x-text-input id="cm_title" wire:model="title" type="text" class="block w-full" placeholder="مثال: ملهاش موبايل في السرير بعد العشاء" />
            <x-input-error :messages="$errors->get('title')" />

            <textarea wire:model="description" rows="2" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="تفاصيل إضافية (اختياري)"></textarea>
            <x-input-error :messages="$errors->get('description')" />

            <div class="flex items-center justify-end gap-3 pt-1">
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                @endif
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة' }}</button>
            </div>
        </form>
    </div>
</div>
