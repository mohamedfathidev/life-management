<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('dreams.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل الأحلام</a>

        {{-- Header --}}
        <div class="mt-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 border-t-4" style="border-top-color: {{ $dream->color }}">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $dream->title }}</h1>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $dream->status->color() }}/15 text-{{ $dream->status->color() }}">{{ $dream->status->label() }}</span>
                    </div>
                    <div class="flex items-center gap-3 mt-2 text-sm text-ink-soft dark:text-ink-dark-soft flex-wrap">
                        @if ($dream->durationLabel())<span>⏳ {{ $dream->durationLabel() }}</span>@endif
                        @if ($dream->target_date)<span>🎯 {{ $dream->target_date->translatedFormat('F Y') }}</span>@endif
                    </div>
                    @if ($dream->why)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">💜 {{ $dream->why }}</p>@endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('dreams.road', $dream) }}" wire:navigate class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">🛣️ شوف المحطات الرئيسية</a>
                    <button type="button" wire:click="editDream" class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
                    <button type="button" wire:click="delete" wire:confirm="حذف الحلم؟" class="px-3 py-1.5 rounded-lg border border-danger/40 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-xs text-ink-soft dark:text-ink-dark-soft mb-1"><span>التقدّم على الطريق</span><span>{{ $progress }}%</span></div>
                <div class="h-2 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden"><div class="h-full rounded-full bg-success transition-all" style="width: {{ $progress }}%"></div></div>
            </div>
        </div>

        {{-- The map --}}
        <div class="mt-6">
            {{-- START --}}
            <div class="flex flex-col items-center">
                <div class="rounded-full bg-primary dark:bg-primary-dark text-white text-sm font-medium px-5 py-2 shadow">📍 أنا هنا@if ($dream->from_point): {{ $dream->from_point }}@endif</div>
                <svg viewBox="0 0 24 40" class="w-6 h-8" aria-hidden="true">
                    <path d="M12,0 C22,10 22,10 12,20 C2,30 2,30 12,40" fill="none" class="stroke-ink-soft dark:stroke-ink-dark-soft" stroke-width="3" stroke-linecap="round" opacity="0.35" />
                </svg>
            </div>

            {{-- Branches (paths) --}}
            @if ($paths->isEmpty())
                <div class="text-center py-8 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                    <p class="text-ink-soft dark:text-ink-dark-soft text-sm">أضف طريق (فرع) للوصول لحلمك — ممكن يكون فيه أكتر من طريق.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($paths as $path)
                        @php($foundCurrent = false)
                        <div wire:key="path-{{ $path->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm border border-primary/10 dark:border-primary-dark/15 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-ink dark:text-ink-dark flex items-center gap-2">🛤️ {{ $path->title }}</h3>
                                <button type="button" wire:click="deletePath({{ $path->id }})" wire:confirm="حذف الطريق ومحطاته؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>

                            {{-- Stations — drawn as a winding road, like "قبل الوقوع تذكر" --}}
                            <div class="space-y-0">
                                @forelse ($path->milestones as $ms)
                                    @php($isCurrent = ! $ms->is_done && ! $foundCurrent)
                                    @php($foundCurrent = $foundCurrent || $isCurrent)
                                    <div wire:key="ms-{{ $ms->id }}" class="flex items-start gap-3">
                                        <div class="flex flex-col items-center">
                                            <button type="button" wire:click="toggleMilestone({{ $ms->id }})"
                                                @class([
                                                    'w-8 h-8 rounded-full flex items-center justify-center text-xs shrink-0 transition shadow-sm',
                                                    'bg-success text-white' => $ms->is_done,
                                                    'bg-primary text-white dark:bg-primary-dark ring-4 ring-primary/30 animate-pulse' => $isCurrent,
                                                    'bg-bg-light dark:bg-bg-dark text-ink-soft dark:text-ink-dark-soft border border-ink-soft/30' => ! $ms->is_done && ! $isCurrent,
                                                ])>{{ $ms->is_done ? '✓' : '○' }}</button>
                                            @if (! $loop->last)
                                                {{-- Winding connector: a fixed abstract curve stretched (non-uniformly) to
                                                     whatever height this row naturally takes, so it stays aligned between
                                                     the two circles regardless of how much the milestone text wraps. --}}
                                                <svg viewBox="0 0 24 100" preserveAspectRatio="none" class="w-6 flex-1 min-h-[28px]" aria-hidden="true">
                                                    <path d="M12,0 C22,25 22,25 12,50 C2,75 2,75 12,100" fill="none"
                                                          class="{{ $ms->is_done ? 'stroke-success' : 'stroke-ink-soft dark:stroke-ink-dark-soft' }}"
                                                          stroke-width="3" stroke-linecap="round" opacity="{{ $ms->is_done ? '0.5' : '0.35' }}" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="pb-4 flex-1 min-w-0">
                                            <p @class(['text-sm', 'line-through text-ink-soft dark:text-ink-dark-soft' => $ms->is_done, 'font-medium text-ink dark:text-ink-dark' => $isCurrent, 'text-ink dark:text-ink-dark' => ! $ms->is_done && ! $isCurrent])>
                                                {{ $ms->title }}
                                                @if ($isCurrent)<span class="text-[10px] text-primary dark:text-primary-dark">• أنت هنا دلوقتي</span>@endif
                                            </p>
                                            @if ($ms->target_date)<p class="text-[11px] text-ink-soft dark:text-ink-dark-soft">🎯 {{ $ms->target_date->translatedFormat('M Y') }}</p>@endif
                                            <button type="button" wire:click="deleteMilestone({{ $ms->id }})" class="text-[10px] text-danger hover:underline">حذف</button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-3">مفيش محطات لسه.</p>
                                @endforelse
                            </div>

                            {{-- Add milestone --}}
                            <div class="flex items-center gap-2 mt-2">
                                <input type="text" wire:model="newMilestone.{{ $path->id }}" wire:keydown.enter.prevent="addMilestone({{ $path->id }})"
                                       class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="+ محطة" />
                                <button type="button" wire:click="addMilestone({{ $path->id }})" class="text-xs px-3 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">إضافة</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Add path --}}
            <div class="flex items-center gap-2 mt-4">
                <input type="text" wire:model="newPathTitle" wire:keydown.enter.prevent="addPath"
                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="+ طريق جديد (مثال: عن طريق منحة ماستر)" />
                <button type="button" wire:click="addPath" class="px-4 py-2 rounded-lg bg-secondary/40 text-ink dark:text-ink-dark text-sm hover:opacity-90">إضافة طريق</button>
            </div>

            {{-- DESTINATION --}}
            <div class="flex flex-col items-center mt-6">
                <svg viewBox="0 0 24 40" class="w-6 h-8" aria-hidden="true">
                    <path d="M12,0 C22,10 22,10 12,20 C2,30 2,30 12,40" fill="none" class="stroke-ink-soft dark:stroke-ink-dark-soft" stroke-width="3" stroke-linecap="round" opacity="0.35" />
                </svg>
                @php($destBg = $dream->darkerColor())
                <div class="rounded-full text-sm font-semibold px-5 py-2 shadow" style="background: {{ $destBg }}; color: {{ $dream->contrastText($destBg) }}">🏁 الحلم@if ($dream->to_point): {{ $dream->to_point }}@endif</div>
            </div>
        </div>
    </div>

    <livewire:dreams.manage-dream />
</div>
