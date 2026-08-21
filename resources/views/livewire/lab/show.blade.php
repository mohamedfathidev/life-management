<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('lab.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← معمل الأفكار</a>

        {{-- Header --}}
        <div @class([
            'mt-3 rounded-2xl bg-ink dark:bg-black text-white shadow-sm p-6 border-t-4',
            'border-warning' => $project->status === \App\Enums\ProjectStatus::Idea,
            'border-primary' => $project->status === \App\Enums\ProjectStatus::InProgress,
            'border-danger' => $project->status === \App\Enums\ProjectStatus::Paused,
            'border-success' => $project->status === \App\Enums\ProjectStatus::Done,
        ])>
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-[10px] tracking-widest text-white/40">LAB-{{ str_pad((string) $project->id, 3, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $project->status->color() }}/15 text-{{ $project->status->color() }}">{{ $project->status->emoji() }} {{ $project->status->label() }}</span>
                    </div>
                    <h1 class="text-2xl font-bold mt-1">{{ $project->title }}</h1>
                    @if ($project->pitch)<p class="text-sm text-white/70 mt-2">{{ $project->pitch }}</p>@endif
                    @if ($project->why)<p class="text-xs text-white/50 mt-2">🎯 {{ $project->why }}</p>@endif
                    @if ($project->goal)<p class="text-xs text-white/50 mt-2">🔗 مربوطة بهدف: {{ $project->goal->title }}</p>@endif
                    @if ($project->url)
                        <a href="{{ $project->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 mt-3 text-xs px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 transition" dir="ltr">🚀 شوفها لايف</a>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" wire:click="editProject" class="px-3 py-1.5 rounded-lg border border-white/30 text-white text-sm hover:bg-white/10 transition">تعديل</button>
                    <button type="button" wire:click="delete" wire:confirm="حذف المشروع؟" class="px-3 py-1.5 rounded-lg border border-danger/50 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
                </div>
            </div>

            <div class="mt-5">
                <div class="flex justify-between text-[10px] font-mono tracking-wider text-white/40 mb-1">
                    <span>PROGRESS</span>
                    <span>{{ $steps->where('is_done', true)->count() }}/{{ $steps->count() }} · {{ $progress }}%</span>
                </div>
                <div class="h-2 rounded-full bg-white/10 overflow-hidden">
                    <div class="h-full rounded-full bg-{{ $project->status->color() }} transition-all" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        {{-- Status pipeline --}}
        <div class="mt-6 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 overflow-x-auto">
            <x-stepper :steps="\App\Enums\ProjectStatus::steps($project->status)" class="min-w-[280px]" />
        </div>

        {{-- Journey / steps --}}
        <div class="mt-6 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h2 class="text-sm font-semibold text-ink dark:text-ink-dark mb-4">🪜 خطوات الرحلة</h2>

            <div class="space-y-0">
                @php($foundCurrent = false)
                @forelse ($steps as $i => $step)
                    @php($isCurrent = ! $step->is_done && ! $foundCurrent)
                    @php($foundCurrent = $foundCurrent || $isCurrent)
                    <div wire:key="step-{{ $step->id }}" class="flex items-start gap-3">
                        <div class="flex flex-col items-center">
                            <button type="button" wire:click="toggleStep({{ $step->id }})"
                                @class([
                                    'w-8 h-8 rounded-full flex items-center justify-center text-xs font-mono font-bold shrink-0 transition',
                                    'bg-success text-white' => $step->is_done,
                                    'bg-primary text-white dark:bg-primary-dark ring-4 ring-primary/25' => $isCurrent,
                                    'bg-bg-light dark:bg-bg-dark text-ink-soft dark:text-ink-dark-soft border border-ink-soft/30' => ! $step->is_done && ! $isCurrent,
                                ])>{{ $step->is_done ? '✓' : $i + 1 }}</button>
                            @if (! $loop->last)<div class="w-0.5 flex-1 min-h-[28px] {{ $step->is_done ? 'bg-success/50' : 'bg-ink-soft/20' }}"></div>@endif
                        </div>
                        <div class="pb-5 flex-1 min-w-0">
                            <p @class([
                                'text-sm',
                                'line-through text-ink-soft dark:text-ink-dark-soft' => $step->is_done,
                                'font-semibold text-ink dark:text-ink-dark' => $isCurrent,
                                'text-ink dark:text-ink-dark' => ! $step->is_done && ! $isCurrent,
                            ])>
                                {{ $step->title }}
                                @if ($isCurrent)<span class="text-[10px] text-primary dark:text-primary-dark font-mono">● شغال عليها دلوقتي</span>@endif
                            </p>
                            @if ($step->description)
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1 whitespace-pre-line">{{ $step->description }}</p>
                            @endif
                            <button type="button" wire:click="deleteStep({{ $step->id }})" class="text-[10px] text-danger hover:underline mt-1">حذف</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mb-2">لسه مفيش خطوات — ابدأ سجّل أول خطوة بتعملها.</p>
                @endforelse
            </div>

            {{-- Add step --}}
            <div class="mt-2 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30 p-4 space-y-2">
                <input type="text" wire:model="newStepTitle" wire:keydown.enter.prevent="addStep"
                       class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="+ خطوة جديدة" />
                <textarea wire:model="newStepDescription" rows="2"
                          class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="بتعمل / عملت فيها إيه؟ (اختياري)"></textarea>
                <div class="flex justify-end">
                    <button type="button" wire:click="addStep" class="text-xs px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90 transition">إضافة خطوة</button>
                </div>
            </div>
        </div>
    </div>

    <livewire:lab.manage-project />
</div>
