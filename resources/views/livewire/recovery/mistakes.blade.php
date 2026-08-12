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

            <div>
                <x-input-label for="m_note" value="ملاحظة / إزاي أتجنّبه (اختياري)" />
                <textarea id="m_note" wire:model="note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3">
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
                    <div wire:key="mistake-{{ $mistake->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-medium text-ink dark:text-ink-dark">{{ $mistake->title }}</p>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="edit({{ $mistake->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="delete({{ $mistake->id }})" wire:confirm="حذف الخطأ؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="flex-1 h-2.5 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                                <div class="h-full rounded-full bg-danger" style="width: {{ $mistake->weight }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-danger w-10 text-start">{{ $mistake->weight }}%</span>
                        </div>
                        @if ($mistake->note)<p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2 whitespace-pre-line">{{ $mistake->note }}</p>@endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
