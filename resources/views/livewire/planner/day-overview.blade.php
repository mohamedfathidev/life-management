<div class="py-8 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Top Navigation & Date Bar --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-ink dark:text-ink-dark flex items-center gap-2">
                        <span>📖 حكاية ونظرة على اليوم</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-ink-soft dark:text-ink-dark-soft mt-1">
                        قصة يومك المترابطة: تتبع النوم، العبادات، التاسكات، التعافي، وتقييم اليوم في لوحة واحدة.
                    </p>
                </div>

                {{-- Date Navigation Control --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" 
                            wire:click="previousDay" 
                            class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-ink dark:text-ink-dark text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        ← اليوم السابق
                    </button>

                    <div class="relative">
                        <input type="date" 
                               wire:model.live="date" 
                               class="rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-ink dark:text-ink-dark text-xs font-bold px-3 py-2 focus:border-primary focus:ring-primary shadow-sm" />
                    </div>

                    <button type="button" 
                            wire:click="setToday" 
                            class="px-3 py-2 rounded-xl bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark text-xs font-bold hover:bg-primary/20 transition">
                        اليوم
                    </button>

                    <button type="button" 
                            wire:click="nextDay" 
                            class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-ink dark:text-ink-dark text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        اليوم التالي →
                    </button>
                </div>
            </div>

            {{-- Selected Date Display & Status --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gray-100 dark:border-gray-800/80 text-xs">
                <span class="font-bold text-ink dark:text-ink-dark flex items-center gap-1.5">
                    <span>🗓️ {{ $targetDate->translatedFormat('l، j M Y') }}</span>
                </span>

                <div class="flex items-center gap-2">
                    @if ($day && $day->isClosed())
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-semibold border border-emerald-500/20">
                            🔒 اليوم مغلق (تم التقفيل)
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full bg-amber-500/15 text-amber-600 dark:text-amber-400 font-semibold border border-amber-500/20">
                            🔓 اليوم مفتوح
                        </span>
                    @endif

                    <a href="{{ route('planner.day', $targetDate->toDateString()) }}" wire:navigate class="text-primary dark:text-primary-dark hover:underline font-medium">
                        الانتقال لجدول اليوم ↗
                    </a>
                </div>
            </div>
        </div>

        {{-- Hero Overview Banner & Score Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            {{-- Mood & Closure Score Card --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-2 flex flex-col justify-between">
                <span class="text-xs font-bold text-ink-soft dark:text-ink-dark-soft uppercase tracking-wider">
                    😊 مزاج وتقييم اليوم
                </span>

                <div>
                    @if ($day && $day->rating)
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-primary dark:text-primary-dark">
                                {{ $day->rating }}/10
                            </span>
                            <span class="text-xs font-semibold text-ink-soft dark:text-ink-dark-soft">من تقفيل اليوم</span>
                        </div>
                    @else
                        <span class="text-base font-semibold text-ink-soft dark:text-ink-dark-soft italic">
                            لم يتم تقفيل اليوم بعد
                        </span>
                    @endif
                </div>

                <div class="text-xs text-ink-soft dark:text-ink-dark-soft pt-1">
                    @if ($day && $day->workedMinutes() > 0)
                        <span>⏱️ وقت العمل: {{ $day->workedHoursLabel() }}</span>
                    @else
                        <span>⏱️ لم يسجل ساعات عمل</span>
                    @endif
                </div>
            </div>

            {{-- Task Progress Card --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-2 flex flex-col justify-between">
                <span class="text-xs font-bold text-ink-soft dark:text-ink-dark-soft uppercase tracking-wider">
                    ✅ إنجاز التاسكات
                </span>

                <div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-extrabold text-ink dark:text-ink-dark">
                            {{ $completionPercent }}%
                        </span>
                        <span class="text-xs font-medium text-ink-soft dark:text-ink-dark-soft">
                            ({{ $completedTasksCount }}/{{ $totalTasksCount }} مهام)
                        </span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-gray-100 dark:bg-gray-800 h-2 rounded-full mt-2 overflow-hidden">
                        <div class="bg-primary dark:bg-primary-dark h-full rounded-full transition-all duration-500" style="width: {{ $completionPercent }}%"></div>
                    </div>
                </div>

                <span class="text-xs text-ink-soft dark:text-ink-dark-soft">
                    @if ($totalTasksCount === 0) لا توجد تاسكات مجدولة @else متبقي {{ $totalTasksCount - $completedTasksCount }} مهمة @endif
                </span>
            </div>

            {{-- Recovery / Setback Status Card --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-2 flex flex-col justify-between">
                <span class="text-xs font-bold text-ink-soft dark:text-ink-dark-soft uppercase tracking-wider">
                    🌱 حالة التعافي
                </span>

                <div>
                    @if ($recoveryLog && $recoveryLog->is_setback)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/15 text-rose-600 dark:text-rose-400 font-extrabold text-sm border border-rose-500/20">
                            🚨 تم تسجيل انتكاسة
                        </div>
                    @else
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-extrabold text-sm border border-emerald-500/20">
                            🌱 يوم نظيف ومبارك
                        </div>
                    @endif
                </div>

                <div class="text-xs text-ink-soft dark:text-ink-dark-soft">
                    @if ($recoveryLog && $recoveryLog->urge_level)
                        <span>🌊 شدة الرغبة: {{ $recoveryLog->urge_level }}/10</span>
                    @else
                        <span>الانضباط مستمر بفضل الله</span>
                    @endif
                </div>
            </div>

        </div>

        {{-- Smart Pattern Correlation Card --}}
        <div class="p-5 sm:p-6 rounded-2xl {{ $patternInsight['bgClass'] }} {{ $patternInsight['textClass'] }} border {{ $patternInsight['borderClass'] }} shadow-sm space-y-2 transition-all duration-300">
            <div class="flex items-center gap-2">
                <span class="text-lg">💡</span>
                <h3 class="text-sm font-bold uppercase tracking-wider">{{ $patternInsight['title'] }}</h3>
            </div>
            <p class="text-sm sm:text-base leading-relaxed font-medium">
                {{ $patternInsight['text'] }}
            </p>
        </div>

        {{-- The Interconnected Story Timeline (حكاية اليوم المترابطة) --}}
        <div class="space-y-6 relative before:absolute before:inset-0 before:left-auto before:right-6 sm:before:right-8 before:w-0.5 before:bg-gradient-to-b before:from-primary/40 before:via-emerald-400/40 before:to-gray-300 dark:before:to-gray-800">
            
            {{-- Chapter 1: Night Routine & Sleep --}}
            <div class="relative pr-12 sm:pr-16 space-y-3">
                <div class="absolute top-0 right-3 sm:right-5 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold shadow-md">
                    1
                </div>

                <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-ink dark:text-ink-dark flex items-center gap-2">
                            <span>🌙 الفصل الأول: ليلة البداية والنوم</span>
                        </h3>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">الروتين المسائي</span>
                    </div>

                    @if ($recoveryLog && ($recoveryLog->stayed_up_late !== null || $recoveryLog->had_dinner !== null || $recoveryLog->prepared_for_sleep !== null))
                        <div class="flex flex-wrap items-center gap-2.5 pt-1">
                            @if ($recoveryLog->stayed_up_late !== null)
                                <span class="text-xs px-3 py-1 rounded-full font-bold {{ $recoveryLog->stayed_up_late ? 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20' : 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' }}">
                                    {{ $recoveryLog->stayed_up_late ? '😴 سهرت بالليل' : '🛌 نمت مبكراً' }}
                                </span>
                            @endif

                            @if ($recoveryLog->had_dinner !== null)
                                <span class="text-xs px-3 py-1 rounded-full font-bold {{ $recoveryLog->had_dinner ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20' }}">
                                    {{ $recoveryLog->had_dinner ? '🍽️ اتغذيت جيدا' : '🚫 مااتغذّيتش' }}
                                </span>
                            @endif

                            @if ($recoveryLog->prepared_for_sleep !== null)
                                <span class="text-xs px-3 py-1 rounded-full font-bold {{ $recoveryLog->prepared_for_sleep ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20' }}">
                                    {{ $recoveryLog->prepared_for_sleep ? '🛏️ استعديت للنوم' : '⚠️ مااستعدّيتش للنوم' }}
                                </span>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft italic">
                            لم يتم تسجيل تفاصيل روتين النوم والتغذية لهذا اليوم.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Chapter 2: Worship & Morning Habits --}}
            <div class="relative pr-12 sm:pr-16 space-y-3">
                <div class="absolute top-0 right-3 sm:right-5 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shadow-md">
                    2
                </div>

                <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-ink dark:text-ink-dark flex items-center gap-2">
                            <span>☀️ الفصل الثاني: العبادات والعادات اليومية</span>
                        </h3>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">الورد والأذكار</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        {{-- Quran Wird Status --}}
                        <div class="p-3 rounded-xl bg-gray-50/70 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <span class="text-xs font-bold text-ink dark:text-ink-dark">📖 ورد القرآن الكريم:</span>
                            @if ($quranWirdDone)
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold">تم بفضل الله ✓</span>
                            @else
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-gray-200/60 dark:bg-gray-700/60 text-ink-soft dark:text-ink-dark-soft font-medium">لم يقرأ بعد</span>
                            @endif
                        </div>

                        {{-- Prayers Status --}}
                        <div class="p-3 rounded-xl bg-gray-50/70 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <span class="text-xs font-bold text-ink dark:text-ink-dark">📿 الصلوات الخمس:</span>
                            @if ($prayerDay)
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold">
                                    {{ $prayerDay->doneCount() }}/5 صلوات
                                </span>
                            @else
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-gray-200/60 dark:bg-gray-700/60 text-ink-soft dark:text-ink-dark-soft font-medium">غير مسجلة</span>
                            @endif
                        </div>
                    </div>

                    {{-- Habits Logged --}}
                    @if ($habitsLogged->isNotEmpty())
                        <div class="pt-2">
                            <span class="text-xs font-semibold text-ink-soft dark:text-ink-dark-soft block mb-2">🔁 العادات المكتملة اليوم:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($habitsLogged as $hl)
                                    <span class="text-xs px-2.5 py-1 rounded-full bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark font-medium">
                                        ✓ {{ $hl->habit->title }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Chapter 3: Productivity & Tasks --}}
            <div class="relative pr-12 sm:pr-16 space-y-3">
                <div class="absolute top-0 right-3 sm:right-5 w-6 h-6 rounded-full bg-teal-500 text-white flex items-center justify-center text-xs font-bold shadow-md">
                    3
                </div>

                <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-ink dark:text-ink-dark flex items-center gap-2">
                            <span>💻 الفصل الثالث: الإنتاجية وتنفيذ المهام</span>
                        </h3>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">{{ $completedTasksCount }}/{{ $totalTasksCount }} مهام</span>
                    </div>

                    @if ($tasks->isNotEmpty())
                        <div class="space-y-2 pt-1">
                            @foreach ($tasks as $t)
                                <div class="flex items-center justify-between gap-3 text-xs p-2.5 rounded-xl bg-gray-50/70 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800/60">
                                    <div class="flex items-center gap-2 min-w-0">
                                        @if ($t->isCompleted())
                                            <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold">✓</span>
                                        @else
                                            <span class="w-4 h-4 rounded-full border border-gray-300 dark:border-gray-600 block"></span>
                                        @endif
                                        <span class="font-semibold text-ink dark:text-ink-dark truncate {{ $t->isCompleted() ? 'line-through opacity-70' : '' }}">
                                            {{ $t->title }}
                                        </span>
                                    </div>
                                    <span class="text-ink-soft dark:text-ink-dark-soft shrink-0">
                                        {{ $t->expected_minutes }}د
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft italic">
                            لم تكن هناك تاسكات مجدولة في هذا اليوم.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Chapter 4: Recovery & Relapse Notes --}}
            <div class="relative pr-12 sm:pr-16 space-y-3">
                <div class="absolute top-0 right-3 sm:right-5 w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs font-bold shadow-md">
                    4
                </div>

                <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-ink dark:text-ink-dark flex items-center gap-2">
                            <span>🌱 الفصل الرابع: التعافي والتحديات المسجلة</span>
                        </h3>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">سجل التعافي</span>
                    </div>

                    @if ($recoveryLog)
                        <div class="space-y-2 pt-1 text-xs sm:text-sm">
                            @if ($recoveryLog->hardest_from && $recoveryLog->hardest_to)
                                <p class="text-ink-soft dark:text-ink-dark-soft">
                                    ⏰ <strong>أصعب فترة في اليوم:</strong> {{ \Illuminate\Support\Carbon::parse($recoveryLog->hardest_from)->translatedFormat('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($recoveryLog->hardest_to)->translatedFormat('g:i A') }}
                                </p>
                            @endif

                            @if ($recoveryLog->trigger_note)
                                <p class="text-ink dark:text-ink-dark">
                                    <span class="text-rose-500 font-bold">المُحفّز:</span> {{ $recoveryLog->trigger_note }}
                                </p>
                            @endif

                            @if ($recoveryLog->note)
                                <p class="text-ink dark:text-ink-dark">
                                    <span class="text-primary dark:text-primary-dark font-bold">كلمتين لنفسي:</span> {{ $recoveryLog->note }}
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft italic">
                            لم يتم تدوين ملاحظات انتكاسة أو محفزات لهذا اليوم.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Chapter 5: Day Closure & Reflection --}}
            <div class="relative pr-12 sm:pr-16 space-y-3">
                <div class="absolute top-0 right-3 sm:right-5 w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold shadow-md">
                    5
                </div>

                <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-ink dark:text-ink-dark flex items-center gap-2">
                            <span>📊 الفصل الخامس: ختام وتأمل اليوم (تقفيل اليوم)</span>
                        </h3>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">تقفيل اليوم</span>
                    </div>

                    @if ($day && $day->isClosed())
                        <div class="space-y-2 pt-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-ink dark:text-ink-dark">تقييم ومزاج ختام اليوم:</span>
                                <span class="text-sm font-extrabold text-primary dark:text-primary-dark">{{ $day->rating }}/10</span>
                            </div>

                            @if ($day->reflection)
                                <div class="p-3 rounded-xl bg-gray-50/70 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 text-xs text-ink dark:text-ink-dark leading-relaxed">
                                    <span class="font-bold text-ink-soft dark:text-ink-dark-soft block mb-1">تأمل التقفيل:</span>
                                    {{ $day->reflection }}
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft italic">
                            لم يتم تقفيل اليوم بعد. يمكنك تقفيله من شاشة المخطط.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Chapter 6: Diary Entry Snippet --}}
            @if ($diaryEntry)
                <div class="relative pr-12 sm:pr-16 space-y-3">
                    <div class="absolute top-0 right-3 sm:right-5 w-6 h-6 rounded-full bg-purple-500 text-white flex items-center justify-center text-xs font-bold shadow-md">
                        6
                    </div>

                    <div class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                            <h3 class="text-base font-bold text-ink dark:text-ink-dark flex items-center gap-2">
                                <span>📖 مذكرات هذا اليوم</span>
                            </h3>
                            <a href="{{ route('diary.show', $diaryEntry) }}" wire:navigate class="text-xs text-primary dark:text-primary-dark font-semibold hover:underline">
                                استعراض المذكرة ↗
                            </a>
                        </div>

                        <div class="text-xs text-ink dark:text-ink-dark space-y-1">
                            <h4 class="font-bold text-sm">{{ $diaryEntry->title ?: 'بدون عنوان' }}</h4>
                            <p class="text-ink-soft dark:text-ink-dark-soft line-clamp-2">
                                {{ Str::limit(strip_tags($diaryEntry->content), 150) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
