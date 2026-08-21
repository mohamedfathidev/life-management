<div class="py-8 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Top navigation --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('recovery.damages') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-ink-soft hover:text-primary dark:text-ink-dark-soft dark:hover:text-primary-dark transition group">
                <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>أضرار الإدمان</span>
            </a>

            <div class="flex items-center gap-2">
                <button type="button"
                        wire:click="$dispatch('edit-damage', { damage: {{ $damage->id }} })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-ink dark:text-ink-dark hover:bg-gray-50 dark:hover:bg-gray-800 transition shadow-sm">✎ تعديل</button>
                <button type="button"
                        wire:click="$dispatch('delete-damage', { damage: {{ $damage->id }} })"
                        wire:confirm="حذف هذا الضرر وكل أضراره الفرعية؟"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-danger hover:bg-red-50 dark:hover:bg-red-950/30 transition shadow-sm">✕ حذف</button>
            </div>
        </div>

        @php($hue = $damage->hue())

        {{-- Main card --}}
        <article class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 shadow-md overflow-hidden">
            <div class="h-1.5" style="background: linear-gradient(to left, hsl({{ $hue }}, 72%, 46%), hsl({{ $hue }}, 45%, 70%));"></div>

            <div class="p-6 sm:p-10 flex flex-col items-center text-center space-y-5">
                {{-- Big circle --}}
                <div class="rounded-full p-[8px] shadow-xl" style="background: conic-gradient(hsl({{ $hue }}, 72%, 46%) calc({{ $damage->degree }} * 1%), hsl({{ $hue }}, 25%, 88%) 0);">
                    <span class="flex flex-col items-center justify-center w-44 h-44 sm:w-52 sm:h-52 rounded-full bg-surface-light dark:bg-surface-dark">
                        @if ($damage->icon)<span class="text-4xl mb-2">{{ $damage->icon }}</span>@endif
                        <span class="text-5xl font-extrabold" style="color: hsl({{ $hue }}, 70%, 38%);">{{ $damage->degree }}%</span>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">درجة الضرر</span>
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-ink dark:text-ink-dark leading-snug">
                    @if ($damage->icon)<span class="ms-1">{{ $damage->icon }}</span>@endif
                    {{ $damage->title }}
                </h1>

                @if ($damage->parent)
                    <a href="{{ route('recovery.damages.show', $damage->parent) }}" wire:navigate
                       class="text-xs px-3 py-1 rounded-full bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark font-semibold hover:bg-primary/20 transition">
                        ↑ ضرر فرعي تحت: {{ $damage->parent->title }}
                    </a>
                @endif

                @if ($damage->description)
                    <div class="trix-content text-base sm:text-lg text-ink/90 dark:text-ink-dark/90 leading-relaxed sm:leading-loose w-full text-start">{!! $damage->description !!}</div>
                @endif
            </div>
        </article>

        {{-- Life without this damage — bullets --}}
        @if (! empty($damage->life_without))
            <section class="rounded-2xl bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-bold text-ink dark:text-ink-dark mb-4 flex items-center gap-2">🌱 لو الضرر ده مش موجود…</h2>
                <ul class="space-y-3">
                    @foreach ($damage->life_without as $bullet)
                        <li class="flex items-start gap-3 text-sm sm:text-base text-ink dark:text-ink-dark leading-relaxed">
                            <span class="shrink-0 mt-0.5 w-6 h-6 rounded-full bg-success/20 text-success flex items-center justify-center text-xs font-bold">✓</span>
                            <span>{{ $bullet }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Sub-damages --}}
        @if ($damage->children->isNotEmpty())
            <section class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
                    <h2 class="text-lg font-bold text-ink dark:text-ink-dark">🧩 الأضرار الفرعية ({{ $damage->children->count() }})</h2>
                    <button type="button" wire:click="$dispatch('create-damage', { parentId: {{ $damage->id }} })" class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark text-xs font-semibold hover:bg-primary/20 transition">+ ضرر فرعي</button>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-x-4 gap-y-8 justify-items-center">
                    @foreach ($damage->children as $sub)
                        @php($subHue = $sub->hue())
                        <a href="{{ route('recovery.damages.show', $sub) }}" wire:navigate
                           class="group flex flex-col items-center gap-2 transition-transform hover:scale-105">
                            <span class="rounded-full p-[4px] shadow-md" style="background: conic-gradient(hsl({{ $subHue }}, 72%, 46%) calc({{ $sub->degree }} * 1%), hsl({{ $subHue }}, 25%, 88%) 0);">
                                <span class="flex flex-col items-center justify-center w-24 h-24 rounded-full bg-surface-light dark:bg-surface-dark text-center px-1.5">
                                    @if ($sub->icon)<span class="text-lg">{{ $sub->icon }}</span>@endif
                                    <span class="text-[11px] font-bold text-ink dark:text-ink-dark line-clamp-2 leading-tight">{{ $sub->title }}</span>
                                    <span class="text-sm font-extrabold mt-0.5" style="color: hsl({{ $subHue }}, 70%, 38%);">{{ $sub->degree }}%</span>
                                </span>
                            </span>
                            <span class="text-[10px] text-ink-soft dark:text-ink-dark-soft group-hover:text-primary dark:group-hover:text-primary-dark transition">التفاصيل ←</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @elseif (! $damage->parent_id)
            <div class="text-center py-8 rounded-2xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft text-sm mb-3">مفيش أضرار فرعية لسه.</p>
                <button type="button" wire:click="$dispatch('create-damage', { parentId: {{ $damage->id }} })" class="px-4 py-2 rounded-lg bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark text-sm font-semibold hover:bg-primary/20 transition">+ إضافة ضرر فرعي</button>
            </div>
        @endif
    </div>

    <livewire:recovery.manage-damage />
</div>