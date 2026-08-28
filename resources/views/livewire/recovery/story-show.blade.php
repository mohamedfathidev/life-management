<div class="py-8 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Top Navigation & Action Header --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('recovery.stories') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-ink-soft hover:text-primary dark:text-ink-dark-soft dark:hover:text-primary-dark transition group">
                <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>حكايات التعافي</span>
            </a>

            <div class="flex items-center gap-2">
                <button type="button"
                        wire:click="editStory"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-ink dark:text-ink-dark hover:bg-gray-50 dark:hover:bg-gray-800 transition shadow-sm">
                    <svg class="w-3.5 h-3.5 text-primary dark:text-primary-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تعديل</span>
                </button>

                <button type="button"
                        wire:click="deleteStory"
                        wire:confirm="هل أنت متأكد من حذف هذه الحكاية؟"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-danger hover:bg-red-50 dark:hover:bg-red-950/30 transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>حذف</span>
                </button>
            </div>
        </div>

        {{-- Main Reading Card --}}
        <article class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 shadow-md overflow-hidden transition-all duration-300">
            <div class="h-1.5 bg-gradient-to-r from-teal-500 via-emerald-500 to-primary"></div>

            <div class="p-6 sm:p-8 md:p-10 space-y-6">
                <header class="space-y-4 border-b border-gray-100 dark:border-gray-800/80 pb-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        @if ($story->recovery)
                            <a href="{{ route('recovery.show', $story->recovery) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark hover:bg-primary/20 transition">
                                في {{ $story->recovery->title }}
                            </a>
                        @else
                            <span class="text-xs text-ink-soft dark:text-ink-dark-soft">حكاية من رحلة التعافي</span>
                        @endif

                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">
                            {{ $story->date->translatedFormat('l، j M Y') }}
                            @if ($story->mood)
                                <span class="ms-2 px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-300 font-medium">😊 {{ $story->mood }}/10</span>
                            @endif
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-ink dark:text-ink-dark leading-snug tracking-tight">
                        {{ $story->title ?: $story->date->translatedFormat('l، j M Y') }}
                    </h1>

                    @if ($story->brief)
                        <p class="text-sm sm:text-base text-ink-soft dark:text-ink-dark-soft italic leading-relaxed">
                            {{ $story->brief }}
                        </p>
                    @endif

                    @if (! empty($story->tags))
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach ($story->tags as $t)
                                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark">#{{ ltrim($t, '#') }}</span>
                            @endforeach
                        </div>
                    @endif
                </header>

                <div class="prose prose-base sm:prose-lg dark:prose-invert max-w-none">
                    @if ($story->content)
                        {!! $story->content !!}
                    @else
                        <p class="text-ink-soft dark:text-ink-dark-soft italic text-center py-10">لا يوجد نص لهذه الحكاية.</p>
                    @endif
                </div>
            </div>

            <footer class="px-6 sm:px-10 py-4 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800/60 flex items-center justify-between text-xs text-ink-soft dark:text-ink-dark-soft">
                <span>تاريخ الإنشاء: {{ $story->created_at->translatedFormat('j M Y - g:i a') }}</span>
                @if ($story->updated_at && $story->updated_at->ne($story->created_at))
                    <span>آخر تعديل: {{ $story->updated_at->diffForHumans() }}</span>
                @endif
            </footer>
        </article>
    </div>

    <livewire:recovery.manage-story />
</div>