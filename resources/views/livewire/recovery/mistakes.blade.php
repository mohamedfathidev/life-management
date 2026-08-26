<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>

        {{-- Case file header --}}
        <div class="relative overflow-hidden rounded-3xl bg-ink dark:bg-black text-white p-6 sm:p-8 mt-3 mb-6">
            <div class="absolute -top-12 -end-12 w-48 h-48 rounded-full bg-danger/20 blur-3xl"></div>
            <div class="absolute -bottom-16 -start-16 w-56 h-56 rounded-full bg-danger/10 blur-3xl"></div>

            <div class="relative flex items-start justify-between gap-4 flex-wrap">
                <div class="min-w-0">
                    <span class="font-mono text-[10px] tracking-widest text-white/40">CASE FILE</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold mt-1 flex items-center gap-2">⛓️ أخطاء التعافي</h1>
                    <p class="text-sm text-white/60 mt-3 max-w-md leading-relaxed">الأخطاء اللي بتبقيك في السجن — حدّد لكل خطأ نسبة قد إيه بيأثّر، وواجه الأكبر الأول.</p>
                </div>
                @if ($mistakes->isNotEmpty())
                    <div class="text-end shrink-0">
                        <p class="text-[10px] font-mono tracking-widest text-white/40">أكبر عدو دلوقتي</p>
                        <p class="text-base font-bold text-danger mt-1 max-w-[10rem] line-clamp-2">{{ $mistakes->first()->title }}</p>
                        <p class="text-xs text-white/50 mt-0.5 font-mono">{{ $mistakes->first()->weight }}%</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Add / edit form --}}
        <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 space-y-5 mb-8 border-t-4 border-danger">
            <div>
                <x-input-label for="m_title" :value="$editingId ? 'تعديل الخطأ' : 'سجّل خطأ جديد'" />
                <x-text-input id="m_title" wire:model="title" type="text" class="mt-1 block w-full" placeholder="مثال: السهر لوحدي على الموبايل بالليل" />
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div x-data="{ w: @entangle('weight') }">
                <div class="flex justify-between items-center">
                    <x-input-label value="قد إيه بيبقيك في السجن؟" />
                    <span class="text-lg font-mono font-extrabold" :class="w >= 70 ? 'text-danger' : (w >= 40 ? 'text-warning' : 'text-ink-soft dark:text-ink-dark-soft')" x-text="w + '%'"></span>
                </div>
                <input type="range" min="0" max="100" step="5" x-model.number="w" class="mt-3 block w-full accent-danger" dir="ltr" />
                <div class="flex justify-between text-[10px] font-mono tracking-wider text-ink-soft dark:text-ink-dark-soft mt-1">
                    <span>بسيط</span>
                    <span>بيسجنك تمامًا</span>
                </div>
                <x-input-error :messages="$errors->get('weight')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-1">
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                @endif
                <button type="submit" class="px-5 py-2 rounded-lg bg-danger text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة للملف' }}</button>
            </div>
        </form>

        {{-- Ranked list --}}
        @if ($mistakes->isEmpty())
            <div class="text-center py-16 rounded-2xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">⛓️</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">اكتب أول خطأ بيعطّل تعافيك.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($mistakes as $i => $mistake)
                    @php($rank = $mistakes->firstItem() + $i)
                    <div wire:key="mistake-{{ $mistake->id }}" class="group relative overflow-hidden rounded-2xl bg-ink dark:bg-black text-white shadow-sm hover:shadow-lg transition p-5">
                        @if ($mistake->weight >= 70)
                            <div class="absolute inset-y-0 start-0 w-1.5 bg-danger"></div>
                        @endif

                        <div class="flex items-start gap-4">
                            <span class="font-mono text-2xl font-black text-white/15 shrink-0 leading-none mt-0.5">{{ str_pad((string) $rank, 2, '0', STR_PAD_LEFT) }}</span>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <a href="{{ route('recovery.mistakes.show', $mistake) }}" wire:navigate class="min-w-0 flex-1">
                                        <h3 class="text-base font-bold group-hover:text-danger transition">{{ $mistake->title }}</h3>
                                    </a>
                                    <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition">
                                        <a href="{{ route('recovery.mistakes.show', $mistake) }}" wire:navigate class="text-xs font-semibold text-primary-dark hover:underline">التفاصيل</a>
                                        <button type="button" wire:click="edit({{ $mistake->id }})" class="text-xs text-white/60 hover:underline">تعديل السريع</button>
                                        <button type="button" wire:click="delete({{ $mistake->id }})" wire:confirm="حذف الخطأ؟" class="text-xs text-danger hover:underline">حذف</button>
                                    </div>
                                </div>

                                <a href="{{ route('recovery.mistakes.show', $mistake) }}" wire:navigate class="mt-3 flex items-center gap-3 block">
                                    <div class="flex-1 h-2.5 rounded-full bg-white/10 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-rose-500 to-danger transition-all duration-300" style="width: {{ $mistake->weight }}%"></div>
                                    </div>
                                    <span class="text-sm font-mono font-extrabold text-danger w-12 text-start shrink-0">{{ $mistake->weight }}%</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($mistakes->hasPages())
                <div class="mt-6">{{ $mistakes->links() }}</div>
            @endif
        @endif
    </div>
</div>
