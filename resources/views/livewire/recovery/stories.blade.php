<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">📖 حكايات التعافي</h1>
            </div>
            <button type="button" wire:click="createStory()" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ حكاية جديدة</button>
        </div>

        <p class="text-sm text-ink-soft dark:text-ink-dark-soft -mt-3">يومياتك الخاصة في رحلة التعافي — محمية بقفل الخصوصية ومشفّرة.</p>

        {{-- Filters: text search + recovery period --}}
        <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="ابحث في العناوين أو النصوص…" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
            <select wire:model.live="recoveryId" class="block w-full sm:w-auto rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                <option value="">كل فترات التعافي</option>
                @foreach ($recoveries as $r)
                    <option value="{{ $r->id }}">{{ $r->title }}</option>
                @endforeach
            </select>
        </div>

        @if ($stories->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🕊️</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش حكايات هنا بعد. اكتبي أول حكاية من رحلتك.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($stories as $story)
                    @php
                        $plainExcerpt = $story->brief ?: \Illuminate\Support\Str::limit(trim(strip_tags($story->content)), 160);
                    @endphp
                    <div wire:key="story-{{ $story->id }}" @class([
                        'group relative rounded-xl shadow-sm hover:shadow-md p-5 transition-all duration-200 border',
                        'bg-amber-500/5 dark:bg-amber-400/10 border-amber-500/30 dark:border-amber-400/30' => $story->is_featured,
                        'bg-surface-light dark:bg-surface-dark border-transparent hover:border-primary/20 dark:hover:border-primary-dark/20' => ! $story->is_featured,
                    ])>
                        @if ($story->is_featured)
                            <span class="absolute -top-2 -end-2 text-xs px-2 py-0.5 rounded-full bg-amber-500 text-white font-semibold shadow-sm">⭐ مميزة</span>
                        @endif

                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('recovery.stories.show', $story) }}" wire:navigate class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-base text-ink dark:text-ink-dark group-hover:text-primary dark:group-hover:text-primary-dark transition">
                                        {{ $story->title ?: $story->date->translatedFormat('l، j M Y') }}
                                    </h3>
                                    @if ($story->mood)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-300 font-medium">😊 {{ $story->mood }}/10</span>
                                    @endif
                                </div>
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">
                                    {{ $story->date->translatedFormat('l، j M Y') }}
                                    @if ($story->recovery)
                                        <span class="inline-block ms-1 px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark font-medium">في {{ $story->recovery->title }}</span>
                                    @endif
                                </p>
                            </a>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="toggleFeatured({{ $story->id }})" title="{{ $story->is_featured ? 'إلغاء التمييز' : 'تمييز الحكاية' }}" class="text-sm {{ $story->is_featured ? 'text-amber-500' : 'text-ink-soft dark:text-ink-dark-soft hover:text-amber-500' }} transition">{{ $story->is_featured ? '⭐' : '☆' }}</button>
                                <button type="button" wire:click="$dispatch('edit-story', { story: {{ $story->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="$dispatch('delete-story', { story: {{ $story->id }} })" wire:confirm="حذف هذه الحكاية؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>

                        @if (! empty($plainExcerpt))
                            <a href="{{ route('recovery.stories.show', $story) }}" wire:navigate class="block text-sm text-ink-soft dark:text-ink-dark-soft mt-3 leading-relaxed line-clamp-2 hover:text-ink dark:hover:text-ink-dark transition">
                                {{ $plainExcerpt }}
                            </a>
                        @endif

                        @if (! empty($story->tags))
                            <div class="flex flex-wrap gap-1.5 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800/60">
                                @foreach ($story->tags as $t)
                                    <span class="text-xs px-2.5 py-1 rounded-full bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark font-medium">#{{ ltrim($t, '#') }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($stories->hasPages())
                <div class="pt-2">{{ $stories->links() }}</div>
            @endif
        @endif
    </div>

    <livewire:recovery.manage-story />
</div>