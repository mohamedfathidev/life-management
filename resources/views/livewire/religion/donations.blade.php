<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('religion') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الدين</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">الصدقات</h1>
            </div>
            <button type="button" wire:click="openCreate" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ صدقة</button>
        </div>

        {{-- Totals --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="rounded-2xl bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent shadow-sm p-6 text-center">
                <p class="text-2xl font-bold text-success">{{ number_format($total, 2) }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">الإجمالي</p>
            </div>
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 text-center">
                <p class="text-2xl font-bold text-ink dark:text-ink-dark">{{ number_format($monthTotal, 2) }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">هذا الشهر</p>
            </div>
        </div>

        <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            @forelse ($donations as $d)
                <div wire:key="don-{{ $d->id }}" class="flex items-start justify-between gap-3 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                    <div>
                        <p class="text-sm text-ink dark:text-ink-dark flex items-center gap-2">
                            <span class="font-semibold">{{ number_format($d->amount, 2) }}</span>
                            @if ($d->cause)<span class="text-ink-soft dark:text-ink-dark-soft">— {{ $d->cause }}</span>@endif
                            @if ($d->is_recurring)<span class="text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">متكرر</span>@endif
                        </p>
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $d->date->translatedFormat('j M Y') }}</p>
                        @if ($d->note)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $d->note }}</p>@endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="editDonation({{ $d->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="deleteDonation({{ $d->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">مفيش صدقات مسجّلة لسه.</p>
            @endforelse
        </div>
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('open') }" x-show="open" x-cloak @keydown.escape.window="open && $wire.close()" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>
        <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->donation ? 'تعديل الصدقة' : 'صدقة جديدة' }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="d_amount" value="المبلغ" />
                        <x-text-input id="d_amount" wire:model="form.amount" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('form.amount')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="d_date" value="التاريخ" />
                        <x-text-input id="d_date" wire:model="form.date" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('form.date')" class="mt-1" />
                    </div>
                </div>
                <div>
                    <x-input-label for="d_cause" value="الجهة / السبب (اختياري)" />
                    <x-text-input id="d_cause" wire:model="form.cause" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="d_note" value="ملاحظة (اختياري)" />
                    <textarea id="d_note" wire:model="form.note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-ink dark:text-ink-dark">
                    <input type="checkbox" wire:model="form.is_recurring" class="rounded border-gray-300 text-primary focus:ring-primary" />
                    صدقة متكررة
                </label>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
