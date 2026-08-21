<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">⚠️ أضرار الإدمان</h1>
            </div>
            <button type="button" wire:click="$dispatch('create-damage')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ ضرر جديد</button>
        </div>

        <p class="text-sm text-ink-soft dark:text-ink-dark-soft -mt-3">
            كل دايرة ضرر بيسببه الإدمان — كل ما الدرجة زادت، الدايرة بتقرّب للأحمر. اضغط على أي دايرة تشوف تفاصيلها وأضرارها الفرعية.
        </p>

        @if ($damages->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-5xl mb-3">🎯</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">ابدأ بإضافة أول ضرر سبّبه الإدمان في حياتك.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10 justify-items-center pt-4">
                @foreach ($damages as $damage)
                    @php($hue = $damage->hue())
                    <div wire:key="dmg-{{ $damage->id }}" class="group relative flex flex-col items-center">
                        {{-- Edit / delete quick actions --}}
                        <div class="absolute -top-2 -end-1 z-10 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" wire:click="$dispatch('edit-damage', { damage: {{ $damage->id }} })" class="w-7 h-7 rounded-full bg-surface-light dark:bg-surface-dark shadow border border-gray-200 dark:border-gray-700 text-xs text-primary dark:text-primary-dark flex items-center justify-center" title="تعديل">✎</button>
                            <button type="button" wire:click="$dispatch('delete-damage', { damage: {{ $damage->id }} })" wire:confirm="حذف هذا الضرر وكل أضراره الفرعية؟" class="w-7 h-7 rounded-full bg-surface-light dark:bg-surface-dark shadow border border-gray-200 dark:border-gray-700 text-xs text-danger flex items-center justify-center" title="حذف">✕</button>
                        </div>

                        {{-- The circle --}}
                        <a href="{{ route('recovery.damages.show', $damage) }}" wire:navigate
                           class="relative rounded-full p-[6px] shadow-lg transition-transform duration-300 group-hover:scale-105 group-hover:shadow-xl"
                           style="background: conic-gradient(hsl({{ $hue }}, 72%, 46%) calc({{ $damage->degree }} * 1%), hsl({{ $hue }}, 25%, 88%) 0);">
                            <span class="absolute inset-0 rounded-full ring-2 ring-white/40 dark:ring-black/20 pointer-events-none"></span>
                            <span class="flex flex-col items-center justify-center w-36 h-36 sm:w-40 sm:h-40 rounded-full bg-surface-light dark:bg-surface-dark text-center px-3">
                                @if ($damage->icon)<span class="text-2xl mb-1">{{ $damage->icon }}</span>@endif
                                <span class="font-bold text-sm sm:text-base text-ink dark:text-ink-dark leading-snug line-clamp-2">{{ $damage->title }}</span>
                                <span class="mt-1 text-2xl font-extrabold" style="color: hsl({{ $hue }}, 70%, 38%);">{{ $damage->degree }}%</span>
                                <span class="text-[10px] text-ink-soft dark:text-ink-dark-soft">درجة الضرر</span>
                            </span>
                        </a>

                        {{-- Sub-damage mini circles --}}
                        @if ($damage->children->isNotEmpty())
                            <div class="mt-4 flex items-start justify-center gap-2 flex-wrap max-w-[240px]">
                                @foreach ($damage->children as $sub)
                                    @php($subHue = $sub->hue())
                                    <a href="{{ route('recovery.damages.show', $sub) }}" wire:navigate
                                       title="{{ $sub->title }} — {{ $sub->degree }}%"
                                       class="rounded-full p-[3px] transition-transform hover:scale-110"
                                       style="background: conic-gradient(hsl({{ $subHue }}, 72%, 46%) calc({{ $sub->degree }} * 1%), hsl({{ $subHue }}, 25%, 88%) 0);">
                                        <span class="flex items-center justify-center w-14 h-14 rounded-full bg-surface-light dark:bg-surface-dark text-center leading-none">
                                            <span class="text-[9px] font-semibold text-ink dark:text-ink-dark line-clamp-2 px-1">{{ $sub->title }}</span>
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($damages->hasPages())
                <div class="mt-10">{{ $damages->links() }}</div>
            @endif
        @endif
    </div>

    <livewire:recovery.manage-damage />
</div>