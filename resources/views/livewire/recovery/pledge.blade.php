<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 text-center">
            <a href="{{ route('recovery.index') }}" wire:navigate class="inline-block text-sm text-primary dark:text-primary-dark hover:underline mb-4">← العودة للتعافي</a>
            <div class="inline-block px-4 py-1.5 rounded-full bg-primary/10 dark:bg-primary-dark/10 text-primary dark:text-primary-dark text-xs font-semibold mb-3">
                عهد وميثاق
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-ink dark:text-ink-dark mb-2">
                🤝 تعهد أمام الله
            </h1>
            <p class="text-base text-ink-soft dark:text-ink-dark-soft max-w-xl mx-auto leading-relaxed">
                هذا ميثاق بينك وبين الله، كلماتك أنت من قلبك، تفتكرها في لحظات الضعف لتستمد منها القوة والعزيمة
            </p>
        </div>

        @if ($editing)
            {{-- Enhanced Edit Form --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-lg border border-primary/20 dark:border-primary-dark/20 overflow-hidden">
                {{-- Header with decorative element --}}
                <div class="bg-gradient-to-r from-primary/10 via-primary/5 to-transparent dark:from-primary-dark/20 dark:via-primary-dark/10 p-6 border-b border-primary/10 dark:border-primary-dark/10">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-primary/20 dark:bg-primary-dark/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary dark:text-primary-dark" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-ink dark:text-ink-dark">اكتب تعهدك</h3>
                            <p class="text-sm text-ink-soft dark:text-ink-dark-soft">كن صادقاً مع نفسك ومع ربك</p>
                        </div>
                    </div>
                </div>

                <form wire:submit="save" class="p-6 space-y-6">
                    {{-- Writing area with enhanced styling --}}
                    <div>
                        <div class="relative">
                            <div class="absolute top-4 right-4 text-4xl text-primary/20 dark:text-primary-dark/20 pointer-events-none select-none">﴿</div>
                            <textarea id="pledge_body" wire:model="body" rows="10"
                                class="mt-1 block w-full rounded-xl border-2 border-primary/30 dark:border-primary-dark/30 bg-gradient-to-br from-primary/5 to-transparent dark:from-primary-dark/10 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-lg leading-loose px-6 py-4 placeholder:text-ink-soft/50"
                                placeholder="أتعهّد أمام الله بأن...

أعاهد نفسي على...

سأسعى جاهداً لـ...

وأقسم بعزة الله أن..."></textarea>
                            <div class="absolute bottom-4 left-4 text-4xl text-primary/20 dark:text-primary-dark/20 pointer-events-none select-none">﴾</div>
                        </div>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2 text-center">
                            💡 اكتب بصدق وصراحة، هذا العهد لك وحدك مع الله
                        </p>
                    </div>

                    {{-- Action buttons with enhanced styling --}}
                    <div class="flex items-center justify-between pt-4 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                        @if ($hasPledge)
                            <button type="button" wire:click="$set('editing', false)" 
                                class="px-5 py-2.5 rounded-lg text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark hover:bg-ink-soft/5 dark:hover:bg-ink-dark-soft/5 transition">
                                إلغاء
                            </button>
                        @else
                            <div></div>
                        @endif
                        <button type="submit" 
                            class="px-8 py-3 rounded-xl bg-gradient-to-r from-primary to-primary/80 dark:from-primary-dark dark:to-primary-dark/80 text-white text-base font-bold shadow-lg shadow-primary/25 dark:shadow-primary-dark/25 hover:shadow-xl hover:scale-105 transition-all duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>حفظ التعهد</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Enhanced Display of Committed Pledge --}}
            <div class="relative">
                {{-- Decorative corner elements --}}
                <div class="absolute -top-2 -right-2 w-16 h-16 opacity-10">
                    <svg viewBox="0 0 100 100" class="text-primary dark:text-primary-dark fill-current">
                        <path d="M0,0 L100,0 L100,100 Z"/>
                    </svg>
                </div>
                <div class="absolute -bottom-2 -left-2 w-16 h-16 opacity-10 rotate-180">
                    <svg viewBox="0 0 100 100" class="text-primary dark:text-primary-dark fill-current">
                        <path d="M0,0 L100,0 L100,100 Z"/>
                    </svg>
                </div>

                {{-- Main pledge card --}}
                <div class="relative rounded-2xl border-3 border-primary/40 dark:border-primary-dark/40 bg-gradient-to-br from-primary/5 via-surface-light to-secondary/5 dark:from-primary-dark/15 dark:via-surface-dark dark:to-secondary-dark/10 shadow-2xl overflow-hidden">
                    {{-- Top decorative bar --}}
                    <div class="h-1.5 bg-gradient-to-r from-transparent via-primary dark:via-primary-dark to-transparent"></div>
                    
                    {{-- Content --}}
                    <div class="p-8 sm:p-12">
                        {{-- Opening ornament --}}
                        <div class="text-center mb-6">
                            <div class="inline-block">
                                <div class="text-6xl text-primary/40 dark:text-primary-dark/40 leading-none mb-2">﴿</div>
                                <div class="h-0.5 w-16 bg-gradient-to-r from-transparent via-primary/30 dark:via-primary-dark/30 to-transparent"></div>
                            </div>
                        </div>

                        {{-- The pledge text --}}
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-primary/5 to-transparent dark:via-primary-dark/5 rounded-xl -m-4 pointer-events-none"></div>
                            <p class="relative text-xl sm:text-2xl leading-loose text-ink dark:text-ink-dark whitespace-pre-line text-center font-medium px-4" style="font-family: 'Cairo', serif;">{{ $body }}</p>
                        </div>

                        {{-- Closing ornament --}}
                        <div class="text-center mt-6">
                            <div class="inline-block">
                                <div class="h-0.5 w-16 bg-gradient-to-r from-transparent via-primary/30 dark:via-primary-dark/30 to-transparent mb-2"></div>
                                <div class="text-6xl text-primary/40 dark:text-primary-dark/40 leading-none">﴾</div>
                            </div>
                        </div>

                        {{-- Signature-like footer --}}
                        <div class="mt-8 pt-6 border-t-2 border-dashed border-primary/20 dark:border-primary-dark/20">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary/20 dark:bg-primary-dark/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary dark:text-primary-dark" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-primary dark:text-primary-dark">عهد موثق</p>
                                        @if ($savedAt)
                                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft">{{ $savedAt->translatedFormat('j F Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" wire:click="edit" 
                                    class="px-5 py-2 rounded-lg bg-primary/10 dark:bg-primary-dark/10 text-primary dark:text-primary-dark text-sm font-medium hover:bg-primary/20 dark:hover:bg-primary-dark/20 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    تعديل التعهد
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom decorative bar --}}
                    <div class="h-1.5 bg-gradient-to-r from-transparent via-primary dark:via-primary-dark to-transparent"></div>
                </div>

                {{-- Inspirational note below --}}
                <div class="mt-6 text-center">
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft italic">
                        "إِنَّ اللَّهَ يُحِبُّ التَّوَّابِينَ وَيُحِبُّ الْمُتَطَهِّرِينَ"
                    </p>
                    <p class="text-xs text-ink-soft/70 dark:text-ink-dark-soft/70 mt-1">سورة البقرة</p>
                </div>
            </div>
        @endif
    </div>
</div>
