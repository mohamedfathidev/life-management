<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-5">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">🧠 تنضيف العقل</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">طلّع عقلك من الكسل الدماغي بتاع عصر الـ AI — ارجع <span class="text-primary dark:text-primary-dark font-medium">تفكّر مش تحفظ</span>. مرّن عقلك كل يوم بلعبة منطقية.</p>
        </div>

        {{-- Weekly stats --}}
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-primary dark:text-primary-dark">{{ $weekCount }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">جلسات الأسبوع</p>
            </div>
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $weekMinutes }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">دقيقة الأسبوع</p>
            </div>
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 text-center">
                <p class="text-2xl font-bold text-success">🔥 {{ $streak }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">يوم متتالي</p>
            </div>
        </div>

        {{-- Log form --}}
        <form wire:submit="logSession" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mb-6">
            <h2 class="font-semibold text-ink dark:text-ink-dark mb-3">سجّل جلسة</h2>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <x-input-label for="mg_game" value="اللعبة" />
                    <x-text-input id="mg_game" wire:model="game" type="text" class="mt-1 block w-full" placeholder="اختر من تحت أو اكتب…" />
                    <x-input-error :messages="$errors->get('game')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="mg_min" value="الدقايق" />
                    <x-text-input id="mg_min" wire:model="minutes" type="number" min="1" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('minutes')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="mg_date" value="التاريخ" />
                    <x-text-input id="mg_date" wire:model="date" type="date" class="mt-1 block w-full" />
                </div>
            </div>
            <div class="mt-3">
                <x-text-input wire:model="note" type="text" class="block w-full" placeholder="ملاحظة (اختياري): نتيجة، مستوى، إحساسك…" />
            </div>
            <div class="flex justify-end mt-3">
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">سجّل</button>
            </div>
        </form>

        {{-- Games list --}}
        <h2 class="font-semibold text-ink dark:text-ink-dark mb-3">ألعاب وتمارين تنمّي التفكير</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8">
            @foreach ($games as $g)
                <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-semibold text-ink dark:text-ink-dark flex items-center gap-2"><span class="text-xl">{{ $g['emoji'] }}</span> {{ $g['name'] }}</h3>
                        <button type="button" wire:click="pickGame(@js($g['name']))" class="shrink-0 text-xs px-3 py-1 rounded-full bg-primary/10 text-primary dark:text-primary-dark hover:bg-primary/20 transition">سجّل دي</button>
                    </div>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2"><span class="text-success font-medium">بتنمّي:</span> {{ $g['trains'] }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">💡 {{ $g['tip'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Sessions log --}}
        <h2 class="font-semibold text-ink dark:text-ink-dark mb-3">سجل الجلسات</h2>
        @if ($sessions->isEmpty())
            <div class="text-center py-10 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">ابدأ أول جلسة تمرين لعقلك النهاردة 🧠</p>
            </div>
        @else
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm divide-y divide-ink-soft/10 dark:divide-ink-dark-soft/10">
                @foreach ($sessions as $s)
                    <div wire:key="ms-{{ $s->id }}" class="flex items-center justify-between gap-3 p-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-ink dark:text-ink-dark">{{ $s->game }} <span class="text-xs text-ink-soft dark:text-ink-dark-soft">· {{ $s->minutes }} د</span></p>
                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $s->date->translatedFormat('l، j M') }}@if ($s->note) — {{ $s->note }}@endif</p>
                        </div>
                        <button type="button" wire:click="delete({{ $s->id }})" wire:confirm="حذف الجلسة؟" class="text-xs text-danger hover:underline shrink-0">حذف</button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
