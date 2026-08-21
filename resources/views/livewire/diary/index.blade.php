<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">المذكرات</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">مساحتك الخاصة — محمية بقفل الخصوصية.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('diary.reasons') }}" wire:navigate class="px-4 py-2 rounded-lg bg-gradient-to-r from-primary/20 to-secondary/20 dark:from-primary-dark/25 dark:to-secondary-dark/25 text-ink dark:text-ink-dark text-sm font-medium hover:opacity-90">🌳 ليه مبتغيرش؟</a>
                <button type="button" wire:click="$dispatch('create-diary-entry')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ مذكرة</button>
            </div>
        </div>

        {{-- Search + tag filter --}}
        <div class="mb-5">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="ابحث بالعنوان أو الوسم (#عن_الحياة)…" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
        </div>
        @if ($allTags->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <button type="button" wire:click="$set('tag', '')" @class(['px-3 py-1.5 text-sm rounded-full border transition font-medium', 'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === '', 'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft' => $tag !== ''])>الكل</button>
                @foreach ($allTags as $t)
                    @php 
                        $cleanTag = ltrim($t, '#'); 
                        $isActive = $tag !== '' && ltrim(str_replace('_', ' ', $tag), '#') === ltrim(str_replace('_', ' ', $t), '#');
                    @endphp
                    <button type="button" wire:click="$set('tag', @js($cleanTag))" @class(['px-3 py-1.5 text-sm rounded-full border transition font-medium', 'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $isActive, 'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:bg-gray-100 dark:hover:bg-gray-800' => ! $isActive])>#{{ $cleanTag }}</button>
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
                    @php
                        $plainExcerpt = Str::limit(trim(strip_tags($entry->content)), 140);
                    @endphp
                    <div wire:key="entry-{{ $entry->id }}" class="group relative rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 dark:hover:border-primary-dark/20 p-5 transition-all duration-200">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('diary.show', $entry) }}" wire:navigate class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-base text-ink dark:text-ink-dark group-hover:text-primary dark:group-hover:text-primary-dark transition">
                                        {{ $entry->title ?: $entry->date->translatedFormat('l، j M Y') }}
                                    </h3>
                                    @if ($entry->mood)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-300 font-medium">😊 {{ $entry->mood }}/10</span>
                                    @endif
                                </div>
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">
                                    {{ $entry->date->translatedFormat('l، j M Y') }}
                                </p>
                            </a>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="$dispatch('edit-diary-entry', { entry: {{ $entry->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="$dispatch('delete-diary-entry', { entry: {{ $entry->id }} })" wire:confirm="حذف هذه المذكرة؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>

                        {{-- Text Excerpt linking to show view --}}
                        @if (! empty($plainExcerpt))
                            <a href="{{ route('diary.show', $entry) }}" wire:navigate class="block text-sm text-ink-soft dark:text-ink-dark-soft mt-3 leading-relaxed line-clamp-2 hover:text-ink dark:hover:text-ink-dark transition">
                                {{ $plainExcerpt }}
                            </a>
                        @endif

                        {{-- Footer Tags & View Link --}}
                        <div class="flex items-center justify-between gap-2 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800/60">
                            <div class="flex flex-wrap gap-1.5">
                                @if (! empty($entry->tags))
                                    @foreach ($entry->tags as $t)
                                        @php $cleanTag = ltrim($t, '#'); @endphp
                                        <button type="button" wire:click="$set('tag', @js($cleanTag))" class="text-xs px-2.5 py-1 rounded-full bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark font-medium hover:bg-primary/20 transition">#{{ $cleanTag }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <a href="{{ route('diary.show', $entry) }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-semibold text-primary dark:text-primary-dark group-hover:translate-x-[-2px] transition-transform">
                                <span>عرض المذكرة</span>
                                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:diary.manage-entry />
</div>
