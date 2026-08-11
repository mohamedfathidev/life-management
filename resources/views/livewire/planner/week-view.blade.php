<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header + week navigation --}}
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">المخطط الأسبوعي</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">
                    {{ $start->translatedFormat('j M') }} — {{ $end->translatedFormat('j M Y') }}
                    @if ($isCurrentWeek)<span class="text-primary dark:text-primary-dark">• الأسبوع الحالي</span>@endif
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('planner.pool') }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">المؤجّلات</a>
                <a href="{{ route('planner.week', $prevWeek) }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">الأسبوع السابق ›</a>
                <a href="{{ route('planner') }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">اليوم</a>
                <a href="{{ route('planner.week', $nextWeek) }}" wire:navigate class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">‹ الأسبوع التالي</a>
            </div>
        </div>

        {{-- 7-day strip --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($strip as $entry)
                @php($date = $entry['date'])
                @php($day = $entry['day'])
                @php($isToday = $date->isToday())
                @php($completion = $day ? (int) round($day->tasks->avg('progress') ?? 0) : 0)

                <a href="{{ route('planner.day', $date->toDateString()) }}" wire:navigate
                   wire:key="day-{{ $date->toDateString() }}"
                   @class([
                       'block rounded-xl p-4 shadow-sm transition hover:shadow-md',
                       'bg-surface-light dark:bg-surface-dark',
                       'ring-2 ring-primary dark:ring-primary-dark' => $isToday,
                   ])>
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-ink dark:text-ink-dark">{{ $date->translatedFormat('l') }}</span>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">{{ $date->translatedFormat('j M') }}</span>
                    </div>

                    <div class="mt-3 flex items-center gap-2 text-xs">
                        @if ($day && $day->isClosed())
                            <span class="px-2 py-0.5 rounded-full bg-success/15 text-success">مُقفل</span>
                        @elseif ($day && $day->isStarted())
                            <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">جارٍ</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-ink-soft/10 text-ink-soft dark:text-ink-dark-soft">لم يبدأ</span>
                        @endif
                        <span class="text-ink-soft dark:text-ink-dark-soft">{{ $day ? $day->tasks_count : 0 }} تاسك</span>
                    </div>

                    {{-- Completion bar --}}
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-ink-soft dark:text-ink-dark-soft mb-1">
                            <span>الإنجاز</span>
                            <span>{{ $completion }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                            <div class="h-full rounded-full bg-success" style="width: {{ $completion }}%"></div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
