<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($recovery)
            <a href="{{ route('recovery.show', $recovery) }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← {{ $recovery->title }}</a>
        @else
            <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
        @endif

        <div class="mt-1 mb-5">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">💔 الانتكاسات</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">
                @if ($recovery)
                    كل يوم انتكست فيه في {{ $recovery->title }} — الوقت والأسباب وحالة نومك.
                @else
                    كل يوم انتكست فيه — الوقت والأسباب وحالة نومك — عشان تشوف النمط وتكسره.
                @endif
            </p>
        </div>

        {{-- Totals across ALL recovery periods, regardless of status --}}
        <div class="mb-6">
            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-2xl bg-gradient-to-br from-primary/10 to-transparent dark:from-primary-dark/15 p-4 text-center">
                    <p class="text-2xl font-extrabold text-ink dark:text-ink-dark">{{ $totalRecoveryDays }}</p>
                    <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-0.5">🗓️ إجمالي الأيام</p>
                </div>
                <div class="rounded-2xl bg-success/10 p-4 text-center">
                    <p class="text-2xl font-extrabold text-success">{{ $totalCleanDays }}</p>
                    <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-0.5">🌿 أيام نضيفة</p>
                </div>
                <div class="rounded-2xl bg-danger/10 p-4 text-center">
                    <p class="text-2xl font-extrabold text-danger">{{ $totalSetbackDays }}</p>
                    <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-0.5">💔 انتكاسات</p>
                </div>
            </div>
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2 text-center">في كل فترات التعافي، أيًّا كانت حالتها</p>
        </div>

        {{-- Filter Control Card --}}
        <div class="mb-6 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 space-y-4 border border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2 pb-2 border-b border-gray-100 dark:border-gray-800">
                <svg class="w-5 h-5 text-primary dark:text-primary-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <h3 class="text-sm font-bold text-ink dark:text-ink-dark">تصفية الانتكاسات بحسب فترة التعافي</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Recovery Select Dropdown --}}
                <div class="space-y-1">
                    <label for="recovery_select" class="block text-xs font-semibold text-ink-soft dark:text-ink-dark-soft">
                        🎯 اختر فترة التعافي:
                    </label>
                    <select id="recovery_select" 
                            wire:model.live="recoveryId" 
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-ink dark:text-ink-dark text-sm font-medium focus:border-primary focus:ring-primary shadow-sm py-2.5 transition">
                        <option value="">كل فترات التعافي (عرض الكل)</option>
                        @foreach ($recoveries as $rec)
                            <option value="{{ $rec->id }}">
                                {{ $rec->title }} 
                                (من {{ $rec->start_date->translatedFormat('j M') }} @if($rec->end_date) إلى {{ $rec->end_date->translatedFormat('j M Y') }} @else - مفتوحة @endif)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Timeframe / Week Select Dropdown --}}
                <div class="space-y-1">
                    <label for="week_select" class="block text-xs font-semibold text-ink-soft dark:text-ink-dark-soft">
                        📅 تصفية المدة الزمنية:
                    </label>
                    <select id="week_select" 
                            wire:model.live="selectedWeek" 
                            class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-ink dark:text-ink-dark text-sm font-medium focus:border-primary focus:ring-primary shadow-sm py-2.5 transition">
                        @if ($recovery)
                            <option value="all">جميع أيام هذا التحدي</option>
                            @foreach ($availableWeeks as $w)
                                <option value="week-{{ $w->number }}">
                                    الأسبوع {{ $w->number }} في التحدي ({{ $w->start_date->translatedFormat('j M') }} - {{ $w->end_date->translatedFormat('j M') }})
                                    @if ($w->count > 0) [{{ $w->count }} انتكاسة] @endif
                                </option>
                            @endforeach
                        @else
                            <option value="all">جميع الأوقات</option>
                            <option value="7days">آخر 7 أيام</option>
                            <option value="30days">آخر 30 يوماً</option>
                            <option value="this_month">هذا الشهر</option>
                        @endif
                    </select>
                </div>
            </div>

            {{-- Summary Pill --}}
            <div class="pt-2 flex items-center justify-between text-xs text-ink-soft dark:text-ink-dark-soft border-t border-gray-100 dark:border-gray-800/60">
                <span>
                    @if ($recovery)
                        تعرض انتكاسات: <strong class="text-primary dark:text-primary-dark font-bold">{{ $recovery->title }}</strong>
                    @else
                        تعرض انتكاسات: <strong class="text-ink dark:text-ink-dark font-bold">جميع التحديات والفترات</strong>
                    @endif
                </span>
                <span class="px-2.5 py-1 rounded-full bg-danger/10 text-danger font-bold">
                    عدد الانتكاسات: {{ $setbacks->total() }}
                </span>
            </div>
        </div>

        {{-- Setbacks List --}}
        @if ($setbacks->isEmpty())
            <div class="text-center py-16 rounded-2xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🌱</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">
                    لا توجد انتكاسات في هذا التحديد — ممتاز جداً! 💪
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($setbacks as $log)
                    <div wire:key="sb-{{ $log->id }}" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 border-s-4 border-danger">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-ink dark:text-ink-dark">{{ $log->date->translatedFormat('l، j M Y') }}</p>
                                @if (!$recoveryId && $log->recovery)
                                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark font-semibold">
                                        {{ $log->recovery->title }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-danger/15 text-danger font-medium">انتكاسة</span>
                        </div>

                        {{-- Time + metrics --}}
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-ink-soft dark:text-ink-dark-soft">
                            @if ($log->hardest_from && $log->hardest_to)
                                <span>⏰ أصعب فترة: {{ \Illuminate\Support\Carbon::parse($log->hardest_from)->translatedFormat('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($log->hardest_to)->translatedFormat('g:i A') }}</span>
                            @endif
                            @if ($log->urge_level)<span>🌊 الرغبة: {{ $log->urge_level }}/10</span>@endif
                            @if ($log->mood)<span>😊 المزاج: {{ $log->mood }}/10</span>@endif
                        </div>

                        {{-- Night routine info from recovery log --}}
                        @if ($log->stayed_up_late !== null || $log->had_dinner !== null || $log->prepared_for_sleep !== null)
                            <div class="mt-3">
                                <p class="text-xs font-medium text-ink-soft dark:text-ink-dark-soft mb-1.5">🌙 النوم/التغذية:</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($log->stayed_up_late !== null)
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $log->stayed_up_late ? 'bg-danger/15 text-danger' : 'bg-success/15 text-success' }}">
                                            {{ $log->stayed_up_late ? '😴 سهرت' : '🛌 نمت مبكّر' }}
                                        </span>
                                    @endif
                                    @if ($log->had_dinner !== null)
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $log->had_dinner ? 'bg-success/15 text-success' : 'bg-warning/15 text-warning' }}">
                                            {{ $log->had_dinner ? '🍽️ اتغذّى' : '🚫 مااتغذّاش' }}
                                        </span>
                                    @endif
                                    @if ($log->prepared_for_sleep !== null)
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $log->prepared_for_sleep ? 'bg-success/15 text-success' : 'bg-warning/15 text-warning' }}">
                                            {{ $log->prepared_for_sleep ? '🛏️ استعدّ للنوم' : '⚠️ مااستعدّش' }}
                                        </span>
                                    @endif
                                    @if ($log->sleep_location)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-ink-soft/10 text-ink-soft dark:text-ink-dark-soft">{{ $log->sleep_location === 'home' ? '🏠 نام في البيت' : '🚶 نام برا' }}</span>
                                    @endif
                                    @if ($log->sleep_spot)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-ink-soft/10 text-ink-soft dark:text-ink-dark-soft">{{ $log->sleep_spot === 'bed' ? '🛏️ على السرير' : '↔️ نومة تانية' }}</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="mt-2">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-ink-soft/10 text-ink-soft dark:text-ink-dark-soft">🌙 النوم/التغذية: مش مسجّل</span>
                            </div>
                        @endif

                        {{-- Causes --}}
                        @if ($log->trigger_note)
                            <p class="text-sm text-ink dark:text-ink-dark mt-3"><span class="text-danger font-medium">المُحفّز:</span> {{ $log->trigger_note }}</p>
                        @endif
                        @if ($log->avoidance_reasons)
                            <div class="text-sm text-ink dark:text-ink-dark mt-1.5">
                                <span class="text-danger font-medium">ازاي كنت اقدر اتجنب السقوط:</span>
                                <ul class="list-disc pr-5 mt-0.5">
                                    @foreach ($log->avoidance_reasons as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($log->note)
                            <p class="text-sm text-ink dark:text-ink-dark mt-1.5 whitespace-pre-line"><span class="text-primary dark:text-primary-dark font-medium">كلمتين لنفسي:</span> {{ $log->note }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($setbacks->hasPages())
                <div class="mt-6">{{ $setbacks->links() }}</div>
            @endif
        @endif
    </div>
</div>
