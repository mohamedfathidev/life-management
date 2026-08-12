<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('religion') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الدين</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">القرآن</h1>
            </div>
            <button type="button" wire:click="openCreate" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ ورد</button>
        </div>

        {{-- Verse of the day --}}
        <x-quran-quote class="mb-6" />

        {{-- Today's wird — explicit "done reading" mark (separate from the page log) --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 mb-6 flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-ink dark:text-ink-dark">ورد النهاردة</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">
                    @if ($wirdReadToday) قرأت وردك النهاردة — بارك الله فيك 🤍 @else علّم إنك قرأت وردك بعد ما تخلّص فعلاً. @endif
                </p>
            </div>
            <button type="button" wire:click="toggleWirdRead"
                class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition
                    {{ $wirdReadToday
                        ? 'bg-success/15 text-success hover:bg-success/25'
                        : 'bg-primary dark:bg-primary-dark text-white hover:opacity-90' }}">
                @if ($wirdReadToday)
                    <span>✓ تم القراءة</span>
                @else
                    <span>تم القراءة</span>
                @endif
            </button>
        </div>

        {{-- Khatmah progress --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-ink dark:text-ink-dark">تقدّم الختمة</h3>
                <span class="text-sm text-ink-soft dark:text-ink-dark-soft">{{ $currentPages }} / {{ $mushafPages }} صفحة</span>
            </div>
            <div class="h-3 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                <div class="h-full rounded-full bg-primary dark:bg-primary-dark transition-all" style="width: {{ $khatmahPercent }}%"></div>
            </div>
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2">
                ختمات مكتملة: <span class="font-semibold text-success">{{ $khatmahs }}</span> · إجمالي الصفحات المقروءة: {{ $totalPages }}
            </p>
        </div>

        {{-- Reading log --}}
        <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">سجل الورد</h3>
            @forelse ($logs as $log)
                <div wire:key="qlog-{{ $log->id }}" class="flex items-start justify-between gap-3 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                    <div>
                        <p class="text-sm text-ink dark:text-ink-dark">{{ $log->date->translatedFormat('l، j M Y') }}</p>
                        <div class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">
                            @if ($log->from_surah)<span>من {{ $log->from_surah }}@if ($log->from_ayah) : {{ $log->from_ayah }}@endif</span>@endif
                            @if ($log->to_surah)<span> إلى {{ $log->to_surah }}@if ($log->to_ayah) : {{ $log->to_ayah }}@endif</span>@endif
                            @if ($log->pages)<span> · {{ $log->pages }} صفحة</span>@endif
                        </div>
                        @if ($log->note)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $log->note }}</p>@endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="editLog({{ $log->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="deleteLog({{ $log->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">مفيش ورد مسجّل لسه.</p>
            @endforelse
        </div>
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('open') }" x-show="open" x-cloak @keydown.escape.window="open && $wire.close()" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>
        <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->log ? 'تعديل الورد' : 'ورد جديد' }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="q_date" value="التاريخ" />
                        <x-text-input id="q_date" wire:model="form.date" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('form.date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="q_pages" value="عدد الصفحات" />
                        <x-text-input id="q_pages" wire:model="form.pages" type="number" min="0" max="604" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('form.pages')" class="mt-1" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="q_fs" value="من سورة" />
                        <x-text-input id="q_fs" wire:model="form.from_surah" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="q_fa" value="من آية" />
                        <x-text-input id="q_fa" wire:model="form.from_ayah" type="number" min="1" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="q_ts" value="إلى سورة" />
                        <x-text-input id="q_ts" wire:model="form.to_surah" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="q_ta" value="إلى آية" />
                        <x-text-input id="q_ta" wire:model="form.to_ayah" type="number" min="1" class="mt-1 block w-full" />
                    </div>
                </div>
                <div>
                    <x-input-label for="q_note" value="ملاحظة (اختياري)" />
                    <textarea id="q_note" wire:model="form.note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
