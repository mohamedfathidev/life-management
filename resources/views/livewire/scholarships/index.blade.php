<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-5 flex-wrap">
            <div>
                <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">المنح</h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('scholarships.documents') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">📄 الأوراق</a>
                <a href="{{ route('scholarships.resources') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">🔖 مصادر</a>
                <a href="{{ route('scholarships.topics') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">📚 التعلّم والاستعداد</a>
                <a href="{{ route('scholarships.volunteering') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">🤝 التطوّع</a>
                <button type="button" wire:click="$dispatch('create-scholarship')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ منحة</button>
            </div>
        </div>

        {{-- Filter --}}
        <div class="flex flex-wrap items-end gap-3 bg-surface-light dark:bg-surface-dark rounded-xl p-3 mb-5">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">المرحلة</label>
                <select wire:model.live="stage" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">كل المراحل</option>
                    @foreach ($stages as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if ($stage !== '')
                <button wire:click="resetFilters" class="px-3 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إعادة ضبط</button>
            @endif
        </div>

        @if ($scholarships->isEmpty())
            <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🎓</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش منح بالفلتر ده.</p>
            </div>
        @else
            {{-- Compact boxes → click to open details --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($scholarships as $s)
                    @php($d = $s->daysToDeadline())
                    @php($ws = $s->windowStatus())
                    @php($wBorder = match ($ws) { 'open' => 'border-success', 'upcoming' => 'border-warning', default => 'border-danger' })
                    @php($wChip = match ($ws) { 'open' => 'bg-success/15 text-success', 'upcoming' => 'bg-warning/15 text-warning', default => 'bg-danger/15 text-danger' })
                    <a href="{{ route('scholarships.show', $s) }}" wire:navigate wire:key="sch-{{ $s->id }}"
                       class="group block rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-4 border-s-4 {{ $wBorder }} {{ $ws === 'closed' ? 'opacity-70' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-2xl">🎓</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-{{ $s->stage->color() }}/15 text-{{ $s->stage->color() }} shrink-0">{{ $s->stage->label() }}</span>
                        </div>
                        <span class="inline-block mt-2 text-[10px] px-2 py-0.5 rounded-full {{ $wChip }}">{{ $ws === 'open' ? '🟢 ' : '' }}{{ $s->windowLabel() }}</span>
                        <h3 class="font-semibold text-sm text-ink dark:text-ink-dark mt-2 line-clamp-2 group-hover:text-primary dark:group-hover:text-primary-dark transition">{{ $s->name }}</h3>
                        @if ($s->institution)<p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1 truncate">{{ $s->institution }}</p>@endif
                        @if ($s->apply_from || $s->apply_to)
                            <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">🗓️ @if ($s->apply_from){{ $s->apply_from->translatedFormat('j M') }}@endif@if ($s->apply_from && $s->apply_to) → @endif@if ($s->apply_to){{ $s->apply_to->translatedFormat('j M Y') }}@endif</p>
                            @php($dOpen = $s->daysToOpen())
                            @if ($ws === 'upcoming' && $dOpen !== null)
                                <p class="text-[11px] text-warning mt-0.5">⏳ باقي {{ $dOpen }} يوم على فتح التقديم</p>
                            @elseif ($ws === 'open' && $d !== null)
                                <p class="text-[11px] text-success mt-0.5">{{ $d === 0 ? '⏳ آخر يوم للتقديم النهاردة' : '⏳ باقي '.$d.' يوم على الغلق' }}</p>
                            @endif
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:scholarships.manage-scholarship />
</div>
