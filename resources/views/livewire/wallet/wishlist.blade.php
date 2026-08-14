<div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
    <div class="flex items-center justify-between gap-3 mb-1">
        <h3 class="font-semibold text-ink dark:text-ink-dark">🛒 ضروريات فقط</h3>
        <button type="button" wire:click="openForm" class="text-sm px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90 transition">+ حاجة</button>
    </div>
    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-4">حاجات محتاج تشتريها بأولوياتها — مابتخصمش من رصيدك إلا لما تشتريها فعلًا.</p>

    @if ($pendingTotal > 0)
        <div class="rounded-xl bg-bg-light dark:bg-bg-dark p-3 mb-4 text-sm">
            <span class="text-ink-soft dark:text-ink-dark-soft">إجمالي المطلوب (تقديري):</span>
            <span class="font-bold text-ink dark:text-ink-dark">{{ number_format($pendingTotal, 2) }}</span>
        </div>
    @endif

    {{-- Add / edit form --}}
    @if ($open)
        <form wire:submit="save" class="rounded-xl bg-bg-light dark:bg-bg-dark p-4 mb-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <x-text-input wire:model="title" type="text" class="block w-full" placeholder="الحاجة (مثال: لابتوب)" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>
                <x-text-input wire:model="estimated_price" type="number" step="0.01" min="0" class="block w-full" placeholder="السعر (تقديري)" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <select wire:model="importance" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    @foreach ($importanceLevels as $key => $meta)
                        <option value="{{ $key }}">{{ $meta['label'] }}</option>
                    @endforeach
                </select>
                <x-text-input wire:model="note" type="text" class="block w-full" placeholder="ملاحظة (اختياري)" />
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" wire:click="$set('open', false)" class="text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة' }}</button>
            </div>
        </form>
    @endif

    {{-- Pending list --}}
    @if ($pending->isEmpty() && $bought->isEmpty())
        <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-6">مفيش حاجات في القائمة — ضيف اللي محتاجه.</p>
    @else
        <div class="space-y-2">
            @foreach ($pending as $item)
                @php($m = $item->importanceMeta())
                <div wire:key="wish-{{ $item->id }}" class="flex items-center gap-3 rounded-xl bg-bg-light dark:bg-bg-dark p-3">
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-{{ $m['color'] }}/15 text-{{ $m['color'] }} shrink-0">{{ $m['label'] }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink dark:text-ink-dark truncate">{{ $item->title }}</p>
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft">@if ($item->estimated_price)~ {{ number_format($item->estimated_price, 2) }}@endif @if ($item->note)· {{ $item->note }}@endif</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="buy({{ $item->id }})" wire:confirm="اشتريتها؟ هيتسجّل مصروف {{ $item->estimated_price ? number_format($item->estimated_price, 2) : '' }}." class="text-xs px-3 py-1.5 rounded-lg bg-success/15 text-success hover:bg-success/25 transition">اشتريتها ✓</button>
                        <button type="button" wire:click="edit({{ $item->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bought --}}
        @if ($bought->isNotEmpty())
            <div x-data="{ o: false }" class="mt-4">
                <button type="button" @click="o = ! o" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">تم شراؤها ({{ $bought->count() }}) ▾</button>
                <div x-show="o" x-cloak class="mt-2 space-y-1">
                    @foreach ($bought as $item)
                        <div wire:key="bought-{{ $item->id }}" class="flex items-center justify-between gap-3 text-sm py-1.5 opacity-75">
                            <span class="text-ink dark:text-ink-dark line-through">{{ $item->title }} @if ($item->estimated_price)<span class="text-xs text-ink-soft dark:text-ink-dark-soft">— {{ number_format($item->estimated_price, 2) }}</span>@endif</span>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="undoBuy({{ $item->id }})" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:text-danger">تراجع</button>
                                <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
