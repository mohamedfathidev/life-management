<div class="py-8 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Navigation & Top Bar --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('recovery.mistakes') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-ink-soft hover:text-primary dark:text-ink-dark-soft dark:hover:text-primary-dark transition group">
                <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>أخطاء التعافي</span>
            </a>

            <div class="flex items-center gap-2">
                <button type="button"
                        wire:click="editMistake"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-ink dark:text-ink-dark hover:bg-gray-50 dark:hover:bg-gray-800 transition shadow-sm">
                    <svg class="w-3.5 h-3.5 text-primary dark:text-primary-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تعديل</span>
                </button>

                <button type="button"
                        wire:click="delete"
                        wire:confirm="هل أنت تأكد من حذف هذا الخطأ؟"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-danger hover:bg-red-50 dark:hover:bg-red-950/30 transition shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>حذف الخطأ</span>
                </button>
            </div>
        </div>

        {{-- Main Reading Paper Card --}}
        <article class="rounded-2xl bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 shadow-md overflow-hidden transition-all duration-300">
            <div class="h-1.5 bg-gradient-to-r from-red-500 via-rose-500 to-amber-500"></div>

            <div class="p-6 sm:p-8 md:p-10 space-y-6">
                <header class="space-y-4 border-b border-gray-100 dark:border-gray-800/80 pb-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">{{ $mistake->created_at->translatedFormat('j M Y') }}</span>

                        <span class="text-sm font-extrabold text-danger px-2.5 py-0.5 rounded-full bg-danger/10 border border-danger/20">
                            ⛓️ {{ $mistake->weight }}%
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-ink dark:text-ink-dark leading-snug tracking-tight">
                        {{ $mistake->title }}
                    </h1>

                    <div class="h-2.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-rose-500 to-danger transition-all duration-300" style="width: {{ $mistake->weight }}%"></div>
                    </div>
                </header>

                <div class="prose dark:prose-invert max-w-none">
                    @if ($mistake->note)
                        <div class="trix-content text-base sm:text-lg text-ink/90 dark:text-ink-dark/90 leading-relaxed sm:leading-loose tracking-wide space-y-4">
                            {!! $mistake->note !!}
                        </div>
                    @else
                        <p class="text-ink-soft dark:text-ink-dark-soft italic text-center py-10">لا توجد ملاحظات مكتوبة بعد لهذا الخطأ.</p>
                    @endif
                </div>
            </div>

            <footer class="px-6 sm:px-10 py-4 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-800/60 flex items-center justify-between text-xs text-ink-soft dark:text-ink-dark-soft">
                <span>تاريخ الإضافة: {{ $mistake->created_at->translatedFormat('j M Y') }}</span>
                @if ($mistake->updated_at && $mistake->updated_at->ne($mistake->created_at))
                    <span>آخر تعديل: {{ $mistake->updated_at->diffForHumans() }}</span>
                @endif
            </footer>
        </article>
    </div>

    <livewire:recovery.manage-mistake />
</div>
