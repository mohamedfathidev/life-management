{{-- Motivational quote of the day with refresh button --}}
<div class="relative overflow-hidden rounded-2xl p-6 sm:p-8 text-center shadow-sm bg-gradient-to-br from-primary/10 via-secondary/10 to-transparent dark:from-primary-dark/15 dark:via-secondary-dark/10 dark:to-transparent mb-6">
    {{-- decorative inner frame --}}
    <div class="pointer-events-none absolute inset-2 rounded-xl border border-primary/25 dark:border-primary-dark/25"></div>
    <div class="pointer-events-none absolute inset-3.5 rounded-lg border border-primary/10 dark:border-primary-dark/10"></div>

    <div class="relative">
        <div class="flex items-center justify-center gap-3 mb-4">
            <p class="text-[11px] tracking-widest text-primary dark:text-primary-dark">جملة تحفيزية</p>
        </div>

        @if ($quote)
            <p class="text-xl sm:text-2xl leading-relaxed font-semibold text-ink dark:text-ink-dark px-2" style="font-family: 'Cairo', sans-serif;">
                {{ $quote['text'] }}
            </p>

            @if (isset($quote['author']) && $quote['author'])
                <div class="flex items-center justify-center gap-3 mt-5">
                    <span class="h-px w-8 bg-primary/30 dark:bg-primary-dark/30"></span>
                    <span class="text-sm text-ink-soft dark:text-ink-dark-soft">{{ $quote['author'] }}</span>
                    <span class="h-px w-8 bg-primary/30 dark:bg-primary-dark/30"></span>
                </div>
            @endif

            {{-- Action buttons --}}
            <div class="flex items-center justify-center gap-2 mt-6">
                <button 
                    type="button" 
                    wire:click="refreshQuote" 
                    class="px-4 py-2 rounded-lg bg-primary/10 dark:bg-primary-dark/10 text-primary dark:text-primary-dark text-sm font-medium hover:bg-primary/20 dark:hover:bg-primary-dark/20 transition flex items-center gap-2"
                    title="جملة جديدة"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>جملة جديدة</span>
                </button>
                <button 
                    type="button" 
                    wire:click="openAddMode" 
                    class="px-4 py-2 rounded-lg bg-success/10 dark:bg-success/10 text-success text-sm font-medium hover:bg-success/20 dark:hover:bg-success/20 transition flex items-center gap-2"
                    title="أضف جملة"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>أضف جملة</span>
                </button>
            </div>
        @else
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft">لا توجد جمل متاحة</p>
        @endif
    </div>

    {{-- Add quote modal --}}
    @if ($addMode)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-data
            @keydown.escape.window="$wire.closeAddMode()"
        >
            <div class="absolute inset-0 bg-black/40" wire:click="closeAddMode"></div>
            
            <div class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">إضافة جملة تحفيزية جديدة</h2>
                
                <form wire:submit="addQuote" class="space-y-4">
                    <div>
                        <x-input-label for="new_quote_text" value="الجملة التحفيزية *" />
                        <textarea 
                            id="new_quote_text" 
                            wire:model="newQuoteText" 
                            rows="4" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"
                            placeholder="اكتب جملة تحفيزية ملهمة..."
                            required
                        ></textarea>
                        <x-input-error :messages="$errors->get('newQuoteText')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="new_quote_author" value="المؤلف (اختياري)" />
                        <x-text-input 
                            id="new_quote_author" 
                            wire:model="newQuoteAuthor" 
                            type="text" 
                            class="mt-1 block w-full" 
                            placeholder="اسم المؤلف أو المصدر"
                        />
                        <x-input-error :messages="$errors->get('newQuoteAuthor')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button 
                            type="button" 
                            wire:click="closeAddMode" 
                            class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark"
                        >
                            إلغاء
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition"
                        >
                            حفظ الجملة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
