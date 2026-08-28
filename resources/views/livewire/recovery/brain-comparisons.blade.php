<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 text-center">
            <a href="{{ route('recovery.index') }}" wire:navigate class="inline-block text-sm text-primary dark:text-primary-dark hover:underline mb-4">← العودة للتعافي</a>
            <h1 class="text-3xl sm:text-4xl font-bold text-ink dark:text-ink-dark mb-2">
                🧠 الدماغ الإدماني ضد دماغي الطبيعية
            </h1>
            <p class="text-base text-ink-soft dark:text-ink-dark-soft max-w-xl mx-auto leading-relaxed">
                نفس النقطة، بس اتنين بيفكروا فيها بشكل مختلف تمامًا — دماغ اتعود على الإدمان، ودماغك الطبيعي اللي بيعرف مصلحتك الحقيقية
            </p>
        </div>

        {{-- Add / edit form --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mb-8">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">{{ $editingId ? 'عدّل المقارنة' : 'ضيف مقارنة جديدة' }}</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <x-input-label for="bc_point" value="النقطة (مثلاً: الراحة، المتعة، المستقبل...)" />
                    <x-text-input id="bc_point" wire:model="point" type="text" class="mt-1 block w-full" placeholder="النقطة اللي بتقارن فيها" />
                    <x-input-error :messages="$errors->get('point')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="bc_addictive" value="🔴 دماغي وقت الإدمان عايز إيه؟" class="text-danger dark:text-danger-dark" />
                        <textarea id="bc_addictive" wire:model="addictiveText" rows="3"
                            class="mt-1 block w-full rounded-lg border-danger/30 dark:border-danger-dark/30 dark:bg-gray-800 dark:text-ink-dark focus:border-danger focus:ring-danger text-sm" placeholder="إيه اللي الدماغ الإدماني بيدور عليه في النقطة دي؟"></textarea>
                        <x-input-error :messages="$errors->get('addictiveText')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="bc_normal" value="🟢 دماغي الطبيعية عايزة إيه؟" class="text-success dark:text-success-dark" />
                        <textarea id="bc_normal" wire:model="normalText" rows="3"
                            class="mt-1 block w-full rounded-lg border-success/30 dark:border-success-dark/30 dark:bg-gray-800 dark:text-ink-dark focus:border-success focus:ring-success text-sm" placeholder="إيه اللي بمصلحتك الحقيقية في النقطة دي؟"></textarea>
                        <x-input-error :messages="$errors->get('normalText')" class="mt-1" />
                    </div>
                </div>

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
        @if ($comparisons->isEmpty())
            <div class="text-center py-16 rounded-3xl border-2 border-dashed border-primary/25 dark:border-primary-dark/25">
                <p class="text-5xl mb-3">🧠</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش مقارنات — ضيف أول واحدة فوق.</p>
            </div>
        @else
            <div class="space-y-5">
                @foreach ($comparisons as $c)
                    <div wire:key="bc-{{ $c->id }}" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between gap-3 px-5 py-3 bg-primary/5 dark:bg-primary-dark/10 border-b border-primary/10 dark:border-primary-dark/10">
                            <h3 class="font-semibold text-ink dark:text-ink-dark">⚖️ {{ $c->point }}</h3>
                            <div class="flex items-center gap-3 shrink-0">
                                <button type="button" wire:click="edit({{ $c->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="delete({{ $c->id }})" wire:confirm="حذف المقارنة دي؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-5">
                            <div class="rounded-xl bg-danger/10 dark:bg-danger-dark/10 border border-danger/20 dark:border-danger-dark/20 p-4">
                                <p class="text-xs font-bold text-danger dark:text-danger-dark mb-1.5">🔴 دماغي وقت الإدمان</p>
                                <p class="text-sm text-ink dark:text-ink-dark leading-relaxed">{{ $c->addictive_text }}</p>
                            </div>
                            <div class="rounded-xl bg-success/10 dark:bg-success-dark/10 border border-success/20 dark:border-success-dark/20 p-4">
                                <p class="text-xs font-bold text-success dark:text-success-dark mb-1.5">🟢 دماغي الطبيعية</p>
                                <p class="text-sm text-ink dark:text-ink-dark leading-relaxed">{{ $c->normal_text }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($comparisons->hasPages())
                <div class="mt-6">{{ $comparisons->links() }}</div>
            @endif
        @endif
    </div>
</div>
