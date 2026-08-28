<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 text-center">
            <a href="{{ route('recovery.index') }}" wire:navigate class="inline-block text-sm text-primary dark:text-primary-dark hover:underline mb-4">← العودة للتعافي</a>
            <div class="inline-block px-4 py-1.5 rounded-full bg-danger/10 dark:bg-danger-dark/10 text-danger dark:text-danger-dark text-xs font-semibold mb-3">
                سؤال ما بتسألوش لنفسك كتير
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-ink dark:text-ink-dark mb-2">
                🔒 ماذا لو لم أدخل هذا السجن؟
            </h1>
            <p class="text-base text-ink-soft dark:text-ink-dark-soft max-w-xl mx-auto leading-relaxed">
                واجه نفسك بالحقيقة — قد إيه ضاع، وإيه اللي كان ممكن يكون، لو السجن ده ما اتبناش أصلاً
            </p>
        </div>

        @if ($editing)
            {{-- Edit form --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-lg border border-danger/20 dark:border-danger-dark/20 overflow-hidden">
                <div class="bg-gradient-to-r from-danger/10 via-danger/5 to-transparent dark:from-danger-dark/20 dark:via-danger-dark/10 p-6 border-b border-danger/10 dark:border-danger-dark/10">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-danger/15 dark:bg-danger-dark/20 flex items-center justify-center text-2xl">🔒</div>
                        <div>
                            <h3 class="text-lg font-bold text-ink dark:text-ink-dark">اكتب مواجهتك</h3>
                            <p class="text-sm text-ink-soft dark:text-ink-dark-soft">كن صريح مع نفسك، محدش هيقرا ده غيرك</p>
                        </div>
                    </div>
                </div>

                <form wire:submit="save" class="p-6 space-y-6">
                    <div>
                        <x-input-label for="prison_years" value="السجن ده طال معاك كام سنة؟" />
                        <input type="number" id="prison_years" wire:model="prisonYears" min="0" max="80"
                            class="mt-1 block w-full sm:w-40 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-danger focus:ring-danger text-sm" placeholder="مثلاً: 5" />
                        <x-input-error :messages="$errors->get('prisonYears')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="prison_body" value="لو ما كنتش دخلت السجن ده... كان ممكن إيه يحصل؟" />
                        <div class="relative mt-1">
                            <textarea id="prison_body" wire:model="body" rows="10"
                                class="block w-full rounded-xl border-2 border-danger/30 dark:border-danger-dark/30 bg-gray-900 text-white focus:border-danger focus:ring-danger text-base leading-loose px-6 py-4 placeholder:text-white/30"
                                placeholder="كنت ممكن أكون دلوقتي...

كنت ممكن أوصل لـ...

كنت هفوّت على نفسي...

اللي ضاع مني بسبب السجن ده هو..."></textarea>
                        </div>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2 text-center">
                            💡 اكتبها بصراحة قد ما تقدر، الهدف إنك تشوف الصورة كاملة
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                        @if ($hasReflection)
                            <button type="button" wire:click="$set('editing', false)"
                                class="px-5 py-2.5 rounded-lg text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark hover:bg-ink-soft/5 dark:hover:bg-ink-dark-soft/5 transition">
                                إلغاء
                            </button>
                        @else
                            <div></div>
                        @endif
                        <button type="submit"
                            class="px-8 py-3 rounded-xl bg-gradient-to-r from-danger to-danger/80 dark:from-danger-dark dark:to-danger-dark/80 text-white text-base font-bold shadow-lg shadow-danger/25 dark:shadow-danger-dark/25 hover:shadow-xl hover:scale-105 transition-all duration-200">
                            احفظ
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Display: styled like a prison cell — dark, barred, heavy --}}
            <div class="relative rounded-2xl overflow-hidden shadow-2xl border-2 border-danger/40">
                <div class="relative bg-gradient-to-b from-black via-gray-900 to-black p-8 sm:p-10">
                    {{-- Bar texture, drawn as real SVG lines (not an opacity utility that can silently fail to compile) --}}
                    <svg class="absolute inset-0 w-full h-full pointer-events-none" preserveAspectRatio="none" aria-hidden="true">
                        <defs>
                            <pattern id="prisonBars" width="34" height="10" patternUnits="userSpaceOnUse">
                                <rect x="0" y="0" width="4" height="10" fill="rgba(255,255,255,0.05)" />
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#prisonBars)" />
                    </svg>

                    <div class="relative text-center mb-6">
                        @if ($prisonYears !== null)
                            <span class="inline-block px-5 py-2 rounded-full bg-danger/20 border border-danger/40 text-danger text-sm font-bold mb-4">
                                🔒 {{ $prisonYears }} {{ $prisonYears === 1 ? 'سنة' : 'سنين' }} في السجن ده
                            </span>
                        @endif
                    </div>

                    <div class="relative">
                        <p class="text-lg sm:text-xl leading-loose text-white/90 whitespace-pre-line text-center">{{ $body }}</p>
                    </div>

                    <div class="relative mt-8 pt-6 border-t border-white/10 flex items-center justify-between flex-wrap gap-4">
                        <div class="text-xs text-white/40">
                            @if ($savedAt)
                                آخر تحديث: {{ $savedAt->translatedFormat('j F Y') }}
                            @endif
                        </div>
                        <button type="button" wire:click="edit"
                            class="px-5 py-2 rounded-lg bg-white/10 text-white text-sm font-medium hover:bg-white/20 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            تعديل
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
