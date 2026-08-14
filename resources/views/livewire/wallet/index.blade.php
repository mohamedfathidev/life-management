<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">المحفظة المالية</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">معاك كام، صرفت كام، وفي إيه.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="openCreate('income')" class="px-4 py-2 rounded-lg bg-success text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ دخل</button>
                <button type="button" wire:click="openCreate('expense')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ مصروف</button>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-2xl p-5 text-center shadow-sm bg-gradient-to-br from-primary/15 to-primary/5 dark:from-primary-dark/20 dark:to-transparent">
                <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-ink dark:text-ink-dark' : 'text-danger' }}">{{ number_format($balance, 2) }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">الرصيد الحالي</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-2xl font-bold text-danger">{{ number_format($monthExpense, 2) }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">مصروف الشهر</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-2xl font-bold text-danger">{{ number_format($totalExpense, 2) }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">إجمالي المصروف</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-2xl font-bold text-success">{{ number_format($totalIncome, 2) }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">إجمالي الدخل</p>
            </div>
        </div>

        {{-- Essentials wishlist --}}
        <div class="mt-6">
            <livewire:wallet.wishlist />
        </div>

        {{-- Scope toggle --}}
        <div class="flex items-center gap-1 mt-6 mb-4 rounded-lg bg-bg-light dark:bg-bg-dark p-1 w-fit">
            <button type="button" wire:click="$set('scope', 'month')" @class(['px-4 py-1.5 text-sm rounded-md', 'bg-primary text-white dark:bg-primary-dark' => $scope === 'month', 'text-ink-soft dark:text-ink-dark-soft' => $scope !== 'month'])>هذا الشهر</button>
            <button type="button" wire:click="$set('scope', 'all')" @class(['px-4 py-1.5 text-sm rounded-md', 'bg-primary text-white dark:bg-primary-dark' => $scope === 'all', 'text-ink-soft dark:text-ink-dark-soft' => $scope !== 'all'])>الكل</button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Transactions --}}
            <div class="lg:col-span-2 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">الحركات</h3>
                @forelse ($transactions as $tx)
                    <div wire:key="tx-{{ $tx->id }}" class="flex items-center justify-between gap-3 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                        <div class="min-w-0">
                            <p class="text-sm text-ink dark:text-ink-dark flex items-center gap-2 flex-wrap">
                                <span class="font-semibold {{ $tx->type->value === 'income' ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->type->value === 'income' ? '+' : '−' }}{{ number_format($tx->amount, 2) }}
                                </span>
                                @if ($tx->category)<span class="text-ink-soft dark:text-ink-dark-soft">{{ $tx->category }}</span>@endif
                            </p>
                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $tx->date->translatedFormat('j M Y') }}@if ($tx->note) · {{ $tx->note }}@endif</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="edit({{ $tx->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="delete({{ $tx->id }})" wire:confirm="حذف الحركة؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">مفيش حركات في الفترة دي.</p>
                @endforelse
            </div>

            {{-- Category breakdown --}}
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">صرفت في إيه</h3>
                @php($breakdownTotal = $breakdown->sum())
                @forelse ($breakdown as $cat => $amount)
                    <div wire:key="cat-{{ $loop->index }}" class="mb-3">
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-ink dark:text-ink-dark">{{ $cat }}</span>
                            <span class="text-ink-soft dark:text-ink-dark-soft">{{ number_format($amount, 2) }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                            <div class="h-full rounded-full bg-danger" style="width: {{ $breakdownTotal > 0 ? round($amount / $breakdownTotal * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-6">مفيش مصروفات.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('open') }" x-show="open" x-cloak @keydown.escape.window="open && $wire.close()" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>
        <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->transaction ? 'تعديل الحركة' : ($form->type === 'income' ? 'دخل جديد' : 'مصروف جديد') }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="tx_type" value="النوع" />
                        <select id="tx_type" wire:model.live="form.type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                            <option value="expense">مصروف</option>
                            <option value="income">دخل</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="tx_amount" value="المبلغ" />
                        <x-text-input id="tx_amount" wire:model="form.amount" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('form.amount')" class="mt-1" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="tx_cat" :value="$form->type === 'income' ? 'مصدر الدخل (جاي منين)' : 'البند / في إيه'" />
                        <x-text-input id="tx_cat" wire:model="form.category" type="text" list="tx_cats" class="mt-1 block w-full"
                            placeholder="{{ $form->type === 'income' ? 'راتب، فريلانس، هدية…' : 'أكل، مواصلات، فواتير…' }}" />
                        <datalist id="tx_cats">
                            @if ($form->type === 'income')
                                <option value="راتب"></option>
                                <option value="فريلانس"></option>
                                <option value="هدية"></option>
                                <option value="مكافأة"></option>
                                <option value="بيع"></option>
                                <option value="استرداد"></option>
                            @else
                                <option value="أكل"></option>
                                <option value="مواصلات"></option>
                                <option value="فواتير"></option>
                                <option value="ترفيه"></option>
                                <option value="صحة"></option>
                                <option value="تعليم"></option>
                            @endif
                        </datalist>
                    </div>
                    <div>
                        <x-input-label for="tx_date" value="التاريخ" />
                        <x-text-input id="tx_date" wire:model="form.date" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('form.date')" class="mt-1" />
                    </div>
                </div>
                <div>
                    <x-input-label for="tx_note" value="ملاحظة (اختياري)" />
                    <textarea id="tx_note" wire:model="form.note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
