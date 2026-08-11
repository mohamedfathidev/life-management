<div
    x-data
    x-on:theme-changed.window="
        const t = $event.detail.theme;
        localStorage.setItem('theme', t);
        const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const dark = t === 'dark' || (t === 'system' && systemDark);
        document.documentElement.classList.toggle('dark', dark);
    "
    class="inline-flex items-center gap-1 rounded-lg bg-bg-light dark:bg-bg-dark p-1"
    role="group"
    aria-label="اختيار المظهر"
>
    @foreach ($themes as $option)
        <button
            type="button"
            wire:click="setTheme('{{ $option->value }}')"
            @class([
                'px-3 py-1.5 text-sm rounded-md transition-colors duration-200',
                'bg-primary text-white dark:bg-primary-dark' => $theme === $option->value,
                'text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' => $theme !== $option->value,
            ])
            aria-pressed="{{ $theme === $option->value ? 'true' : 'false' }}"
        >
            {{ $option->label() }}
        </button>
    @endforeach
</div>
