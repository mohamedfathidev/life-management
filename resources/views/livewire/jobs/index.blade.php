<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">الوظائف</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تتبّع تقديماتك واستعدادك للإنترفيو.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('jobs.resources') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">🔖 مصادر</a>
                <button type="button" wire:click="$dispatch('create-job')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ وظيفة</button>
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

        @if ($jobs->isEmpty())
            <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">💼</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش وظائف بالفلتر ده.</p>
            </div>
        @else
            {{-- Compact boxes → click to open details --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($jobs as $job)
                    <a href="{{ route('jobs.show', $job) }}" wire:navigate wire:key="job-{{ $job->id }}"
                       class="group block rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-4 {{ $job->isClosed() ? 'opacity-75' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-2xl">💼</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-{{ $job->stage->color() }}/15 text-{{ $job->stage->color() }} shrink-0">{{ $job->stage->label() }}</span>
                        </div>
                        <h3 class="font-semibold text-sm text-ink dark:text-ink-dark mt-2 line-clamp-2 group-hover:text-primary dark:group-hover:text-primary-dark transition">{{ $job->position }}</h3>
                        <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1 truncate">{{ $job->company }}</p>
                        @if ($job->interview_at)
                            <p class="text-[11px] text-primary dark:text-primary-dark mt-1" dir="ltr">🎙️ {{ $job->interview_at->translatedFormat('j M') }}</p>
                        @elseif ($job->deadline)
                            <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1" dir="ltr">⏰ {{ $job->deadline->translatedFormat('j M') }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:jobs.manage-job />
</div>
