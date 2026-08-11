@props(['label' => '', 'active' => false])

@php
$trigger = ($active ?? false)
    ? 'inline-flex items-center gap-1 px-1 pt-1 border-b-2 border-indigo-400 dark:border-indigo-600 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none transition duration-150 ease-in-out'
    : 'inline-flex items-center gap-1 px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none transition duration-150 ease-in-out';
@endphp

<div class="relative inline-flex items-center" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button type="button" @click="open = ! open" class="{{ $trigger }}">
        <span>{{ $label }}</span>
        <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-180'" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" x-transition x-cloak
         @click="open = false"
         class="absolute top-full start-0 z-50 mt-1 w-52 rounded-lg bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black/5 py-1">
        {{ $slot }}
    </div>
</div>
