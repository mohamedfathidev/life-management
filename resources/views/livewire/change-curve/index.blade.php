<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">📈 منحنى التغيير</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">مش تقييم مزاجي — إزاي يومك كان فعليًا: تعافي، تاسكات، عادات، وروحانيات.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Period switch --}}
                <div class="inline-flex items-center gap-1 rounded-lg bg-bg-light dark:bg-bg-dark p-1">
                    <button type="button" wire:click="setPeriod('week')" @class(['px-3 py-1.5 text-sm rounded-md', 'bg-primary text-white dark:bg-primary-dark' => $period === 'week', 'text-ink-soft dark:text-ink-dark-soft' => $period !== 'week'])>أسبوعي</button>
                    <button type="button" wire:click="setPeriod('month')" @class(['px-3 py-1.5 text-sm rounded-md', 'bg-primary text-white dark:bg-primary-dark' => $period === 'month', 'text-ink-soft dark:text-ink-dark-soft' => $period !== 'month'])>شهري</button>
                    <button type="button" wire:click="setPeriod('custom')" @class(['px-3 py-1.5 text-sm rounded-md', 'bg-primary text-white dark:bg-primary-dark' => $period === 'custom', 'text-ink-soft dark:text-ink-dark-soft' => $period !== 'custom'])>فترة محددة</button>
                </div>
                @if ($period !== 'custom')
                    <button type="button" wire:click="shift(-1)" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">السابق ›</button>
                    <button type="button" wire:click="goCurrent" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">الحالي</button>
                    <button type="button" wire:click="shift(1)" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">‹ التالي</button>
                @endif
            </div>
        </div>

        @if ($period === 'custom')
            <div class="flex flex-wrap items-end gap-3 bg-surface-light dark:bg-surface-dark rounded-xl p-3 mb-5">
                <div>
                    <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">من</label>
                    <input type="date" wire:model.live="customFrom" class="block rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
                </div>
                <div>
                    <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">إلى</label>
                    <input type="date" wire:model.live="customTo" class="block rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
                </div>
            </div>
        @endif

        <p class="text-center text-sm font-medium text-ink dark:text-ink-dark mb-6">{{ $rangeLabel }}</p>

        {{-- The curve --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mb-6">
            @if (count($points) >= 2)
                <x-line-chart :points="$points" />
            @else
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-10">محتاج يومين على الأقل من البيانات عشان يظهر منحنى.</p>
            @endif
        </div>

        {{-- Best / worst day --}}
        @if ($best && $worst && $best['date']->toDateString() !== $worst['date']->toDateString())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="rounded-2xl bg-success/10 p-5">
                    <p class="text-xs font-semibold text-success">🌟 أحسن يوم</p>
                    <p class="text-sm font-bold text-ink dark:text-ink-dark mt-1">
                        {{ $best['date']->translatedFormat('l، j M') }} — {{ $best['percent'] }}%
                        @if ($best['date']->isToday())
                            <span class="text-[11px] font-normal text-ink-soft dark:text-ink-dark-soft">(لسه متقفلش)</span>
                        @endif
                    </p>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-success/15 text-success">✅ تاسكات {{ $best['breakdown']['tasks'][0] }}/{{ $best['breakdown']['tasks'][1] }}</span>
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-success/15 text-success">🔁 عادات {{ $best['breakdown']['habits'][0] }}/{{ $best['breakdown']['habits'][1] }}</span>
                        @if ($best['breakdown']['lab'])
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-success/15 text-success">🧪 تجربة</span>
                        @endif
                        @if ($best['breakdown']['recovery'][1] > 0)
                            <span class="text-[11px] px-2 py-0.5 rounded-full {{ $best['setback'] ? 'bg-danger/15 text-danger' : 'bg-success/15 text-success' }}">🌱 تعافي {{ $best['setback'] ? 'انتكاسة' : 'نضيف' }}</span>
                        @endif
                    </div>
                </div>
                <div class="rounded-2xl bg-danger/10 p-5">
                    <p class="text-xs font-semibold text-danger">📉 أصعب يوم</p>
                    <p class="text-sm font-bold text-ink dark:text-ink-dark mt-1">
                        {{ $worst['date']->translatedFormat('l، j M') }} — {{ $worst['percent'] }}%
                        @if ($worst['date']->isToday())
                            <span class="text-[11px] font-normal text-ink-soft dark:text-ink-dark-soft">(لسه متقفلش)</span>
                        @endif
                    </p>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-danger/15 text-danger">✅ تاسكات {{ $worst['breakdown']['tasks'][0] }}/{{ $worst['breakdown']['tasks'][1] }}</span>
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-danger/15 text-danger">🔁 عادات {{ $worst['breakdown']['habits'][0] }}/{{ $worst['breakdown']['habits'][1] }}</span>
                        @if ($worst['breakdown']['lab'])
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-danger/15 text-danger">🧪 تجربة</span>
                        @endif
                        @if ($worst['breakdown']['recovery'][1] > 0)
                            <span class="text-[11px] px-2 py-0.5 rounded-full {{ $worst['setback'] ? 'bg-danger/15 text-danger' : 'bg-success/15 text-success' }}">🌱 تعافي {{ $worst['setback'] ? 'انتكاسة' : 'نضيف' }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Notable observations --}}
        <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">🔎 أبرز ملاحظات الفترة</h2>
        <div class="space-y-3 mb-4">
            @forelse ($changes as $change)
                <div wire:key="ch-{{ $change->id }}" class="flex items-start gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 border-s-4 border-success">
                    <span class="text-lg shrink-0">🌱</span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-success">إيه اللي غيّرني</p>
                        <p class="text-sm text-ink dark:text-ink-dark mt-0.5">{{ $change->body }}</p>
                        <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">{{ $change->created_at->translatedFormat('j M Y') }}</p>
                    </div>
                </div>
            @empty
            @endforelse

            @forelse ($setbacks as $setback)
                <div wire:key="sb-{{ $setback->id }}" class="flex items-start gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 border-s-4 border-danger">
                    <span class="text-lg shrink-0">⚠️</span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-danger">إيه اللي وقعني — {{ $setback->recovery?->title }}</p>
                        <p class="text-sm text-ink dark:text-ink-dark mt-0.5">{{ $setback->trigger_note ?: 'انتكاسة من غير سبب مسجّل.' }}</p>
                        <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">{{ $setback->date->translatedFormat('j M Y') }}</p>
                    </div>
                </div>
            @empty
            @endforelse

            @if ($changes->isEmpty() && $setbacks->isEmpty())
                <div class="text-center py-10 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                    <p class="text-ink-soft dark:text-ink-dark-soft text-sm">مفيش حاجة مسجّلة كتغيير أو انتكاسة في الفترة دي.</p>
                </div>
            @endif
        </div>
    </div>
</div>
