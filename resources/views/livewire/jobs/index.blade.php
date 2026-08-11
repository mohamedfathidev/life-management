<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">الوظائف</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تتبّع تقديماتك واستعدادك للإنترفيو.</p>
            </div>
            <button type="button" wire:click="$dispatch('create-job')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ وظيفة</button>
        </div>

        @if ($jobs->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">أضف أول وظيفة قدّمت عليها.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($jobs as $job)
                    <a href="{{ route('jobs.show', $job) }}" wire:navigate wire:key="job-{{ $job->id }}"
                       class="block rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $job->position }}</h3>
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $job->company }}</p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $job->stage->color() }}/15 text-{{ $job->stage->color() }} shrink-0">{{ $job->stage->label() }}</span>
                        </div>
                        <div class="flex items-center gap-3 mt-3 text-xs text-ink-soft dark:text-ink-dark-soft">
                            @if ($job->applied_via)<span>عن طريق {{ $job->applied_via }}</span>@endif
                            @if ($job->applied_on)<span>· قدّمت {{ $job->applied_on->translatedFormat('j M') }}</span>@endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:jobs.manage-job />
</div>
