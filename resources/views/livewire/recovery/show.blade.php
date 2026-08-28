<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل حالات التعافي</a>

        {{-- Header --}}
        <div class="mt-3 flex items-start justify-between gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $recovery->title }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $recovery->status->color() }}/15 text-{{ $recovery->status->color() }}">{{ $recovery->status->label() }}</span>
                </div>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">
                    🗓️ الفترة: من {{ $recovery->start_date->translatedFormat('j M Y') }}
                    @if ($recovery->end_date) إلى {{ $recovery->end_date->translatedFormat('j M Y') }} @else (مفتوحة) @endif
                </p>
                @if ($recovery->description)
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 whitespace-pre-line">{{ $recovery->description }}</p>
                @endif
            </div>
            <button type="button" wire:click="editRecovery" class="shrink-0 px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
                <button type="button" wire:click="$dispatch('create-story', { recoveryId: {{ $recovery->id }} })" class="shrink-0 px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">📖 اكتب حكاية</button>
        </div>

        {{-- Streak counter --}}
        <div class="mt-6 rounded-2xl bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent shadow-sm p-8 text-center">
            <p class="text-6xl font-bold text-success">{{ $streakDays }}</p>
            <p class="text-ink-soft dark:text-ink-dark-soft mt-2">يوم نظيف متواصل — منذ {{ $streakSince->translatedFormat('j M Y') }}</p>

            <div class="flex items-center justify-center gap-8 mt-6 text-sm">
                <div>
                    <p class="text-xl font-bold text-success">{{ $cleanDays }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">عدد الأيام النضيفة</p>
                </div>
                <div>
                    <p class="text-xl font-bold text-ink dark:text-ink-dark">{{ $remainingDaysLabel }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">الأيام المتبقية في الفترة</p>
                </div>
                <div>
                    <p class="text-xl font-bold text-ink dark:text-ink-dark">{{ $setbackCount }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">عدد الانتكاسات</p>
                </div>
            </div>

            <div class="flex items-center justify-center gap-3 mt-6">
                <button type="button" wire:click="addLog(false)" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">سجّل يوم نضيف</button>
                <button type="button" wire:click="addLog(true)" class="px-4 py-2 rounded-lg bg-danger/15 text-danger text-sm font-medium hover:bg-danger/25 transition">سجّل انتكاسة</button>
                <a href="{{ route('recovery.setbacks', ['recoveryId' => $recovery->id]) }}" wire:navigate class="px-4 py-2 rounded-lg bg-bg-light dark:bg-bg-dark text-ink dark:text-ink-dark text-sm font-medium hover:bg-ink-soft/10 transition border border-ink-soft/20">💔 عرض الانتكاسات</a>
            </div>
        </div>

        {{-- Week Filter --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-primary dark:text-primary-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-sm font-semibold text-ink dark:text-ink-dark">فلترة حسب الأسبوع</h3>
            </div>
            
            <div class="flex flex-wrap gap-2 items-center">
                {{-- Current Week --}}
                <button 
                    type="button" 
                    wire:click="setWeek('current')"
                    @class([
                        'px-4 py-2 rounded-lg text-sm font-medium transition',
                        'bg-primary text-white dark:bg-primary-dark' => $selectedWeek === 'current',
                        'bg-bg-light dark:bg-bg-dark text-ink dark:text-ink-dark hover:bg-primary/10 dark:hover:bg-primary-dark/10' => $selectedWeek !== 'current',
                    ])
                >
                    الأسبوع الحالي
                </button>

                {{-- All Weeks --}}
                <button 
                    type="button" 
                    wire:click="setWeek('all')"
                    @class([
                        'px-4 py-2 rounded-lg text-sm font-medium transition',
                        'bg-primary text-white dark:bg-primary-dark' => $selectedWeek === 'all',
                        'bg-bg-light dark:bg-bg-dark text-ink dark:text-ink-dark hover:bg-primary/10 dark:hover:bg-primary-dark/10' => $selectedWeek !== 'all',
                    ])
                >
                    الكل
                </button>

                {{-- Dropdown for weeks if more than 5 --}}
                @if ($availableWeeks->count() > 5)
                    <div x-data="{ open: false }" class="relative">
                        <button 
                            type="button" 
                            @click="open = !open"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition bg-bg-light dark:bg-bg-dark text-ink dark:text-ink-dark hover:bg-primary/10 dark:hover:bg-primary-dark/10 flex items-center gap-2"
                        >
                            <span>اختر أسبوع</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            x-transition
                            class="absolute z-10 mt-2 w-64 rounded-lg bg-surface-light dark:bg-surface-dark shadow-lg border border-ink-soft/20 dark:border-ink-dark-soft/20 max-h-64 overflow-y-auto"
                        >
                            @foreach ($availableWeeks as $week)
                                @php
                                    $weekKey = 'week-' . $week->number;
                                @endphp
                                <button 
                                    type="button" 
                                    wire:click="setWeek('{{ $weekKey }}')"
                                    @click="open = false"
                                    @class([
                                        'w-full text-right px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-dark/10 flex items-center justify-between gap-2',
                                        'bg-primary/20 dark:bg-primary-dark/20' => $selectedWeek === $weekKey,
                                    ])
                                >
                                    <span class="font-medium">الأسبوع {{ $week->number }}</span>
                                    <span class="text-xs text-ink-soft dark:text-ink-dark-soft">
                                        {{ $week->start_date->translatedFormat('j M') }} - {{ $week->end_date->translatedFormat('j M') }}
                                        @if ($week->setback_count > 0)
                                            <span class="inline-block mr-1 px-1.5 py-0.5 rounded-full bg-danger text-white text-xs">{{ $week->setback_count }}</span>
                                        @elseif ($week->count > 0)
                                            <span class="inline-block mr-1 px-1.5 py-0.5 rounded-full bg-success text-white text-xs">{{ $week->count }}</span>
                                        @endif
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Show buttons for first 5 weeks --}}
                    @foreach ($availableWeeks as $week)
                        @php
                            $weekKey = 'week-' . $week->number;
                        @endphp
                        <button 
                            type="button" 
                            wire:click="setWeek('{{ $weekKey }}')"
                            @class([
                                'px-4 py-2 rounded-lg text-sm font-medium transition relative',
                                'bg-primary text-white dark:bg-primary-dark' => $selectedWeek === $weekKey,
                                'bg-bg-light dark:bg-bg-dark text-ink dark:text-ink-dark hover:bg-primary/10 dark:hover:bg-primary-dark/10' => $selectedWeek !== $weekKey,
                            ])
                            title="{{ $week->start_date->translatedFormat('j M') }} - {{ $week->end_date->translatedFormat('j M Y') }}"
                        >
                            <span>أسبوع {{ $week->number }}</span>
                            @if ($week->setback_count > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-danger text-white text-xs flex items-center justify-center">{{ $week->setback_count }}</span>
                            @elseif ($week->count > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-success text-white text-xs flex items-center justify-center">{{ $week->count }}</span>
                            @endif
                        </button>
                    @endforeach
                @endif
            </div>

            {{-- Selected Week Info --}}
            @if ($selectedWeek && $selectedWeek !== 'all' && $selectedWeek !== 'current')
                @php
                    $weekNum = (int) str_replace('week-', '', $selectedWeek);
                    $selectedWeekData = $availableWeeks->firstWhere('number', $weekNum);
                @endphp
                @if ($selectedWeekData)
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-3">
                        📅 {{ $selectedWeekData->start_date->translatedFormat('j F') }} - {{ $selectedWeekData->end_date->translatedFormat('j F Y') }}
                    </p>
                @endif
            @elseif ($selectedWeek === 'current')
                @php
                    $latestWeek = $availableWeeks->last();
                @endphp
                @if ($latestWeek)
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-3">
                        📅 {{ $latestWeek->start_date->translatedFormat('j F') }} - {{ $latestWeek->end_date->translatedFormat('j F Y') }}
                        <span class="text-primary dark:text-primary-dark font-medium">(الأسبوع {{ $latestWeek->number }})</span>
                    </p>
                @endif
            @endif
        </div>

        {{-- Logs --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">
                السجل
                @if ($selectedWeek && $selectedWeek !== 'all')
                    <span class="text-xs font-normal text-ink-soft dark:text-ink-dark-soft">({{ $logs->count() }} تسجيل)</span>
                @endif
            </h3>
            @forelse ($logs as $log)
                <div wire:key="reclog-{{ $log->id }}" class="flex items-start justify-between gap-4 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                    <div class="min-w-0">
                        <p class="text-sm text-ink dark:text-ink-dark flex items-center gap-2">
                            {{ $log->date->translatedFormat('l، j M Y') }}
                            @if ($log->is_setback)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-danger/15 text-danger">انتكاسة</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-success/15 text-success">يوم نضيف ✓</span>
                            @endif
                        </p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                            @if ($log->urge_level)<span>🌊 الرغبة: {{ $log->urge_level }}/10</span>@endif
                            @if ($log->mood)<span>😊 المزاج: {{ $log->mood }}/10</span>@endif
                            @if ($log->hardest_from && $log->hardest_to)<span>⏰ أصعب فترة: {{ \Illuminate\Support\Carbon::parse($log->hardest_from)->translatedFormat('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($log->hardest_to)->translatedFormat('g:i A') }}</span>@endif
                        </div>
                        @if ($log->stayed_up_late !== null || $log->had_dinner !== null || $log->prepared_for_sleep !== null || $log->sleep_location || $log->sleep_spot)
                            <div class="flex items-center gap-3 mt-1 text-xs flex-wrap">
                                <span class="text-ink-soft dark:text-ink-dark-soft">ليلة اليوم:</span>
                                @if ($log->stayed_up_late !== null)
                                    <span class="{{ $log->stayed_up_late ? 'text-warning' : 'text-success' }}">
                                        {{ $log->stayed_up_late ? '🌙 سهرت' : '🛌 نمت مبكّر' }}
                                    </span>
                                @endif
                                @if ($log->had_dinner !== null)
                                    <span class="{{ $log->had_dinner ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">
                                        {{ $log->had_dinner ? '🍽️ اتغذّى' : '🚫 مااتغذّاش' }}
                                    </span>
                                @endif
                                @if ($log->prepared_for_sleep !== null)
                                    <span class="{{ $log->prepared_for_sleep ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">
                                        {{ $log->prepared_for_sleep ? '🛏️ استعدّ للنوم' : '⚠️ مااستعدّش' }}
                                    </span>
                                @endif
                                @if ($log->sleep_location)
                                    <span class="text-ink-soft dark:text-ink-dark-soft">{{ $log->sleep_location === 'home' ? '🏠 نام في البيت' : '🚶 نام برا' }}</span>
                                @endif
                                @if ($log->sleep_spot)
                                    <span class="text-ink-soft dark:text-ink-dark-soft">{{ $log->sleep_spot === 'bed' ? '🛏️ على السرير' : '↔️ نومة تانية' }}</span>
                                @endif
                            </div>
                        @endif
                        @if ($log->trigger_note)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1"><span class="text-danger font-medium">المُحفّز:</span> {{ $log->trigger_note }}</p>@endif
                        @if ($log->avoidance_reasons)
                            <div class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">
                                <span class="text-danger font-medium">ازاي كنت اقدر اتجنب السقوط:</span>
                                <ul class="list-disc pr-5 mt-0.5">
                                    @foreach ($log->avoidance_reasons as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($log->protection_actions)
                            <div class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">
                                <span class="text-success font-medium">عملت ايه عشان محصلش سقوط:</span>
                                <ul class="list-disc pr-5 mt-0.5">
                                    @foreach ($log->protection_actions as $action)
                                        <li>{{ $action }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($log->note)<p class="text-sm text-ink dark:text-ink-dark mt-1"><span class="text-primary dark:text-primary-dark font-medium">كلمتين لنفسي:</span> {{ $log->note }}</p>@endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="$dispatch('edit-recovery-log', { log: {{ $log->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="$dispatch('delete-recovery-log', { log: {{ $log->id }} })" wire:confirm="حذف هذا السجل؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">لا توجد سجلات بعد.</p>
            @endforelse
        </div>
    </div>

    <livewire:recovery.manage-recovery />
    <livewire:recovery.manage-log />
    <livewire:recovery.manage-story />
</div>
