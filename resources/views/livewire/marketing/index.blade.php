<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">التسويق الشخصي</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">جدوِل بوستاتك وابنِ حضورك خطوة بخطوة.</p>
            </div>
            <button type="button" wire:click="$dispatch('create-post')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ بوست</button>
        </div>

        {{-- Board: columns per status --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($statuses as $status)
                @php($column = $posts[$status->value] ?? collect())
                <div class="rounded-xl bg-bg-light dark:bg-bg-dark p-3">
                    <div class="flex items-center justify-between mb-3 px-1">
                        <h3 class="text-sm font-semibold text-{{ $status->color() }}">{{ $status->label() }}</h3>
                        <span class="text-xs text-ink-soft dark:text-ink-dark-soft">{{ $column->count() }}</span>
                    </div>

                    <div class="space-y-2">
                        @foreach ($column as $post)
                            <div wire:key="post-{{ $post->id }}" class="rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm p-3">
                                <p class="text-sm font-medium text-ink dark:text-ink-dark">{{ $post->topic }}</p>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-ink-soft dark:text-ink-dark-soft flex-wrap">
                                    <span>{{ $post->platform }}</span>
                                    @if ($post->scheduled_for)<span>· 🗓️ {{ $post->scheduled_for->translatedFormat('j M') }}</span>@endif
                                </div>
                                @if ($post->content)
                                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2 line-clamp-3 whitespace-pre-line">{{ $post->content }}</p>
                                @endif
                                @if ($post->status->value === 'published' && $post->link)
                                    <a href="{{ $post->link }}" target="_blank" rel="noopener" class="text-xs text-primary dark:text-primary-dark hover:underline mt-2 inline-block">🔗 البوست</a>
                                @endif

                                <div class="flex items-center gap-2 mt-3 pt-2 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                                    @if ($post->status->next())
                                        <button type="button" wire:click="advance({{ $post->id }})" class="text-[11px] px-2 py-1 rounded bg-primary/10 text-primary dark:text-primary-dark hover:bg-primary/20 transition">{{ $post->status->next()->label() }} →</button>
                                    @endif
                                    <button type="button" wire:click="$dispatch('edit-post', { post: {{ $post->id }} })" class="text-[11px] text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                    <button type="button" wire:click="delete({{ $post->id }})" wire:confirm="حذف البوست؟" class="text-[11px] text-danger hover:underline">حذف</button>
                                </div>
                            </div>
                        @endforeach

                        <button type="button" wire:click="$dispatch('create-post', { status: '{{ $status->value }}' })" class="w-full text-xs text-ink-soft dark:text-ink-dark-soft hover:text-primary dark:hover:text-primary-dark py-2 rounded-lg border border-dashed border-ink-soft/20 transition">+ إضافة</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <livewire:marketing.manage-post />
</div>
