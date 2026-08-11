<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">تعلّم عن التعافي والإدمان</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">مواضيع وملاحظات تتعلّم منها، منظّمة بالوسوم.</p>
            </div>
            <button type="button" wire:click="$dispatch('create-topic')" class="inline-flex items-center gap-2 rounded-lg bg-primary dark:bg-primary-dark px-4 py-2 text-white text-sm font-medium shadow-sm hover:opacity-90 transition">
                + موضوع
            </button>
        </div>

        {{-- Tag filter --}}
        @if ($allTags->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <button type="button" wire:click="$set('tag', '')"
                    @class([
                        'px-3 py-1.5 text-sm rounded-full border transition',
                        'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === '',
                        'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $tag !== '',
                    ])>الكل</button>
                @foreach ($allTags as $t)
                    <button type="button" wire:click="$set('tag', @js($t))"
                        @class([
                            'px-3 py-1.5 text-sm rounded-full border transition',
                            'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === $t,
                            'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $tag !== $t,
                        ])>#{{ $t }}</button>
                @endforeach
            </div>
        @endif

        @if ($topics->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش مواضيع لسه. سجّل أول درس اتعلمته.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($topics as $topic)
                    <div wire:key="topic-{{ $topic->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $topic->title }}</h3>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $topic->importance->color() }}/15 text-{{ $topic->importance->color() }}">أهمية {{ $topic->importance->label() }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="$dispatch('edit-topic', { topic: {{ $topic->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="$dispatch('delete-topic', { topic: {{ $topic->id }} })" wire:confirm="حذف هذا الموضوع؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                        @if ($topic->content)
                            <div class="trix-content text-sm text-ink-soft dark:text-ink-dark-soft mt-2">{!! $topic->content !!}</div>
                        @endif
                        @if (! empty($topic->tags))
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach ($topic->tags as $t)
                                    <button type="button" wire:click="$set('tag', @js($t))" class="text-xs px-2 py-0.5 rounded-full bg-secondary/25 text-ink dark:text-ink-dark hover:opacity-80">#{{ $t }}</button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:recovery.manage-topic />
</div>
