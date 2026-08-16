<div>
    @if ($open && $task)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="absolute inset-0 bg-black/40" wire:click="close"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
                <div class="text-center mb-4">
                    <div class="text-3xl mb-1">✅</div>
                    <h2 class="text-lg font-semibold text-ink dark:text-ink-dark">خلّصت التاسك؟</h2>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 truncate">{{ $task->title }}</p>
                </div>

                <form wire:submit="complete" class="space-y-5">
                    {{-- Rating --}}
                    <div>
                        <x-input-label value="التقييم (من ١٠)" />
                        <div class="flex items-center gap-1 mt-2 flex-wrap">
                            @for ($i = 1; $i <= 10; $i++)
                                <button type="button" wire:click="$set('rating', {{ $i }})"
                                    class="w-7 h-7 rounded-full text-xs font-medium transition {{ $rating !== null && $i <= $rating ? 'bg-warning text-white' : 'bg-ink-soft/15 text-ink-soft dark:text-ink-dark-soft hover:bg-ink-soft/25' }}">{{ $i }}</button>
                            @endfor
                            @if ($rating !== null)
                                <button type="button" wire:click="$set('rating', null)" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:text-danger ms-1">مسح</button>
                            @endif
                        </div>
                    </div>

                    {{-- Expected time --}}
                    <div>
                        <x-input-label for="cm_est" value="الوقت المتوقّع (دقيقة)" />
                        <x-text-input id="cm_est" wire:model="estimatedMinutes" type="number" min="0" class="mt-1 block w-full" placeholder="مثال: 45" />
                        <x-input-error :messages="$errors->get('estimatedMinutes')" class="mt-1" />
                    </div>

                    {{-- Actual time --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label value="الوقت الفعلي (دقيقة)" />
                            <label class="flex items-center gap-2 text-xs text-ink-soft dark:text-ink-dark-soft cursor-pointer">
                                <input type="checkbox" wire:model.live="actualIsAuto" class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" />
                                تلقائيًا من التركيز
                            </label>
                        </div>
                        @if ($actualIsAuto)
                            <div class="mt-1 rounded-md border border-dashed border-ink-soft/30 bg-bg-light dark:bg-bg-dark px-3 py-2 text-sm text-ink dark:text-ink-dark">
                                {{ $focusMinutes }} دقيقة <span class="text-xs text-ink-soft dark:text-ink-dark-soft">(من جلسات التركيز)</span>
                            </div>
                        @else
                            <x-text-input wire:model="actualMinutes" type="number" min="0" class="mt-1 block w-full" placeholder="اكتب الوقت الفعلي" />
                            <x-input-error :messages="$errors->get('actualMinutes')" class="mt-1" />
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-1">
                        <button type="button" wire:click="quickDone" class="text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">خلاص من غير تفاصيل</button>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-success text-white text-sm font-medium hover:opacity-90 transition">تم ✓</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
