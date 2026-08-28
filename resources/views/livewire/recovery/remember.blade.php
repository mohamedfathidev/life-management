<div class="min-h-screen bg-gradient-to-b from-bg-light via-bg-light to-danger/5 dark:from-bg-dark dark:via-bg-dark dark:to-danger-dark/10"
    x-data="{
        salvationOpen: false,
        remaining: 15 * 60,
        timer: null,
        start() {
            this.salvationOpen = true;
            this.remaining = 15 * 60;
            clearInterval(this.timer);
            this.timer = setInterval(() => { if (this.remaining > 0) this.remaining--; }, 1000);
        },
        stop() {
            this.salvationOpen = false;
            clearInterval(this.timer);
        },
        get clock() {
            const m = Math.floor(this.remaining / 60).toString().padStart(2, '0');
            const s = (this.remaining % 60).toString().padStart(2, '0');
            return m + ':' + s;
        },
    }">
    <div class="max-w-5xl mx-auto px-4 py-8 sm:py-12">

        <a href="{{ route('recovery.index') }}" wire:navigate class="inline-block text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark mb-6">← رجوع</a>

        {{-- Hero --}}
        <div class="text-center mb-10">
            <div class="inline-block px-4 py-1.5 rounded-full bg-ink/10 dark:bg-white/10 text-ink dark:text-ink-dark text-xs font-semibold mb-3 tracking-wide">
                قف لحظة قبل ما تاخد خطوة
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-ink dark:text-ink-dark mb-3">
                🛑 قبل الوقوع... تذكر
            </h1>
            <p class="text-base sm:text-lg text-ink-soft dark:text-ink-dark-soft max-w-xl mx-auto leading-relaxed">
                إنت دلوقتي واقف في مفترق طريق. خطوة واحدة هتحدد أنت رايح فين.
                اقرا الاتنين لحد الآخر قبل ما تختار.
            </p>
        </div>

        {{-- Decorative fork --}}
        <div class="flex justify-center mb-8 select-none pointer-events-none" aria-hidden="true">
            <svg viewBox="0 0 400 140" class="w-64 sm:w-80 h-auto">
                <path d="M200,8 C200,50 330,60 340,132" fill="none" class="stroke-danger dark:stroke-danger-dark" stroke-width="6" stroke-linecap="round" />
                <path d="M200,8 C200,50 70,60 60,132" fill="none" class="stroke-success dark:stroke-success-dark" stroke-width="6" stroke-linecap="round" />
                <circle cx="200" cy="8" r="6" class="fill-ink dark:fill-ink-dark" />
                <text x="200" y="24" text-anchor="middle" class="fill-ink dark:fill-ink-dark text-[11px] font-bold">إنت هنا</text>
                <circle cx="340" cy="132" r="5" class="fill-danger dark:fill-danger-dark" />
                <circle cx="60" cy="132" r="5" class="fill-success dark:fill-success-dark" />
            </svg>
        </div>

        {{-- The two paths --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- طريق الهلاك --}}
            <div class="rounded-2xl border-2 border-danger/30 dark:border-danger-dark/30 bg-gradient-to-b from-danger/10 via-surface-light to-surface-light dark:from-danger-dark/15 dark:via-surface-dark dark:to-surface-dark shadow-lg overflow-hidden flex flex-col">
                <div class="px-5 py-4 bg-danger/15 dark:bg-danger-dark/20 border-b border-danger/20 dark:border-danger-dark/20">
                    <h2 class="text-lg font-bold text-danger dark:text-danger-dark flex items-center gap-2">⚠️ طريق الهلاك</h2>
                </div>

                <div class="p-5 space-y-5 flex-1">
                    <div>
                        <p class="text-xs font-semibold text-ink-soft dark:text-ink-dark-soft mb-2">بيبدأ بـ...</p>
                        <ul class="space-y-1.5 text-sm text-ink dark:text-ink-dark">
                            @forelse ($recentTriggers as $trigger)
                                <li class="flex gap-2"><span class="text-danger">•</span><span>{{ $trigger }}</span></li>
                            @empty
                                <li class="flex gap-2"><span class="text-danger">•</span><span>فكرة عابرة بتقول لك "جرّب مرة واحدة بس"</span></li>
                                <li class="flex gap-2"><span class="text-danger">•</span><span>لحظة فراغ أو ملل بتفتح فيها الباب</span></li>
                                <li class="flex gap-2"><span class="text-danger">•</span><span>قرار صغير إنك تفتح حاجة "من غير قصد"</span></li>
                            @endforelse
                            @foreach ($hardMoments as $moment)
                                <li class="flex gap-2"><span class="text-danger">•</span><span>{{ $moment->title }}</span></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="text-center text-danger/50 dark:text-danger-dark/50 text-xl">↓</div>

                    <div>
                        <p class="text-xs font-semibold text-ink-soft dark:text-ink-dark-soft mb-2">وحصادك بيكون...</p>
                        <ul class="space-y-1.5 text-sm text-ink dark:text-ink-dark">
                            @forelse ($damages as $damage)
                                <li class="flex gap-2"><span class="text-danger">•</span><span>{{ $damage->title }}</span></li>
                            @empty
                                <li class="flex gap-2"><span class="text-danger">•</span><span>ندم بيقعدلك أيام</span></li>
                                <li class="flex gap-2"><span class="text-danger">•</span><span>وقت وطاقة ضايعين كان ممكن تبني بيهم حاجة</span></li>
                                <li class="flex gap-2"><span class="text-danger">•</span><span>ثقتك في نفسك بتتهزّ من جديد</span></li>
                            @endforelse
                            @foreach ($mistakes as $mistake)
                                <li class="flex gap-2"><span class="text-danger">•</span><span>{{ $mistake->title }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if ($setbackCount > 0)
                    <div class="px-5 py-3 bg-danger/10 dark:bg-danger-dark/10 border-t border-danger/20 dark:border-danger-dark/20 text-xs text-danger dark:text-danger-dark font-medium text-center">
                        رجعت للطريق ده {{ $setbackCount }} {{ $setbackCount === 1 ? 'مرة' : 'مرة' }} قبل كده — عارف نهايته
                    </div>
                @endif
                <a href="{{ route('recovery.remember.road', 'destruction') }}" wire:navigate
                    class="block px-5 py-3 bg-danger/20 dark:bg-danger-dark/25 text-danger dark:text-danger-dark text-sm font-bold text-center hover:opacity-90 transition">
                    🗺️ اتفرّج على الطريق ده بالتفصيل ←
                </a>
            </div>

            {{-- طريق النجاة --}}
            <div class="rounded-2xl border-2 border-success/30 dark:border-success-dark/30 bg-gradient-to-b from-success/10 via-surface-light to-surface-light dark:from-success-dark/15 dark:via-surface-dark dark:to-surface-dark shadow-lg overflow-hidden flex flex-col">
                <div class="px-5 py-4 bg-success/15 dark:bg-success-dark/20 border-b border-success/20 dark:border-success-dark/20">
                    <h2 class="text-lg font-bold text-success dark:text-success-dark flex items-center gap-2">🌱 طريق النجاة</h2>
                </div>

                <div class="p-5 space-y-5 flex-1">
                    <div>
                        <p class="text-xs font-semibold text-ink-soft dark:text-ink-dark-soft mb-2">بيبدأ بـ...</p>
                        <ul class="space-y-1.5 text-sm text-ink dark:text-ink-dark">
                            @forelse ($protectionActions as $action)
                                <li class="flex gap-2"><span class="text-success">•</span><span>{{ $action }}</span></li>
                            @empty
                                <li class="flex gap-2"><span class="text-success">•</span><span>قرار سريع إنك تسيب المكان أو تقفل الموبايل</span></li>
                                <li class="flex gap-2"><span class="text-success">•</span><span>مكالمة أو رسالة لحد بتثق فيه</span></li>
                                <li class="flex gap-2"><span class="text-success">•</span><span>سجدة أو دعوة صغيرة تقولها دلوقتي</span></li>
                            @endforelse
                            @foreach ($copingPlans as $plan)
                                <li class="flex gap-2"><span class="text-success">•</span><span>{{ $plan->plan }}</span></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="text-center text-success/50 dark:text-success-dark/50 text-xl">↓</div>

                    <div>
                        <p class="text-xs font-semibold text-ink-soft dark:text-ink-dark-soft mb-2">وحصادك بيكون...</p>
                        <ul class="space-y-1.5 text-sm text-ink dark:text-ink-dark">
                            @forelse ($dreams as $dream)
                                <li class="flex gap-2"><span class="text-success">•</span><span>{{ $dream->title }}</span></li>
                            @empty
                                <li class="flex gap-2"><span class="text-success">•</span><span>فخر بنفسك حتى لو محدش شافه</span></li>
                                <li class="flex gap-2"><span class="text-success">•</span><span>يوم إضافي في رصيدك، ما بيترجعش</span></li>
                                <li class="flex gap-2"><span class="text-success">•</span><span>ثقة بتكبر مع كل مرة تنتصر فيها</span></li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="px-5 py-3 bg-success/10 dark:bg-success-dark/10 border-t border-success/20 dark:border-success-dark/20 text-xs text-success dark:text-success-dark font-medium text-center">
                    @if ($currentStreak > 0)
                        عندك {{ $currentStreak }} يوم نضافة متواصل دلوقتي — ماتكسرهوش
                    @else
                        النهاردة ممكن يكون أول يوم في سلسلة جديدة
                    @endif
                    @if ($bestStreak > 0)
                        · أطول سلسلة ليك: {{ $bestStreak }} يوم
                    @endif
                </div>
                <a href="{{ route('recovery.remember.road', 'salvation') }}" wire:navigate
                    class="block px-5 py-3 bg-success/20 dark:bg-success-dark/25 text-success dark:text-success-dark text-sm font-bold text-center hover:opacity-90 transition">
                    🗺️ اتفرّج على الطريق ده بالتفصيل ←
                </a>
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center mt-10">
            <button type="button" @click="start()"
                class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-success to-success/80 dark:from-success-dark dark:to-success-dark/80 text-white text-base font-bold shadow-lg shadow-success/25 dark:shadow-success-dark/25 hover:shadow-xl hover:scale-105 transition-all duration-200">
                🙏 الحمد لله، اخترت طريق النجاة
            </button>
        </div>
    </div>

    {{-- Glowing instructions popup: fires the moment "طريق النجاة" is chosen —
         the concrete first moves to make right now, not just words. --}}
    <div x-show="salvationOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-lg" x-show="salvationOpen" x-transition.scale.origin.center>
            {{-- Radiant glow halo --}}
            <div class="absolute -inset-4 rounded-[2rem] bg-success/40 dark:bg-success-dark/40 blur-2xl animate-pulse pointer-events-none"></div>
            <span class="absolute -inset-2 rounded-[1.75rem] bg-success/30 dark:bg-success-dark/30 blur-xl animate-ping pointer-events-none"></span>

            <div class="relative rounded-3xl border-2 border-success/50 dark:border-success-dark/50 bg-surface-light dark:bg-surface-dark shadow-2xl p-6 sm:p-8 max-h-[90vh] overflow-y-auto text-center">
                <div class="text-4xl mb-2">🌱🕋</div>
                <h2 class="text-xl sm:text-2xl font-black text-success dark:text-success-dark mb-1">اخترت طريق النجاة — دلوقتي نفّذ</h2>
                <p class="text-xs sm:text-sm text-ink-soft dark:text-ink-dark-soft mb-6">مش وقت تفكير، وقت تحرّك. اعمل الخطوات دي بالترتيب:</p>

                <ol class="space-y-3 text-right">
                    <li class="flex items-start gap-3 rounded-xl bg-success/10 dark:bg-success-dark/10 p-3">
                        <span class="shrink-0 w-7 h-7 rounded-full bg-success dark:bg-success-dark text-white text-sm font-bold flex items-center justify-center">١</span>
                        <span class="text-sm sm:text-base text-ink dark:text-ink-dark font-medium">📵 اترك التليفون فورًا</span>
                    </li>
                    <li class="flex items-start gap-3 rounded-xl bg-success/10 dark:bg-success-dark/10 p-3">
                        <span class="shrink-0 w-7 h-7 rounded-full bg-success dark:bg-success-dark text-white text-sm font-bold flex items-center justify-center">٢</span>
                        <span class="text-sm sm:text-base text-ink dark:text-ink-dark font-medium">🚶 اتحرك من المكان اللي إنت فيه</span>
                    </li>
                    <li class="flex items-start gap-3 rounded-xl bg-success/10 dark:bg-success-dark/10 p-3">
                        <span class="shrink-0 w-7 h-7 rounded-full bg-success dark:bg-success-dark text-white text-sm font-bold flex items-center justify-center">٣</span>
                        <span class="text-sm sm:text-base text-ink dark:text-ink-dark font-medium">🪞 روح اغسل وشك وبُصّ لنفسك في المرايا</span>
                    </li>
                    <li class="flex items-start gap-3 rounded-xl bg-success/10 dark:bg-success-dark/10 p-3">
                        <span class="shrink-0 w-7 h-7 rounded-full bg-success dark:bg-success-dark text-white text-sm font-bold flex items-center justify-center">٤</span>
                        <span class="text-sm sm:text-base text-ink dark:text-ink-dark font-medium leading-relaxed">
                            ⏳ اعرف إنها ١٥ دقيقة بس وهتهدى. متتعجلش ولا تسترسل في الأفكار — بدّلها بالتفكير في أحلامك، وعلاقتك بربنا هتكون أقوى.
                        </span>
                    </li>
                    <li class="flex items-start gap-3 rounded-xl bg-success/10 dark:bg-success-dark/10 p-3">
                        <span class="shrink-0 w-7 h-7 rounded-full bg-success dark:bg-success-dark text-white text-sm font-bold flex items-center justify-center">٥</span>
                        <span class="text-sm sm:text-base text-ink dark:text-ink-dark font-medium leading-relaxed">
                            🚪 لو تقدر تطلع برا البيت، اطلع. ولو مش هتقدر، متنمش على السرير — غيّر المكان، شغّل قرآن، وتحكّم في أفكارك.
                        </span>
                    </li>
                </ol>

                <div class="mt-6 mb-2">
                    <div class="text-4xl sm:text-5xl font-black text-success dark:text-success-dark tracking-widest animate-pulse" x-text="clock"></div>
                    <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">هتعدي وهتهدى، زي كل مرة قبل كده</p>
                </div>

                <p class="text-lg sm:text-xl font-black text-primary dark:text-primary-dark my-4">قول بصوت عالي: يااااااااارب 🤲</p>

                <a href="{{ route('recovery.index') }}" wire:navigate @click="stop()"
                    class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-success to-success/80 dark:from-success-dark dark:to-success-dark/80 text-white text-base font-bold shadow-lg hover:scale-105 transition-all duration-200">
                    ✅ نفّذت الخطوات، كمّل
                </a>
            </div>
        </div>
    </div>
</div>
