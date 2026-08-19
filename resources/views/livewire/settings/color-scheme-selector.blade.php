<div class="space-y-4" x-data="{ changing: false }" @color-scheme-changed.window="changing = true; setTimeout(() => changing = false, 300)">
    <div>
        <h3 class="text-lg font-semibold text-ink dark:text-ink-dark mb-1">🎨 نظام الألوان</h3>
        <p class="text-sm text-ink-soft dark:text-ink-dark-soft">اختر مجموعة الألوان المفضلة لديك</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($schemes as $scheme)
            @php
                $colors = $scheme->previewColors();
                $isActive = $colorScheme === $scheme->value;
            @endphp
            
            <button 
                type="button" 
                wire:click="setColorScheme('{{ $scheme->value }}')"
                @class([
                    'relative rounded-xl p-4 text-right transition-all duration-200',
                    'ring-2 ring-primary dark:ring-primary-dark bg-surface-light dark:bg-surface-dark' => $isActive,
                    'bg-surface-light dark:bg-surface-dark hover:ring-2 hover:ring-ink-soft/20 dark:hover:ring-ink-dark-soft/20' => !$isActive,
                ])
                :class="changing && '{{ $isActive ? 'opacity-100' : 'opacity-50' }}'"
            >
                {{-- Active indicator --}}
                @if ($isActive)
                    <div class="absolute top-2 left-2">
                        <svg class="w-5 h-5 text-success dark:text-success-dark" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                @endif

                {{-- Color preview circles --}}
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full border-2 border-ink-soft/20 dark:border-ink-dark-soft/20" style="background-color: {{ $colors['primary'] }}"></div>
                    <div class="w-6 h-6 rounded-full border-2 border-ink-soft/20 dark:border-ink-dark-soft/20" style="background-color: {{ $colors['secondary'] }}"></div>
                    <div class="w-6 h-6 rounded-full border-2 border-ink-soft/20 dark:border-ink-dark-soft/20" style="background-color: {{ $colors['accent'] }}"></div>
                </div>

                {{-- Scheme info --}}
                <div>
                    <p class="text-sm font-semibold text-ink dark:text-ink-dark">{{ $scheme->label() }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $scheme->description() }}</p>
                </div>
            </button>
        @endforeach
    </div>

    {{-- Color transition animation --}}
    <div 
        x-show="changing" 
        x-transition.opacity
        class="fixed inset-0 bg-black/5 dark:bg-white/5 pointer-events-none z-50"
    ></div>
</div>

{{-- JavaScript to apply color scheme change instantly --}}
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('color-scheme-changed', (event) => {
            const scheme = event.scheme || 'default';
            document.documentElement.setAttribute('data-color-scheme', scheme);
        });
    });
</script>
