<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">أحلامي ✨</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">أحلامك طويلة المدى كخريطة — أنت فين، وعايز توصل فين.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <x-add-to-today kind="dream" label="تاسك حلم النهاردة" />
                <button type="button" wire:click="$dispatch('create-dream')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ حلم</button>
            </div>
        </div>

        @if ($dreams->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">اكتب أول حلم كبير ليك، وابدأ ترسم طريقه.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach ($dreams as $dream)
                    <a href="{{ route('dreams.show', $dream) }}" wire:navigate wire:key="dream-{{ $dream->id }}"
                       class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6 border-t-4" style="border-top-color: {{ $dream->color }}">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-lg text-ink dark:text-ink-dark">{{ $dream->title }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $dream->status->color() }}/15 text-{{ $dream->status->color() }} shrink-0">{{ $dream->status->label() }}</span>
                        </div>
                        @if ($dream->from_point || $dream->to_point)
                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2">
                                @if ($dream->from_point){{ $dream->from_point }}@endif
                                <span class="text-primary dark:text-primary-dark"> ← </span>
                                @if ($dream->to_point){{ $dream->to_point }}@endif
                            </p>
                        @endif
                        <div class="flex items-center gap-3 mt-3 text-xs text-ink-soft dark:text-ink-dark-soft">
                            @if ($dream->durationLabel())<span>⏳ {{ $dream->durationLabel() }}</span>@endif
                            @if ($dream->target_date)<span>🎯 {{ $dream->target_date->translatedFormat('M Y') }}</span>@endif
                        </div>
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-ink-soft dark:text-ink-dark-soft mb-1">
                                <span>التقدّم</span><span>{{ $dream->progressPercent() }}%</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden">
                                <div class="h-full rounded-full bg-success" style="width: {{ $dream->progressPercent() }}%"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:dreams.manage-dream />
</div>
