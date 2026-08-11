<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
            <div>
                <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">المنح</h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('scholarships.topics') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">📚 تعلّم عن المنح</a>
                <a href="{{ route('scholarships.volunteering') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">🤝 التطوّع</a>
                <button type="button" wire:click="$dispatch('create-scholarship')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ منحة</button>
            </div>
        </div>

        @if ($scholarships->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">أضف أول منحة لمتابعة تقديمها.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($scholarships as $s)
                    <a href="{{ route('scholarships.show', $s) }}" wire:navigate wire:key="sch-{{ $s->id }}"
                       class="block rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $s->name }}</h3>
                                @if ($s->institution)<p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $s->institution }}</p>@endif
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $s->stage->color() }}/15 text-{{ $s->stage->color() }} shrink-0">{{ $s->stage->label() }}</span>
                        </div>
                        @if ($s->apply_to)
                            @php($d = $s->daysToDeadline())
                            <p class="text-xs mt-3 {{ $d !== null && $d < 0 ? 'text-danger' : 'text-ink-soft dark:text-ink-dark-soft' }}">
                                🗓️ آخر موعد: {{ $s->apply_to->translatedFormat('j M Y') }}
                                @if ($d !== null && ! $s->isClosed())
                                    @if ($d > 0) — باقٍ {{ $d }} يوم @elseif ($d === 0) — النهاردة @else — انتهى @endif
                                @endif
                            </p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:scholarships.manage-scholarship />
</div>
