<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('recovery.changes') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-ink-soft hover:text-primary dark:text-ink-dark-soft dark:hover:text-primary-dark transition group">
                <svg class="w-4 h-4 rtl:rotate-180 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>تغييرات جذرية</span>
            </a>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="editChange" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-ink dark:text-ink-dark hover:bg-gray-50 dark:hover:bg-gray-800 transition shadow-sm">تعديل</button>
                <button type="button" wire:click="deleteChange" wire:confirm="حذف هذا التغيير مع كل خطواته؟" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-xs font-medium text-danger hover:bg-red-50 dark:hover:bg-red-950/30 transition shadow-sm">حذف</button>
            </div>
        </div>

        {{-- Progress hero --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-5 flex-wrap sm:flex-nowrap">
                @php
                    $circumference = 163.36; // r=26
                    $offset = $circumference - ($circumference * $progress / 100);
                @endphp
                <div class="relative w-24 h-24 shrink-0 mx-auto sm:mx-0">
                    <svg viewBox="0 0 64 64" class="w-24 h-24 -rotate-90">
                        <circle cx="32" cy="32" r="26" fill="none" stroke-width="5" class="stroke-ink-soft/15 dark:stroke-ink-dark-soft/15" />
                        <circle cx="32" cy="32" r="26" fill="none" stroke-width="5" stroke-linecap="round"
                                class="stroke-primary dark:stroke-primary-dark transition-all duration-700"
                                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl">{{ $change->icon ?: '🔥' }}</span>
                    </div>
                </div>

                <div class="min-w-0 flex-1 text-center sm:text-start">
                    <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-ink dark:text-ink-dark">{{ $change->title }}</h1>
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-{{ $change->status->color() }}/15 text-{{ $change->status->color() }}">{{ $change->status->label() }}</span>
                    </div>
                    @if ($change->why)
                        <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2 leading-relaxed">{{ $change->why }}</p>
                    @endif
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-3">
                        بدأت من {{ $change->started_at->translatedFormat('j M Y') }} — {{ $change->daysSinceStart() }} يوم
                        @if ($change->target_date)
                            @php($left = $change->daysRemaining())
                            <span class="mx-1">·</span>
                            {{ $left > 0 ? "باقي {$left} يوم على الهدف" : 'الموعد المستهدف فات' }}
                        @endif
                        @if ($change->recovery)
                            <span class="mx-1">·</span>
                            <a href="{{ route('recovery.show', $change->recovery) }}" wire:navigate class="text-primary dark:text-primary-dark hover:underline">في {{ $change->recovery->title }}</a>
                        @endif
                    </p>
                </div>

                <span class="text-3xl font-black text-primary dark:text-primary-dark shrink-0">{{ $progress }}%</span>
            </div>
        </div>

        {{-- Steps checklist --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h2 class="text-sm font-bold text-ink dark:text-ink-dark mb-4">خطوات التنفيذ</h2>

            @if ($steps->isEmpty())
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-6">قسّم التغيير لخطوات صغيرة تقدر تتابعها — ابدأ بأول خطوة تحت.</p>
            @else
                <ul class="space-y-2">
                    @foreach ($steps as $step)
                        <li wire:key="step-{{ $step->id }}" class="group flex items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                            <button type="button" wire:click="toggleStep({{ $step->id }})"
                                    class="w-5 h-5 shrink-0 rounded-full border-2 flex items-center justify-center transition
                                           {{ $step->is_done ? 'bg-success border-success dark:bg-success-dark dark:border-success-dark' : 'border-ink-soft/40 dark:border-ink-dark-soft/40' }}">
                                @if ($step->is_done)
                                    <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </button>
                            <span class="flex-1 text-sm {{ $step->is_done ? 'text-ink-soft dark:text-ink-dark-soft line-through' : 'text-ink dark:text-ink-dark' }}">{{ $step->title }}</span>
                            <button type="button" wire:click="deleteStep({{ $step->id }})" class="text-xs text-danger opacity-0 group-hover:opacity-100 transition">حذف</button>
                        </li>
                    @endforeach
                </ul>
            @endif

            <form wire:submit="addStep" class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800/60">
                <input type="text" wire:model="newStep" placeholder="خطوة جديدة…" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">إضافة</button>
            </form>
            <x-input-error :messages="$errors->get('newStep')" class="mt-2" />
        </div>
    </div>

    <livewire:recovery.manage-change />
</div>
