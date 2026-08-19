<div class="py-8 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Navigation & Top Bar --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('recovery.mistakes') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-ink-soft hover:text-primary dark:text-ink-dark-soft dark:hover:text-primary-dark transition group">
                <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>أخطاء التعافي</span>
            </a>

            <button type="button" 
                    wire:click="delete" 
                    wire:confirm="هل أنت تأكد من حذف هذا الخطأ؟" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-danger hover:bg-red-50 dark:hover:bg-red-950/30 transition shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>حذف الخطأ</span>
            </button>
        </div>

        {{-- Success Flash Alert --}}
        @if ($savedSuccessfully)
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-medium flex items-center justify-between">
                <span>تم حفظ التعديلات والملاحظات بنجاح.</span>
                <button type="button" @click="show = false" class="text-emerald-500 hover:opacity-75">&times;</button>
            </div>
        @endif

        {{-- Main Mistake Card --}}
        <form wire:submit="save" 
              x-data="{ noteValue: @entangle('note') }"
              class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 shadow-md overflow-hidden transition-all duration-300">
            
            {{-- Accent Danger Top Border --}}
            <div class="h-1.5 bg-gradient-to-r from-red-500 via-rose-500 to-amber-500"></div>

            <div class="p-6 sm:p-8 space-y-6">

                {{-- Header Title Input & Impact Rating --}}
                <div class="space-y-4 border-b border-gray-100 dark:border-gray-800/80 pb-6">
                    <div>
                        <x-input-label for="ms_title" value="عنوان الخطأ" class="text-xs text-ink-soft dark:text-ink-dark-soft uppercase tracking-wider mb-1" />
                        <input id="ms_title" 
                               wire:model="title" 
                               type="text" 
                               class="w-full text-xl sm:text-2xl font-bold text-ink dark:text-ink-dark bg-transparent border-0 border-b border-gray-200 dark:border-gray-700 focus:border-danger focus:ring-0 px-0 py-1 transition" 
                               placeholder="مثال: السهر لوحدي على الموبايل بالليل" />
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>

                    {{-- Weight Slider --}}
                    <div x-data="{ w: @entangle('weight') }" class="p-4 rounded-xl bg-gray-50/70 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold text-ink dark:text-ink-dark flex items-center gap-1.5">
                                ⛓️ قد إيه بيعطّلك ويبقيك في السجن؟
                            </span>
                            <span class="text-sm font-extrabold text-danger px-2.5 py-0.5 rounded-full bg-danger/10 border border-danger/20" x-text="w + '%'"></span>
                        </div>
                        <input type="range" min="0" max="100" step="5" x-model.number="w" class="block w-full accent-danger cursor-pointer h-2 bg-gray-200 dark:bg-gray-700 rounded-lg" dir="ltr" />
                        <x-input-error :messages="$errors->get('weight')" class="mt-1" />
                    </div>
                </div>

                {{-- Rich-Text Editor (Trix) Section --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <x-input-label value="محرر الملاحظات والتفاصيل (إزاي أواجهه وأتجنّبه)" class="text-sm font-semibold text-ink dark:text-ink-dark" />
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">اكتب وتظهر المعاينة المنسقة في الأسفل 👇</span>
                    </div>

                    <div wire:ignore
                         x-data="{
                            init() {
                                const el = this.$refs.trix;
                                const load = () => { 
                                    if (el.editor && el.value !== (noteValue || '')) {
                                        el.editor.loadHTML(noteValue || ''); 
                                    }
                                };
                                el.addEventListener('trix-initialize', load);
                                el.addEventListener('trix-change', () => { noteValue = el.value; });
                                this.$watch('noteValue', () => load());
                            }
                         }">
                        <input id="mistake_note_input" type="hidden">
                        <trix-editor x-ref="trix" 
                                     input="mistake_note_input" 
                                     placeholder="اكتب هنا التفاصيل الكاملة، الأسباب، وخطوات لتجنب هذا الخطأ مستقبلاً..." 
                                     class="trix-content max-h-[220px] overflow-y-auto bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl p-4 text-ink dark:text-ink-dark leading-relaxed focus:border-danger focus:ring-danger text-base"></trix-editor>
                    </div>
                    <x-input-error :messages="$errors->get('note')" class="mt-1" />
                </div>

                {{-- Reading Preview Container --}}
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800/80 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="p-1 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold text-ink dark:text-ink-dark">معاينة القراءة المنسقة</h3>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50/70 dark:bg-gray-900/60 border border-gray-200/80 dark:border-gray-800/80 min-h-[140px]">
                        <div class="trix-content text-base sm:text-lg text-ink/95 dark:text-ink-dark/95 leading-relaxed sm:leading-loose space-y-4"
                             x-html="noteValue && noteValue.trim() !== '' ? noteValue : '<p class=\'text-sm text-ink-soft dark:text-ink-dark-soft italic\'>لا توجد ملاحظات مكتوبة بعد... اكتب تفاصيلك في المحرر أعلاه وتظهر هنا مباشرة بالتنسيق الكامل.</p>'">
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer Save Actions --}}
            <footer class="px-6 sm:px-8 py-4 bg-gray-50/50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-800/60 flex items-center justify-between">
                <span class="text-xs text-ink-soft dark:text-ink-dark-soft">
                    تاريخ الإضافة: {{ $mistake->created_at->translatedFormat('j M Y') }}
                </span>

                <div class="flex items-center gap-3">
                    <a href="{{ route('recovery.mistakes') }}" wire:navigate class="px-4 py-2 text-sm font-medium text-ink-soft hover:text-ink dark:text-ink-dark-soft dark:hover:text-ink-dark transition">
                        إلغاء
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-danger text-white text-sm font-bold shadow-md hover:opacity-90 active:scale-95 transition">
                        حفظ الملاحظات
                    </button>
                </div>
            </footer>
        </form>
    </div>
</div>
