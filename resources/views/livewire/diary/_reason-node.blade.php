@foreach ($nodes as $node)
    @php($reason = $node->reason)
    <div wire:key="reason-{{ $reason->id }}">
        <div class="group flex items-start gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-primary/20 dark:hover:border-primary-dark/20 p-4 transition-all duration-200">
            <span class="mt-0.5 text-primary dark:text-primary-dark shrink-0">🌿</span>
            <p class="flex-1 text-sm text-ink dark:text-ink-dark leading-relaxed">{{ $reason->body }}</p>
            <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition">
                <button type="button" wire:click="edit({{ $reason->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                <button type="button" wire:click="delete({{ $reason->id }})" wire:confirm="حذف هذا السبب وكل فروعه؟" class="text-xs text-danger hover:underline">حذف</button>
            </div>
        </div>

        @if ($node->children->isNotEmpty())
            <div class="ps-6 ms-4 mt-2 space-y-2 border-s-2 border-ink-soft/15 dark:border-ink-dark-soft/15">
                @include('livewire.diary._reason-node', ['nodes' => $node->children])
            </div>
        @endif
    </div>
@endforeach
