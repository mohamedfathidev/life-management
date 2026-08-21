<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">معمل الأفكار 🧪</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">حاجات نفسك تجربها وتعملها — من الفكرة لحد ما تطلع للنور.</p>
            </div>
            <button type="button" wire:click="$dispatch('create-project')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition shrink-0">+ فكرة</button>
        </div>

        {{-- Filter --}}
        <div class="flex flex-wrap items-end gap-3 bg-surface-light dark:bg-surface-dark rounded-xl p-3 mb-5">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">الحالة</label>
                <select wire:model.live="status" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">كل الحالات</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->value }}">{{ $s->emoji() }} {{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if ($status !== '')
                <button wire:click="resetFilters" class="px-3 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إعادة ضبط</button>
            @endif
        </div>

        @if ($projects->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🧪</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">اكتب أول فكرة نفسك تجربها، وابدأ سجّل خطواتك فيها.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach ($projects as $project)
                    @php($pct = $project->steps_count > 0 ? (int) round($project->done_steps_count / $project->steps_count * 100) : 0)
                    <a href="{{ route('lab.show', $project) }}" wire:navigate wire:key="proj-{{ $project->id }}"
                       class="group block rounded-2xl bg-ink dark:bg-black text-white shadow-sm hover:shadow-lg transition p-5">
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-mono text-[10px] tracking-widest text-white/40">LAB-{{ str_pad((string) $project->id, 3, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-{{ $project->status->color() }}/15 text-{{ $project->status->color() }} shrink-0">{{ $project->status->emoji() }} {{ $project->status->label() }}</span>
                        </div>
                        <h3 class="font-bold text-lg mt-2 group-hover:text-primary-dark transition">{{ $project->title }}</h3>
                        @if ($project->pitch)
                            <p class="text-xs text-white/60 mt-1 line-clamp-2">{{ $project->pitch }}</p>
                        @endif
                        <div class="mt-4">
                            <div class="flex justify-between text-[10px] font-mono tracking-wider text-white/40 mb-1">
                                <span>PROGRESS</span><span>{{ $project->done_steps_count }}/{{ $project->steps_count }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-white/10 overflow-hidden">
                                <div class="h-full rounded-full bg-{{ $project->status->color() }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:lab.manage-project />
</div>
