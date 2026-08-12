<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">الإنجازات</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">شارات بتتفتح مع تقدّمك.</p>
            </div>
            <div class="text-center rounded-xl bg-gradient-to-br from-warning/20 to-warning/5 dark:from-warning/20 dark:to-transparent shadow-sm px-5 py-3">
                <p class="text-2xl font-bold text-warning">{{ $earnedCount }}<span class="text-base text-ink-soft dark:text-ink-dark-soft">/{{ $total }}</span></p>
                <p class="text-[10px] text-ink-soft dark:text-ink-dark-soft">شارة</p>
            </div>
        </div>

        {{-- Newly unlocked banner --}}
        @if (! empty($newlyUnlocked))
            <div class="rounded-xl bg-success/10 border border-success/30 p-4 mb-6">
                <p class="text-sm font-medium text-success mb-1">🎉 مبروك! فتحت شارات جديدة:</p>
                <p class="text-sm text-ink dark:text-ink-dark">
                    @foreach ($newlyUnlocked as $def)
                        <span>{{ $def['emoji'] }} {{ $def['title'] }}</span>@if (! $loop->last) · @endif
                    @endforeach
                </p>
            </div>
        @endif

        @foreach ($grouped as $group => $items)
            <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3 mt-6">{{ $group }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach ($items as $item)
                    @php($def = $item['def'])
                    <div wire:key="ach-{{ $def['key'] }}"
                         @class([
                             'rounded-xl p-4 shadow-sm text-center transition',
                             'bg-surface-light dark:bg-surface-dark' => $item['earned'],
                             'bg-bg-light dark:bg-bg-dark opacity-80' => ! $item['earned'],
                         ])>
                        <div @class(['text-4xl mb-2', 'grayscale opacity-40' => ! $item['earned']])>{{ $def['emoji'] }}</div>
                        <p class="text-sm font-semibold text-ink dark:text-ink-dark">{{ $def['title'] }}</p>
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">{{ $def['desc'] }}</p>

                        @if ($item['earned'])
                            <p class="text-[10px] text-success mt-2">
                                ✓ @if ($item['unlockedAt']) {{ $item['unlockedAt']->translatedFormat('j M Y') }} @else مفتوحة @endif
                            </p>
                        @else
                            <div class="mt-3">
                                <div class="h-1.5 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                                    <div class="h-full rounded-full bg-primary dark:bg-primary-dark" style="width: {{ $item['percent'] }}%"></div>
                                </div>
                                <p class="text-[10px] text-ink-soft dark:text-ink-dark-soft mt-1">{{ $item['current'] }}/{{ $def['target'] }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
