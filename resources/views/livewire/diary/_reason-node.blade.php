@foreach ($nodes as $node)
    @php($reason = $node->reason)
    <div wire:key="reason-{{ $reason->id }}">
        @if ($reason->is_important)
            {{-- Starred = one of the most important reasons: a standout circle instead of a row --}}
            <div class="group relative w-36 h-36 sm:w-40 sm:h-40 mx-auto rounded-full bg-gradient-to-br from-primary/15 to-secondary/10 dark:from-primary-dark/20 dark:to-secondary-dark/10 border-2 border-primary/50 dark:border-primary-dark/50 shadow-md flex items-center justify-center text-center p-4 overflow-hidden transition-transform hover:scale-105">
                <span class="absolute top-2 end-3 text-lg">⭐</span>
                <p class="text-xs sm:text-sm font-semibold text-ink dark:text-ink-dark leading-snug line-clamp-5">{{ $reason->body }}</p>

                <div class="absolute inset-0 rounded-full bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-3 text-white text-xs">
                    <button type="button" wire:click="toggleImportant({{ $reason->id }})" title="شيلها من الأهم">☆</button>
                    <button type="button" wire:click="edit({{ $reason->id }})" class="hover:underline">تعديل</button>
                    <button type="button" wire:click="delete({{ $reason->id }})" wire:confirm="حذف هذا السبب وكل فروعه؟" class="hover:underline">حذف</button>
                </div>
            </div>
        @else
            <div class="group flex items-start gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 dark:hover:border-primary-dark/20 p-4 transition-all duration-200">
                <span class="mt-0.5 text-primary dark:text-primary-dark shrink-0">🌿</span>
                <p class="flex-1 text-sm text-ink dark:text-ink-dark leading-relaxed">{{ $reason->body }}</p>
                <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition">
                    <button type="button" wire:click="toggleImportant({{ $reason->id }})" class="text-sm text-warning hover:scale-110 transition" title="علّمه كأهم سبب">☆</button>
                    <button type="button" wire:click="edit({{ $reason->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                    <button type="button" wire:click="delete({{ $reason->id }})" wire:confirm="حذف هذا السبب وكل فروعه؟" class="text-xs text-danger hover:underline">حذف</button>
                </div>
            </div>
        @endif

        @if ($node->children->isNotEmpty())
            <div class="ps-6 ms-4 mt-2 space-y-2 border-s-2 border-ink-soft/15 dark:border-ink-dark-soft/15">
                @include('livewire.diary._reason-node', ['nodes' => $node->children])
            </div>
        @endif
    </div>
@endforeach
