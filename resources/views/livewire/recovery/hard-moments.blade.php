<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>

        {{-- Hero --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-warning/15 via-danger/10 to-transparent dark:from-warning-dark/20 dark:via-danger-dark/10 p-6 sm:p-8 mt-3 mb-6 text-center">
            <div class="absolute -top-10 -end-10 w-40 h-40 rounded-full bg-warning/20 dark:bg-warning-dark/20 blur-2xl"></div>
            <div class="absolute -bottom-14 -start-14 w-48 h-48 rounded-full bg-danger/15 dark:bg-danger-dark/15 blur-2xl"></div>
            <p class="relative text-2xl sm:text-3xl font-extrabold text-ink dark:text-ink-dark leading-snug">
                ⚡ أصعب اللحظات
            </p>
            <p class="relative text-sm text-ink-soft dark:text-ink-dark-soft mt-3 max-w-md mx-auto leading-relaxed">
                اللحظات اللي بتضعّفك بتتكرر — سجّلها واكتب خطة مواجهة جاهزة، عشان لما تيجي تاني تكون مستعد مش متفاجئ.
            </p>
        </div>

        {{-- Quick add / edit --}}
        <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 space-y-3 mb-8">
            <x-input-label for="hm_title" :value="$editingId ? 'تعديل اللحظة' : 'لحظة صعبة جديدة'" />
            <x-text-input id="hm_title" wire:model="title" type="text" class="block w-full" placeholder="مثال: السهر لوحدي بالليل" />
            <x-input-error :messages="$errors->get('title')" />

            <textarea wire:model="description" rows="2" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="امتى وليه بتحصل اللحظة دي؟ (اختياري)"></textarea>
            <x-input-error :messages="$errors->get('description')" />

            <div class="flex items-center justify-end gap-3 pt-1">
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                @endif
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة' }}</button>
            </div>
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft">اكتب خطة المواجهة التفصيلية بعد كده من صفحة اللحظة نفسها.</p>
        </form>

        {{-- List --}}
        @if ($moments->isEmpty())
            <div class="text-center py-16 rounded-2xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">⚡</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش لحظات مسجّلة. ابدأ بأول لحظة بتضعّفك.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($moments as $moment)
                    <div wire:key="hm-{{ $moment->id }}" class="group rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-warning/30 dark:hover:border-warning-dark/30 p-4 transition-all duration-200">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('recovery.hard-moments.show', $moment) }}" wire:navigate class="min-w-0 flex-1">
                                <h3 class="text-base font-bold text-ink dark:text-ink-dark group-hover:text-warning transition">⚡ {{ $moment->title }}</h3>
                                @if ($moment->description)
                                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 line-clamp-2">{{ $moment->description }}</p>
                                @endif
                                @if ($moment->plan)
                                    <span class="inline-block mt-2 text-[11px] px-2 py-0.5 rounded-full bg-success/15 text-success">✓ فيه خطة مواجهة</span>
                                @else
                                    <span class="inline-block mt-2 text-[11px] px-2 py-0.5 rounded-full bg-warning/15 text-warning">لسه من غير خطة</span>
                                @endif
                            </a>
                            <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('recovery.hard-moments.show', $moment) }}" wire:navigate class="text-xs font-semibold text-primary dark:text-primary-dark hover:underline">التفاصيل</a>
                                <button type="button" wire:click="edit({{ $moment->id }})" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:underline">تعديل سريع</button>
                                <button type="button" wire:click="delete({{ $moment->id }})" wire:confirm="حذف اللحظة دي وخطتها؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($moments->hasPages())
                <div class="mt-6">{{ $moments->links() }}</div>
            @endif
        @endif
    </div>
</div>
