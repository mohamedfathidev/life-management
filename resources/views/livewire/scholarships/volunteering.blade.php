<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('scholarships.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← المنح</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">التطوّع</h1>
            </div>
            <div class="flex items-center gap-3">
                @if ($totalHours > 0)
                    <div class="text-center rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm px-4 py-2">
                        <p class="text-xl font-bold text-primary dark:text-primary-dark">{{ $totalHours }}</p>
                        <p class="text-[10px] text-ink-soft dark:text-ink-dark-soft">ساعة</p>
                    </div>
                @endif
                <button type="button" wire:click="$dispatch('create-volunteer')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ نشاط / تقديم</button>
            </div>
        </div>

        {{-- Tabs: two separate parts --}}
        <div class="flex items-center gap-1 mb-6 rounded-lg bg-bg-light dark:bg-bg-dark p-1 w-fit">
            <button type="button" wire:click="$set('tab', 'current')"
                @class(['px-4 py-1.5 text-sm rounded-md transition', 'bg-primary text-white dark:bg-primary-dark' => $tab === 'current', 'text-ink-soft dark:text-ink-dark-soft' => $tab !== 'current'])>
                الأنشطة الحالية ({{ $current->count() }})
            </button>
            <button type="button" wire:click="$set('tab', 'applications')"
                @class(['px-4 py-1.5 text-sm rounded-md transition', 'bg-primary text-white dark:bg-primary-dark' => $tab === 'applications', 'text-ink-soft dark:text-ink-dark-soft' => $tab !== 'applications'])>
                التقديمات ({{ $applications->count() }})
            </button>
        </div>

        @if ($tab === 'current')
            {{-- Current activities --}}
            @forelse ($current as $a)
                <div wire:key="volcur-{{ $a->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 mb-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $a->title }}</h3>
                            @if ($a->organization)<p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $a->organization }}</p>@endif
                            <div class="flex items-center gap-3 mt-2 text-xs text-ink-soft dark:text-ink-dark-soft">
                                @if ($a->start_date)<span>{{ $a->start_date->translatedFormat('j M Y') }}@if ($a->end_date) — {{ $a->end_date->translatedFormat('j M Y') }}@endif</span>@endif
                                @if ($a->hours)<span>· {{ $a->hours }} ساعة</span>@endif
                            </div>
                            @if ($a->description)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2 whitespace-pre-line">{{ $a->description }}</p>@endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="$dispatch('edit-volunteer', { activity: {{ $a->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="$dispatch('delete-volunteer', { activity: {{ $a->id }} })" wire:confirm="حذف هذا النشاط؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                    <p class="text-ink-soft dark:text-ink-dark-soft">لا توجد أنشطة حالية.</p>
                </div>
            @endforelse

        @else
            {{-- Applications with pipeline --}}
            @forelse ($applications as $a)
                <div wire:key="volapp-{{ $a->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 mb-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="font-semibold text-ink dark:text-ink-dark flex items-center gap-2 flex-wrap">
                                {{ $a->title }}
                                <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $a->stage->color() }}/15 text-{{ $a->stage->color() }}">{{ $a->stage->label() }}</span>
                            </h3>
                            <div class="flex items-center gap-3 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                                @if ($a->organization)<span>{{ $a->organization }}</span>@endif
                                @if ($a->applied_via)<span>· من خلال {{ $a->applied_via }}</span>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="$dispatch('edit-volunteer', { activity: {{ $a->id }} })" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="$dispatch('delete-volunteer', { activity: {{ $a->id }} })" wire:confirm="حذف؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>

                    {{-- pipeline --}}
                    <div class="mt-4">
                        <x-stepper :steps="\App\Enums\ScholarshipStage::steps($a->stage, $a->submitted_on, $a->decided_on)" />
                    </div>

                    @if ($a->stage->value === 'rejected' && $a->rejection_reason)
                        <p class="text-xs text-danger mb-2">سبب الرفض: {{ $a->rejection_reason }}</p>
                    @endif

                    {{-- inline advance --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @switch($a->stage->value)
                            @case('preparing')
                                <button type="button" wire:click="markSubmitted({{ $a->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">✓ قدّمت الأوراق</button>
                                @break
                            @case('submitted')
                                <button type="button" wire:click="markWaiting({{ $a->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">انتظار الرد →</button>
                                @break
                            @case('waiting')
                                <button type="button" wire:click="markInterview({{ $a->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">🎙️ عندي إنترفيو</button>
                                <button type="button" wire:click="markAccepted({{ $a->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-success text-white hover:opacity-90">✓ قبول</button>
                                <button type="button" wire:click="markRejected({{ $a->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-danger/15 text-danger hover:bg-danger/25">✕ رفض</button>
                                @break
                            @case('interview')
                                <button type="button" wire:click="markAccepted({{ $a->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-success text-white hover:opacity-90">✓ قبول</button>
                                <button type="button" wire:click="markRejected({{ $a->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-danger/15 text-danger hover:bg-danger/25">✕ رفض</button>
                                @break
                            @default
                                <span class="text-xs text-ink-soft dark:text-ink-dark-soft">مُغلق</span>
                        @endswitch
                    </div>
                </div>
            @empty
                <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                    <p class="text-ink-soft dark:text-ink-dark-soft">لا توجد تقديمات جارية.</p>
                </div>
            @endforelse
        @endif
    </div>

    <livewire:scholarships.manage-volunteer />
</div>
