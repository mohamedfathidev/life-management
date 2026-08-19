<div class="py-8" x-data>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">لوحة التحكم</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ now()->translatedFormat('l، j F Y') }}</p>
        </div>

        {{-- Verse of the day --}}
        <x-quran-quote class="mb-6" />

        {{-- Today at a glance --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Plan --}}
            <a href="{{ route('planner.day', now()->toDateString()) }}" wire:navigate class="rounded-2xl p-5 shadow-sm bg-gradient-to-br from-primary/15 to-primary/5 dark:from-primary-dark/20 dark:to-transparent hover:shadow-md transition">
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft">مخطط اليوم</p>
                <p class="text-3xl font-bold text-ink dark:text-ink-dark mt-2">{{ $todayPlan['percent'] }}%</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">
                    {{ $todayPlan['done'] }}/{{ $todayPlan['total'] }} تاسك ·
                    {{ $todayPlan['closed'] ? 'مقفل' : ($todayPlan['started'] ? 'جارٍ' : 'لم يبدأ') }}
                </p>
            </a>

            {{-- Prayers --}}
            <a href="{{ route('religion.prayers') }}" wire:navigate class="rounded-2xl p-5 shadow-sm bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent hover:shadow-md transition">
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft">🕌 الصلوات</p>
                <p class="text-3xl font-bold text-ink dark:text-ink-dark mt-2">{{ $prayers['done'] }}<span class="text-lg text-ink-soft dark:text-ink-dark-soft">/5</span></p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">{{ $prayers['onTime'] }} في وقتها</p>
            </a>

            {{-- Habits --}}
            <a href="{{ route('habits.index') }}" wire:navigate class="rounded-2xl p-5 shadow-sm bg-gradient-to-br from-secondary/25 to-secondary/5 dark:from-secondary-dark/25 dark:to-transparent hover:shadow-md transition">
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft">🔁 العادات</p>
                <p class="text-3xl font-bold text-ink dark:text-ink-dark mt-2">{{ $habits['done'] }}<span class="text-lg text-ink-soft dark:text-ink-dark-soft">/{{ $habits['total'] }}</span></p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">اتعملت النهاردة</p>
            </a>

            {{-- Challenges --}}
            <a href="{{ route('challenges.index') }}" wire:navigate class="rounded-2xl p-5 shadow-sm bg-gradient-to-br from-warning/20 to-warning/5 dark:from-warning/20 dark:to-transparent hover:shadow-md transition">
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft">🔥 التحديات</p>
                <p class="text-3xl font-bold text-ink dark:text-ink-dark mt-2">{{ $challenges['done'] }}<span class="text-lg text-ink-soft dark:text-ink-dark-soft">/{{ $challenges['total'] }}</span></p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">علّمتها النهاردة</p>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-6">
            {{-- Upcoming appointments --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-ink dark:text-ink-dark">📅 مواعيد قادمة</h3>
                    <a href="{{ route('appointments') }}" wire:navigate class="text-xs text-primary dark:text-primary-dark hover:underline">الكل</a>
                </div>
                @forelse ($appointments as $ap)
                    <a href="{{ route('appointments') }}" wire:navigate wire:key="dash-ap-{{ $ap->id }}" class="flex items-center justify-between gap-2 py-2 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0 hover:opacity-80">
                        <span class="flex items-center gap-2 min-w-0">
                            <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $ap->type->hex() }}"></span>
                            <span class="text-sm text-ink dark:text-ink-dark truncate">{{ $ap->title }}</span>
                        </span>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft shrink-0">{{ $ap->date->translatedFormat('j M') }}@if ($ap->timeLabel()) · <span dir="ltr">{{ $ap->timeLabel() }}</span>@endif</span>
                    </a>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-6">مفيش مواعيد قادمة.</p>
                @endforelse
            </div>

            {{-- Recovery streaks --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-ink dark:text-ink-dark">🌱 التعافي</h3>
                    <a href="{{ route('recovery.index') }}" wire:navigate class="text-xs text-primary dark:text-primary-dark hover:underline">الكل</a>
                </div>
                @forelse ($recoveries as $rec)
                    <a href="{{ route('recovery.show', $rec) }}" wire:navigate wire:key="dash-rec-{{ $rec->id }}" class="flex items-center justify-between gap-2 py-2 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0 hover:opacity-80">
                        <span class="text-sm text-ink dark:text-ink-dark truncate">{{ $rec->title }}</span>
                        <span class="text-sm font-bold text-success shrink-0">{{ $rec->currentStreakDays() }} يوم</span>
                    </a>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-6">مفيش حالات تعافٍ نشطة.</p>
                @endforelse
            </div>

            {{-- Goal deadlines --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-ink dark:text-ink-dark">🎯 مواعيد الأهداف</h3>
                    <a href="{{ route('goals.index') }}" wire:navigate class="text-xs text-primary dark:text-primary-dark hover:underline">الكل ({{ $activeGoals }})</a>
                </div>
                @forelse ($deadlines as $goal)
                    <a href="{{ route('goals.show', $goal) }}" wire:navigate wire:key="dash-goal-{{ $goal->id }}" class="flex items-center justify-between gap-2 py-2 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0 hover:opacity-80">
                        <span class="text-sm text-ink dark:text-ink-dark truncate">{{ $goal->title }}</span>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft shrink-0">{{ $goal->target_date->translatedFormat('j M') }}</span>
                    </a>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-6">مفيش مواعيد أهداف.</p>
                @endforelse
            </div>
        </div>

        {{-- Mood trend (Saturday to Friday week) --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mt-6">
            <div class="flex items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="font-bold text-ink dark:text-ink-dark">المزاج (أسبوع من السبت إلى الجمعة)</h3>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">
                        {{ $moodTrend['startDate']->translatedFormat('j M') }} – {{ $moodTrend['endDate']->translatedFormat('j M Y') }} · يربط مزاج تقفيل اليوم والتعافي والسجل
                    </p>
                </div>
            </div>

            <div class="flex items-end gap-2 sm:gap-4 h-32 pt-6 pb-2" dir="rtl">
                @foreach ($moodTrend['points'] as $point)
                    <div class="flex-1 flex flex-col items-center justify-end h-full">
                        {{-- Score badge at top with Extra Large Emoji --}}
                        @if ($point['mood'] !== null)
                            <span class="mb-1 flex flex-col sm:flex-row items-center justify-center gap-1 leading-none">
                                <span class="text-2xl sm:text-3xl drop-shadow-sm">{{ $point['emoji'] }}</span>
                                <span class="text-xs font-extrabold text-primary dark:text-primary-dark">{{ $point['mood'] }}</span>
                            </span>
                        @else
                            <span class="text-xs text-gray-300 dark:text-gray-600 mb-1">—</span>
                        @endif

                        {{-- Bar --}}
                        <div class="w-full max-w-[40px] rounded-t-lg transition-all duration-500 {{ $point['isToday'] ? 'bg-primary dark:bg-primary-dark ring-2 ring-primary/40' : ($point['mood'] ? 'bg-primary/70 dark:bg-primary-dark/70 hover:bg-primary' : 'bg-gray-100 dark:bg-gray-800') }}"
                             style="height: {{ $point['mood'] ? max(15, $point['mood'] * 10) : 8 }}%"
                             title="{{ $point['dayName'] }} {{ $point['dayNumber'] }}: {{ $point['mood'] ? $point['mood'] . '/10' : 'غير مسجّل' }}"></div>

                        {{-- Day name & number --}}
                        <span class="text-xs font-bold text-ink dark:text-ink-dark mt-2 {{ $point['isToday'] ? 'text-primary dark:text-primary-dark' : '' }}">
                            {{ $point['dayName'] }}
                        </span>
                        <span class="text-[10px] text-ink-soft dark:text-ink-dark-soft">
                            {{ $point['dayNumber'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Motivational quote at bottom --}}
        <div class="mt-6">
            <livewire:dashboard.motivational-quote />
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

    <livewire:goals.manage-goal />
    <livewire:daily-logs.manage-log />
</div>
