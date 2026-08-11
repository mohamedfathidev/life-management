<div class="py-8" x-data="{ quickOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">لوحة التحكم</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">
                {{ now()->translatedFormat('l، j F Y') }}
            </p>
        </div>

        {{-- Stat cards with soft gradients (dashboard-only style) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Active goals --}}
            <div class="rounded-2xl p-5 shadow-sm bg-gradient-to-br from-primary/15 to-primary/5 dark:from-primary-dark/20 dark:to-transparent backdrop-blur">
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft">أهداف نشطة</p>
                <p class="text-3xl font-bold text-ink dark:text-ink-dark mt-2">{{ $activeGoals }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">من {{ $totalGoals }} هدف</p>
            </div>

            {{-- Logs today --}}
            <div class="rounded-2xl p-5 shadow-sm bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent backdrop-blur">
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft">سجلات اليوم</p>
                <p class="text-3xl font-bold text-ink dark:text-ink-dark mt-2">{{ $logsToday }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">تم تسجيلها اليوم</p>
            </div>

            {{-- Upcoming deadlines --}}
            <div class="rounded-2xl p-5 shadow-sm bg-gradient-to-br from-warning/15 to-warning/5 dark:from-warning/20 dark:to-transparent backdrop-blur">
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft">مواعيد قادمة</p>
                <p class="text-3xl font-bold text-ink dark:text-ink-dark mt-2">{{ $deadlines->count() }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">أهداف لها تاريخ</p>
            </div>

            {{-- Mood 7-day sparkline --}}
            <div class="rounded-2xl p-5 shadow-sm bg-gradient-to-br from-secondary/25 to-secondary/5 dark:from-secondary-dark/25 dark:to-transparent backdrop-blur">
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mb-2">المزاج (٧ أيام)</p>
                <div class="flex items-end gap-1 h-12" dir="ltr">
                    @foreach ($moodTrend as $point)
                        <div class="flex-1 rounded-t bg-primary/70 dark:bg-primary-dark/70"
                             style="height: {{ $point['mood'] ? ($point['mood'] * 10) : 4 }}%"
                             title="{{ $point['date'] }}: {{ $point['mood'] ?? '—' }}"></div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Two columns: today's logs + upcoming deadlines --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-6">
            {{-- Today's logs --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">سجلات اليوم</h3>
                @forelse ($todaysLogs as $log)
                    <div wire:key="today-log-{{ $log->id }}" class="flex items-center justify-between py-2 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                        <div>
                            <p class="text-sm text-ink dark:text-ink-dark">{{ $log->module_type->label() }}</p>
                            @if ($log->goal)<p class="text-xs text-ink-soft dark:text-ink-dark-soft">{{ $log->goal->title }}</p>@endif
                        </div>
                        <div class="text-xs text-ink-soft dark:text-ink-dark-soft">
                            @if ($log->mood)<span>😊 {{ $log->mood }}/10</span>@endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">لم تسجّل شيئًا اليوم بعد.</p>
                @endforelse
            </div>

            {{-- Upcoming deadlines --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">مواعيد قادمة</h3>
                @forelse ($deadlines as $goal)
                    <a href="{{ route('goals.show', $goal) }}" wire:navigate wire:key="deadline-{{ $goal->id }}"
                       class="group flex items-center justify-between gap-2 -mx-2 px-2 py-2.5 rounded-lg cursor-pointer transition hover:bg-primary/5 dark:hover:bg-primary-dark/10">
                        <span class="flex items-center gap-2 min-w-0">
                            <svg class="w-4 h-4 text-primary dark:text-primary-dark shrink-0 rotate-180 opacity-60 group-hover:opacity-100 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            <span class="text-sm text-ink dark:text-ink-dark truncate group-hover:text-primary dark:group-hover:text-primary-dark transition">{{ $goal->title }}</span>
                        </span>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft shrink-0">{{ $goal->target_date->translatedFormat('j M') }}</span>
                    </a>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">لا توجد مواعيد قادمة.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Floating quick-add --}}
    <div class="fixed bottom-6 left-6 z-40" x-data="{ menu: false }" @click.outside="menu = false">
        <div x-show="menu" x-transition x-cloak class="mb-3 flex flex-col gap-2">
            <button type="button" @click="menu = false" wire:click="$dispatch('create-log')" class="px-4 py-2 rounded-full bg-surface-light dark:bg-surface-dark shadow-md text-sm text-ink dark:text-ink-dark hover:opacity-90">+ سجل يومي</button>
            <button type="button" @click="menu = false" wire:click="$dispatch('create-goal')" class="px-4 py-2 rounded-full bg-surface-light dark:bg-surface-dark shadow-md text-sm text-ink dark:text-ink-dark hover:opacity-90">+ هدف جديد</button>
        </div>
        <button type="button" @click="menu = !menu" class="w-14 h-14 rounded-full bg-primary dark:bg-primary-dark text-white shadow-lg flex items-center justify-center hover:opacity-90 transition" aria-label="إضافة سريعة">
            <svg class="w-6 h-6 transition-transform" :class="menu && 'rotate-45'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </button>
    </div>

    {{-- Quick-add modals --}}
    <livewire:goals.manage-goal />
    <livewire:daily-logs.manage-log />
</div>
