<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('scholarships.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← المنح</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">تعلّم عن المنح</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">خطاب التحفيز، التوصية، وكل ما يخص التقديم — منظّم بالوسوم.</p>
            </div>
            <button type="button" wire:click="$dispatch('create-scholarship-topic')" class="inline-flex items-center gap-2 rounded-lg bg-primary dark:bg-primary-dark px-4 py-2 text-white text-sm font-medium shadow-sm hover:opacity-90 transition">
                + موضوع
            </button>
        </div>

        @if ($allTags->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <button type="button" wire:click="$set('tag', '')"
                    @class(['px-3 py-1.5 text-sm rounded-full border transition', 'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === '', 'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $tag !== ''])>الكل</button>
                @foreach ($allTags as $t)
                    <button type="button" wire:click="$set('tag', @js($t))"
                        @class(['px-3 py-1.5 text-sm rounded-full border transition', 'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === $t, 'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $tag !== $t])>#{{ $t }}</button>
                @endforeach
            </div>
        @endif

        @if ($topics->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش مواضيع لسه. سجّل أول ملاحظة عن المنح.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($topics as $topic)
                    <div wire:key="sctopic-{{ $topic->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $topic->title }}</h3>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="$dispatch('edit-scholarship-topic', { topic: {{ $topic->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="$dispatch('delete-scholarship-topic', { topic: {{ $topic->id }} })" wire:confirm="حذف هذا الموضوع؟" class="text-xs text-danger hover:underline">حذف</button>
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

    <livewire:scholarships.manage-topic />
</div>
