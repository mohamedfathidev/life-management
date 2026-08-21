<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">⛓️ أخطاء التعافي</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">الأخطاء اللي بتبقيك في السجن — حدّد لكل خطأ نسبة قد إيه بيأثّر، وواجه الأكبر الأول.</p>
        </div>

        {{-- Add / edit form --}}
        <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 space-y-4 mb-6">
            <div>
                <x-input-label for="m_title" :value="$editingId ? 'تعديل الخطأ' : 'خطأ جديد'" />
                <x-text-input id="m_title" wire:model="title" type="text" class="mt-1 block w-full" placeholder="مثال: السهر لوحدي على الموبايل بالليل" />
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div x-data="{ w: @entangle('weight') }">
                <div class="flex justify-between items-center">
                    <x-input-label value="قد إيه بيبقيك في السجن؟" />
                    <span class="text-sm font-bold text-danger" x-text="w + '%'"></span>
                </div>
                <input type="range" min="0" max="100" step="5" x-model.number="w" class="mt-2 block w-full accent-danger" dir="ltr" />
                <x-input-error :messages="$errors->get('weight')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-1">
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                @endif
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة' }}</button>
            </div>
        </form>

        {{-- List --}}
        @if ($mistakes->isEmpty())
            <div class="text-center py-12 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">اكتب أول خطأ بيعطّل تعافيك.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($mistakes as $mistake)
                    <div wire:key="mistake-{{ $mistake->id }}" class="group rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-danger/20 p-4 transition-all duration-200">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('recovery.mistakes.show', $mistake) }}" wire:navigate class="min-w-0 flex-1">
                                <h3 class="text-base font-bold text-ink dark:text-ink-dark group-hover:text-danger transition">
                                    {{ $mistake->title }}
                                </h3>
                            </a>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('recovery.mistakes.show', $mistake) }}" wire:navigate class="text-xs font-semibold text-primary dark:text-primary-dark hover:underline">
                                    التفاصيل
                                </a>
                                <button type="button" wire:click="edit({{ $mistake->id }})" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:underline">تعديل السريع</button>
                                <button type="button" wire:click="delete({{ $mistake->id }})" wire:confirm="حذف الخطأ؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>

                        {{-- Weight percentage bar linking to show details --}}
                        <a href="{{ route('recovery.mistakes.show', $mistake) }}" wire:navigate class="mt-3 flex items-center gap-3 block group-hover:opacity-95 transition">
                            <div class="flex-1 h-3 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-rose-500 to-danger transition-all duration-300" style="width: {{ $mistake->weight }}%"></div>
                            </div>
                            <span class="text-sm font-extrabold text-danger w-10 text-start">{{ $mistake->weight }}%</span>
                        </a>
                    </div>
                @endforeach
            </div>

            @if ($mistakes->hasPages())
                <div class="mt-6">{{ $mistakes->links() }}</div>
            @endif
        @endif
    </div>
</div>
