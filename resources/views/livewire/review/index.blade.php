<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">المراجعة</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">ملخص تلقائي لإنجازك.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Period switch --}}
                <div class="inline-flex items-center gap-1 rounded-lg bg-bg-light dark:bg-bg-dark p-1">
                    <button type="button" wire:click="setPeriod('week')" @class(['px-3 py-1.5 text-sm rounded-md', 'bg-primary text-white dark:bg-primary-dark' => $period === 'week', 'text-ink-soft dark:text-ink-dark-soft' => $period !== 'week'])>أسبوعي</button>
                    <button type="button" wire:click="setPeriod('month')" @class(['px-3 py-1.5 text-sm rounded-md', 'bg-primary text-white dark:bg-primary-dark' => $period === 'month', 'text-ink-soft dark:text-ink-dark-soft' => $period !== 'month'])>شهري</button>
                </div>
                <button type="button" wire:click="shift(-1)" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">السابق ›</button>
                <button type="button" wire:click="goCurrent" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">الحالي</button>
                <button type="button" wire:click="shift(1)" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">‹ التالي</button>
            </div>
        </div>

        <p class="text-center text-sm font-medium text-ink dark:text-ink-dark mb-6">{{ $rangeLabel }}</p>

        {{-- Headline metrics --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-2xl p-5 text-center shadow-sm bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent">
                <p class="text-3xl font-bold text-success">{{ $summary['prayers']['completion'] }}%</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">التزام الصلاة</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-gradient-to-br from-primary/15 to-primary/5 dark:from-primary-dark/20 dark:to-transparent">
                <p class="text-3xl font-bold text-primary dark:text-primary-dark">{{ $summary['prayers']['onTime'] }}%</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">صلاة في وقتها</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-3xl font-bold text-ink dark:text-ink-dark">{{ $summary['goalsCompleted'] }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">أهداف اكتملت</p>
            </div>
            <div class="rounded-2xl p-5 text-center shadow-sm bg-surface-light dark:bg-surface-dark">
                <p class="text-3xl font-bold text-ink dark:text-ink-dark">{{ number_format($summary['donations']['total'], 0) }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">صدقات ({{ $summary['donations']['count'] }})</p>
            </div>
        </div>

        {{-- Detailed rows --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 divide-y divide-ink-soft/10 dark:divide-ink-dark-soft/10">
            @php($rows = [
                ['🔁 عادات علّمتها', $summary['habits'].' مرة'],
                ['🔥 تحديات (تعليم/نشطة)', $summary['challenges']['checkins'].' / '.$summary['challenges']['active']],
                ['📖 صفحات قرآن', $summary['quranPages']],
                ['📋 أيام مقفولة بالمخطط', $summary['planner']['daysClosed']],
                ['✅ تاسكات مكتملة', $summary['planner']['tasksDone']],
                ['⏱️ ساعات عمل فعلية', intdiv($summary['planner']['workedMinutes'], 60).'س '.($summary['planner']['workedMinutes'] % 60).'د'],
                ['🎯 أهداف نشطة حاليًا', $summary['activeGoals']],
                ['😊 متوسط المزاج', $summary['moodAvg'] !== null ? $summary['moodAvg'].'/10' : '—'],
                ['🌱 انتكاسات التعافي', $summary['recoverySetbacks']],
                ['🚀 تجارب خارج الزون', $summary['comfortDone']],
                ['📝 مذكرات كتبتها', $summary['diaryEntries']],
            ])
            @foreach ($rows as $row)
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-ink dark:text-ink-dark">{{ $row[0] }}</span>
                    <span class="text-sm font-semibold text-ink dark:text-ink-dark">{{ $row[1] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
