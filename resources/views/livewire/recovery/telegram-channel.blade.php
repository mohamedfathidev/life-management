<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">📡 قناة تيليجرام</h1>
            </div>
            <button type="button" wire:click="sync" wire:loading.attr="disabled" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition disabled:opacity-60">
                <span wire:loading.remove wire:target="sync">تحديث ↻</span>
                <span wire:loading wire:target="sync">بيجيب الجديد...</span>
            </button>
        </div>

        <p class="text-sm text-ink-soft dark:text-ink-dark-soft -mt-3">
            آخر منشورات قناة <a href="https://t.me/{{ $channel }}" target="_blank" rel="noopener" class="text-primary dark:text-primary-dark hover:underline">t.me/{{ $channel }}</a> — اضغط «تحديث» عشان تجيب الجديد.
        </p>

        {{-- Channel tabs --}}
        <div class="flex items-center gap-2 flex-wrap border-b border-ink-soft/10 dark:border-ink-dark-soft/10 -mt-2 pb-px">
            @foreach ($channels as $username => $name)
                <button type="button" wire:click="switchChannel('{{ $username }}')"
                        class="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition {{ $channel === $username
                            ? 'border-primary dark:border-primary-dark text-primary dark:text-primary-dark'
                            : 'border-transparent text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' }}">
                    {{ $name }}
                </button>
            @endforeach
        </div>

        @if ($syncMessage)
            <div class="p-3 rounded-lg text-sm {{ $syncFailed ? 'bg-danger/10 text-danger' : 'bg-success/10 text-success' }}">
                {{ $syncMessage }}
            </div>
        @endif

        @if ($posts->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-5xl mb-3">📡</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش منشورات لسه — اضغط «تحديث» عشان تجيب آخر المنشورات.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($posts as $post)
                    <article wire:key="tg-{{ $post->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm overflow-hidden">
                        @if ($post->image_url)
                            <div class="bg-black/5 dark:bg-black/40 flex items-center justify-center">
                                <img src="{{ $post->image_url }}" alt="" loading="lazy" class="w-full max-h-[32rem] object-contain">
                            </div>
                        @elseif ($post->video_url)
                            <div class="bg-black/5 dark:bg-black/40 flex items-center justify-center">
                                <video src="{{ $post->video_url }}" controls class="w-full max-h-[32rem] object-contain"></video>
                            </div>
                        @endif

                        <div class="p-4 space-y-2">
                            @if ($post->content)
                                <div class="text-sm text-ink dark:text-ink-dark leading-relaxed">{!! $post->content !!}</div>
                            @endif

                            <div class="flex items-center justify-between pt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                                <a href="{{ $post->post_url }}" target="_blank" rel="noopener" class="hover:underline">فتح في تيليجرام ↗</a>
                                @if ($post->posted_at)
                                    <span>{{ $post->posted_at->translatedFormat('j M Y، g:i A') }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($posts->hasPages())
                <div class="mt-6">{{ $posts->links() }}</div>
            @endif
        @endif
    </div>
</div>
