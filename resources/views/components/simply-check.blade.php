@props(['done' => false, 'toggle' => '', 'title' => '', 'emoji' => '🌱', 'url' => null])

<div {{ $attributes }} class="flex items-center gap-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
    <button type="button" wire:click="{{ $toggle }}"
        class="shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition {{ $done ? 'bg-success border-success text-white' : 'border-success/40 text-transparent hover:border-success' }}">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </button>
    @if ($url)
        <a href="{{ $url }}" wire:navigate class="flex-1 min-w-0">
            <p class="text-sm font-medium text-ink dark:text-ink-dark truncate {{ $done ? 'line-through opacity-60' : '' }}">{{ $emoji }} {{ $title }}</p>
        </a>
    @else
        <p class="flex-1 min-w-0 text-sm font-medium text-ink dark:text-ink-dark truncate {{ $done ? 'line-through opacity-60' : '' }}">{{ $emoji }} {{ $title }}</p>
    @endif
</div>
