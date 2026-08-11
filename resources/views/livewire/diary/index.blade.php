<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">المذكرات</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">مساحتك الخاصة — محمية بقفل الخصوصية.</p>
            </div>
            <button type="button" wire:click="$dispatch('create-diary-entry')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ مذكرة</button>
        </div>

        {{-- Search + tag filter --}}
        <div class="mb-5">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="ابحث بالعنوان…" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
        </div>
        @if ($allTags->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <button type="button" wire:click="$set('tag', '')" @class(['px-3 py-1.5 text-sm rounded-full border transition', 'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === '', 'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft' => $tag !== ''])>الكل</button>
                @foreach ($allTags as $t)
                    <button type="button" wire:click="$set('tag', @js($t))" @class(['px-3 py-1.5 text-sm rounded-full border transition', 'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === $t, 'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft' => $tag !== $t])>#{{ $t }}</button>
                @endforeach
            </div>
        @endif

        @if ($entries->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش مذكرات هنا. ابدأ بأول واحدة.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($entries as $entry)
                    <div wire:key="entry-{{ $entry->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $entry->title ?: $entry->date->translatedFormat('l، j M Y') }}</h3>
                                    @if ($entry->mood)<span class="text-xs text-ink-soft dark:text-ink-dark-soft">😊 {{ $entry->mood }}/10</span>@endif
                                </div>
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $entry->date->translatedFormat('l، j M Y') }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="$dispatch('edit-diary-entry', { entry: {{ $entry->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="$dispatch('delete-diary-entry', { entry: {{ $entry->id }} })" wire:confirm="حذف هذه المذكرة؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                        @if ($entry->content)
                            <div class="trix-content text-sm text-ink-soft dark:text-ink-dark-soft mt-3 leading-7">{!! $entry->content !!}</div>
                        @endif
                        @if (! empty($entry->tags))
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach ($entry->tags as $t)
                                    <button type="button" wire:click="$set('tag', @js($t))" class="text-xs px-2 py-0.5 rounded-full bg-secondary/25 text-ink dark:text-ink-dark hover:opacity-80">#{{ $t }}</button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:diary.manage-entry />
</div>
