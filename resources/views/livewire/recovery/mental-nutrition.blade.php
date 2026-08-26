<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">التغذية الذهنية</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">كل يوم حاجة من رحلتك تتعمّق فيها، عشان دماغك تفضل واعية بالخطر ومتذكّرة ليه بدأت.</p>
            </div>
            <div class="text-center rounded-xl bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent px-5 py-3">
                <p class="text-3xl font-bold text-success">{{ $streak }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft">يوم متتالي</p>
            </div>
        </div>

        @if (! $hasItems)
            <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft mb-3">محتاج تكتب حاجة في تابات التعافي الأول (تعلّم، أضرار الإدمان، أحلام، تغييرات...) عشان نغذّي بيها دماغك كل يوم.</p>
                <a href="{{ route('recovery.topics') }}" wire:navigate class="text-primary dark:text-primary-dark hover:underline">اذهب إلى «تعلّم» ←</a>
            </div>
        @elseif ($todayLog)
            {{-- Already consumed today --}}
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center gap-2 text-success mb-3">
                    <span class="text-lg">✓</span>
                    <span class="text-sm font-medium">تمّت تغذية اليوم</span>
                </div>
                @if ($todayItem)
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-secondary/20 text-ink dark:text-ink-dark">{{ $todayItem->type->emoji() }} {{ $todayItem->type->label() }}</span>
                    </div>
                    <a href="{{ $todayItem->url }}" wire:navigate class="font-semibold text-ink dark:text-ink-dark hover:underline">{{ $todayItem->title }}</a>
                    @if ($todayItem->body)
                        @if ($todayItem->isHtml)
                            <div class="trix-content text-sm text-ink-soft dark:text-ink-dark-soft mt-2">{!! $todayItem->body !!}</div>
                        @else
                            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2 whitespace-pre-line">{{ $todayItem->body }}</p>
                        @endif
                    @endif
                @else
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft">(المحتوى اتحذف)</p>
                @endif
                @if ($todayLog->reflection)
                    <div class="mt-4 rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-1">انعكاسك</p>
                        <p class="text-sm text-ink dark:text-ink-dark whitespace-pre-line">{{ $todayLog->reflection }}</p>
                    </div>
                @endif
            </div>
        @elseif ($suggested)
            {{-- Today's suggested item --}}
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center justify-between gap-3 mb-1">
                    <span class="text-xs px-2 py-0.5 rounded-full bg-secondary/20 text-ink dark:text-ink-dark">{{ $suggested->type->emoji() }} {{ $suggested->type->label() }}</span>
                    <button type="button" wire:click="shuffleSuggestion" class="text-xs text-primary dark:text-primary-dark hover:underline">حاجة تانية ↻</button>
                </div>
                <div class="flex items-center gap-2 flex-wrap mt-2">
                    <h3 class="font-semibold text-ink dark:text-ink-dark text-lg">{{ $suggested->title }}</h3>
                </div>
                @if ($suggested->body)
                    @if ($suggested->isHtml)
                        <div class="trix-content text-sm text-ink-soft dark:text-ink-dark-soft mt-3">{!! $suggested->body !!}</div>
                    @else
                        <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-3 whitespace-pre-line">{{ $suggested->body }}</p>
                    @endif
                @endif

                <div class="mt-5">
                    <x-input-label for="reflection" value="انعكاس / إيه اللي وصلك؟ (اختياري)" />
                    <textarea id="reflection" wire:model="reflection" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>

                <button type="button" wire:click="markConsumed('{{ $suggested->type->value }}', {{ $suggested->id }})" class="mt-4 px-4 py-2 rounded-lg bg-success text-white text-sm font-medium hover:opacity-90 transition">
                    تمّت القراءة اليوم ✓
                </button>
            </div>
        @endif

        {{-- Recent history --}}
        @if ($recent->isNotEmpty())
            <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-3">آخر الأيام</h3>
                @foreach ($recent as $entry)
                    <div wire:key="mn-{{ $entry['log']->id }}" class="flex items-center justify-between py-2 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0 text-sm">
                        <span class="text-ink dark:text-ink-dark flex items-center gap-1.5">
                            @if ($entry['item'])
                                <span class="text-xs">{{ $entry['item']->type->emoji() }}</span>
                                {{ $entry['item']->title }}
                            @else
                                —
                            @endif
                        </span>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">{{ $entry['log']->date->translatedFormat('j M') }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
