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

        {{-- Search input --}}
        <div class="mb-5">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="ابحث بالعنوان أو الوسم (#السهر)…" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
        </div>

        {{-- Tag filter --}}
        @if ($allTags->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <button type="button" wire:click="$set('tag', '')"
                    @class([
                        'px-3 py-1.5 text-sm rounded-full border transition font-medium',
                        'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === '',
                        'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:bg-gray-100 dark:hover:bg-gray-800' => $tag !== '',
                    ])>الكل</button>
                @foreach ($allTags as $t)
                    @php 
                        $cleanTag = ltrim($t, '#'); 
                        $isActive = $tag !== '' && ltrim(str_replace('_', ' ', $tag), '#') === ltrim(str_replace('_', ' ', $t), '#');
                    @endphp
                    <button type="button" wire:click="$set('tag', @js($cleanTag))"
                        @class([
                            'px-3 py-1.5 text-sm rounded-full border transition font-medium',
                            'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $isActive,
                            'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:bg-gray-100 dark:hover:bg-gray-800' => ! $isActive,
                        ])>#{{ $cleanTag }}</button>
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
                    @php
                        $plainExcerpt = Str::limit(trim(strip_tags($topic->content)), 140);
                    @endphp
                    <div wire:key="topic-{{ $topic->id }}" class="group relative rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 dark:hover:border-primary-dark/20 p-5 transition-all duration-200">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('recovery.topics.show', $topic) }}" wire:navigate class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-base text-ink dark:text-ink-dark group-hover:text-primary dark:group-hover:text-primary-dark transition">
                                        {{ $topic->title }}
                                    </h3>
                                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-{{ $topic->importance->color() }}/15 text-{{ $topic->importance->color() }} font-semibold">
                                        أهمية {{ $topic->importance->label() }}
                                    </span>
                                </div>
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">
                                    {{ $topic->created_at->translatedFormat('l، j M Y') }}
                                </p>
                            </a>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="$dispatch('edit-topic', { topic: {{ $topic->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="$dispatch('delete-topic', { topic: {{ $topic->id }} })" wire:confirm="حذف هذا الموضوع؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>

                        {{-- Text Excerpt --}}
                        @if (! empty($plainExcerpt))
                            <a href="{{ route('recovery.topics.show', $topic) }}" wire:navigate class="block text-sm text-ink-soft dark:text-ink-dark-soft mt-3 leading-relaxed line-clamp-2 hover:text-ink dark:hover:text-ink-dark transition">
                                {{ $plainExcerpt }}
                            </a>
                        @endif

                        {{-- Footer Tags & View Link --}}
                        <div class="flex items-center justify-between gap-2 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800/60">
                            <div class="flex flex-wrap gap-1.5">
                                @if (! empty($topic->tags))
                                    @foreach ($topic->tags as $t)
                                        @php $cleanTag = ltrim($t, '#'); @endphp
                                        <button type="button" wire:click="$set('tag', @js($cleanTag))" class="text-xs px-2.5 py-1 rounded-full bg-primary/10 text-primary dark:bg-primary-dark/20 dark:text-primary-dark font-medium hover:bg-primary/20 transition">#{{ $cleanTag }}</button>
                                    @endforeach
                                @endif
                            </div>
                            <a href="{{ route('recovery.topics.show', $topic) }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-semibold text-primary dark:text-primary-dark group-hover:translate-x-[-2px] transition-transform">
                                <span>عرض الموضوع</span>
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

    <livewire:recovery.manage-topic />
</div>
