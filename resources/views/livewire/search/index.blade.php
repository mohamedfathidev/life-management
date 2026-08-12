<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mb-4">بحث</h1>

        <div class="relative mb-6">
            <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-ink-soft dark:text-ink-dark-soft">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            </span>
            <input type="text" wire:model.live.debounce.350ms="q" autofocus
                   placeholder="ابحث في كل حاجة — أهداف، مواعيد، مذكرات، وظائف…"
                   class="block w-full ps-11 pe-4 py-3 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
        </div>

        @if (mb_strlen($q) < 2)
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-12">اكتب حرفين على الأقل للبحث عبر كل الأقسام.</p>
        @elseif ($count === 0)
            <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش نتائج لـ «{{ $q }}».</p>
            </div>
        @else
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-4">{{ $count }} نتيجة</p>
            @foreach ($grouped as $section => $items)
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-2">{{ $items->first()['emoji'] }} {{ $section }}</h2>
                    <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm divide-y divide-ink-soft/10 dark:divide-ink-dark-soft/10">
                        @foreach ($items as $item)
                            <a href="{{ $item['url'] }}" wire:navigate wire:key="res-{{ $section }}-{{ $loop->index }}" class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-primary/5 dark:hover:bg-primary-dark/10 transition">
                                <span class="text-sm text-ink dark:text-ink-dark truncate">{{ $item['title'] }}</span>
                                @if (! empty($item['subtitle']))
                                    <span class="text-xs text-ink-soft dark:text-ink-dark-soft shrink-0">{{ $item['subtitle'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
