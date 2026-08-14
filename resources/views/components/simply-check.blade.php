@props(['done' => false, 'toggle' => '', 'title' => '', 'emoji' => '🌱', 'url' => null, 'hint' => null, 'important' => false])

<div {{ $attributes }} class="flex items-center gap-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 {{ $important ? 'ring-2 ring-warning/50 bg-warning/5' : '' }}">
    @if ($important)<span class="shrink-0 text-warning">⭐</span>@endif
    <button type="button" wire:click="{{ $toggle }}"
        class="shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition {{ $done ? 'bg-success border-success text-white' : 'border-success/40 text-transparent hover:border-success' }}">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    </button>
    @php($body = '<p class="text-sm font-medium text-ink dark:text-ink-dark truncate '.($done ? 'line-through opacity-60' : '').'">'.e($emoji).' '.e($title).'</p>')
    @if ($url)
        <a href="{{ $url }}" wire:navigate class="flex-1 min-w-0">
            {!! $body !!}
            @if ($hint)<span class="inline-block mt-0.5 text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $hint }}</span>@endif
        </a>
    @else
        <div class="flex-1 min-w-0">
            {!! $body !!}
            @if ($hint)<span class="inline-block mt-0.5 text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $hint }}</span>@endif
        </div>
    @endif
</div>
