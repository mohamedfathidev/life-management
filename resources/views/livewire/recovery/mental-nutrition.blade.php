<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">التغذية الذهنية</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">كل يوم موضوع تتعمّق فيه، عشان دماغك تفضل واعية بالخطر.</p>
            </div>
            <div class="text-center rounded-xl bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent px-5 py-3">
                <p class="text-3xl font-bold text-success">{{ $streak }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft">يوم متتالي</p>
            </div>
        </div>

        @if (! $hasTopics)
            <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft mb-3">محتاج تكتب مواضيع الأول عشان نغذّي بيها دماغك كل يوم.</p>
                <a href="{{ route('recovery.topics') }}" wire:navigate class="text-primary dark:text-primary-dark hover:underline">اذهب إلى «تعلّم» ←</a>
            </div>
        @elseif ($todayLog)
            {{-- Already consumed today --}}
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center gap-2 text-success mb-3">
                    <span class="text-lg">✓</span>
                    <span class="text-sm font-medium">تمّت تغذية اليوم</span>
                </div>
                @if ($todayLog->topic)
                    <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $todayLog->topic->title }}</h3>
                    @if ($todayLog->topic->content)
                        <div class="trix-content text-sm text-ink-soft dark:text-ink-dark-soft mt-2">{!! $todayLog->topic->content !!}</div>
                    @endif
                @else
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft">(الموضوع اتحذف)</p>
                @endif
                @if ($todayLog->reflection)
                    <div class="mt-4 rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-1">انعكاسك</p>
                        <p class="text-sm text-ink dark:text-ink-dark whitespace-pre-line">{{ $todayLog->reflection }}</p>
                    </div>
                @endif
            </div>
        @elseif ($suggested)
            {{-- Today's suggested topic --}}
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center justify-between gap-3 mb-1">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">موضوع اليوم</p>
                    <button type="button" wire:click="shuffleSuggestion" class="text-xs text-primary dark:text-primary-dark hover:underline">موضوع تاني ↻</button>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="font-semibold text-ink dark:text-ink-dark text-lg">{{ $suggested->title }}</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $suggested->importance->color() }}/15 text-{{ $suggested->importance->color() }}">أهمية {{ $suggested->importance->label() }}</span>
                </div>
                @if ($suggested->content)
                    <div class="trix-content text-sm text-ink-soft dark:text-ink-dark-soft mt-3">{!! $suggested->content !!}</div>
                @endif

                <div class="mt-5">
                    <x-input-label for="reflection" value="انعكاس / إيه اللي وصلك؟ (اختياري)" />
                    <textarea id="reflection" wire:model="reflection" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>

                <button type="button" wire:click="markConsumed({{ $suggested->id }})" class="mt-4 px-4 py-2 rounded-lg bg-success text-white text-sm font-medium hover:opacity-90 transition">
                    تمّت القراءة اليوم ✓
                </button>
            </div>
        @endif

        {{-- Recent history --}}
        @if ($recent->isNotEmpty())
            <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-3">آخر الأيام</h3>
                @foreach ($recent as $log)
                    <div wire:key="mn-{{ $log->id }}" class="flex items-center justify-between py-2 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0 text-sm">
                        <span class="text-ink dark:text-ink-dark">{{ $log->topic?->title ?? '—' }}</span>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">{{ $log->date->translatedFormat('j M') }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
