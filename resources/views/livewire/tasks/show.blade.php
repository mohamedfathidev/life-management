<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('tasks.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل التاسكات</a>

        <div class="mt-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-ink dark:text-ink-dark flex items-center gap-2">
                        <span>{{ $task->kind->emoji() }}</span>
                        <span>{{ $task->title }}</span>
                    </h1>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-xs text-ink-soft dark:text-ink-dark-soft">
                        <span class="px-2 py-0.5 rounded-full bg-{{ $task->status->color() }}/15 text-{{ $task->status->color() }}">{{ $task->status->label() }}</span>
                        @if ($task->goal)<a href="{{ route('goals.show', $task->goal) }}" wire:navigate class="hover:underline">🎯 {{ $task->goal->title }}</a>@endif
                        @if ($task->day)<span>📅 {{ $task->day->date->translatedFormat('l j M') }}</span>@else<span>🗂️ المؤجلات</span>@endif
                        @if ($task->start_time)<span dir="ltr">🕒 {{ $task->startLabel() }}@if ($task->endLabel()) – {{ $task->endLabel() }}@endif</span>@endif
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" wire:click="edit" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                    <button type="button" wire:click="delete" wire:confirm="حذف التاسك؟" class="text-xs text-danger hover:underline">حذف</button>
                </div>
            </div>

            {{-- Progress --}}
            <div class="mt-4">
                <div class="flex justify-between text-xs text-ink-soft dark:text-ink-dark-soft mb-1"><span>الإنجاز</span><span>{{ $task->progress }}%</span></div>
                <div class="h-2 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden"><div class="h-full rounded-full bg-{{ $task->status->color() }}" style="width: {{ $task->progress }}%"></div></div>
            </div>

            {{-- Focus shortcut --}}
            <a href="{{ route('focus', ['focusType' => 'task', 'focusId' => $task->id]) }}" wire:navigate
               class="mt-4 inline-flex items-center gap-2 text-sm text-primary dark:text-primary-dark hover:underline">
                🎯 ركّز على دي
                @if ($focusMinutes > 0)<span class="text-ink-soft dark:text-ink-dark-soft">· ركّزت عليها {{ \App\Models\Task::minutesToLabel($focusMinutes) }}</span>@endif
            </a>
        </div>

        {{-- Detail form --}}
        <form wire:submit="save" class="mt-6 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 space-y-5">

            {{-- Notes --}}
            <div>
                <x-input-label for="t_notes" value="ملاحظات على التاسك" />
                <textarea id="t_notes" wire:model="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="أي حاجة تخص التاسك دي…"></textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Estimated --}}
                <div>
                    <x-input-label for="t_est" value="الوقت المتوقّع (دقيقة)" />
                    <x-text-input id="t_est" wire:model="estimatedMinutes" type="number" min="0" class="mt-1 block w-full" placeholder="مثال: 45" />
                    <x-input-error :messages="$errors->get('estimatedMinutes')" class="mt-1" />
                </div>

                {{-- Rating --}}
                <div>
                    <x-input-label value="التقييم (من ١٠)" />
                    <div class="flex items-center gap-1 mt-2 flex-wrap">
                        @for ($i = 1; $i <= 10; $i++)
                            <button type="button" wire:click="$set('rating', {{ $i }})"
                                class="w-6 h-6 rounded-full text-[11px] font-medium transition {{ $rating !== null && $i <= $rating ? 'bg-warning text-white' : 'bg-ink-soft/15 text-ink-soft dark:text-ink-dark-soft hover:bg-ink-soft/25' }}">{{ $i }}</button>
                        @endfor
                        @if ($rating !== null)
                            <button type="button" wire:click="$set('rating', null)" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:text-danger ms-1">مسح</button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actual time --}}
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label value="الوقت الفعلي (دقيقة)" />
                    <label class="flex items-center gap-2 text-xs text-ink-soft dark:text-ink-dark-soft cursor-pointer">
                        <input type="checkbox" wire:model.live="actualIsAuto" class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" />
                        تلقائيًا من التركيز
                    </label>
                </div>
                @if ($actualIsAuto)
                    <div class="mt-1 flex items-center gap-2">
                        <div class="block w-full rounded-md border border-dashed border-ink-soft/30 bg-bg-light dark:bg-bg-dark px-3 py-2 text-sm text-ink dark:text-ink-dark">
                            {{ $focusMinutes }} دقيقة <span class="text-xs text-ink-soft dark:text-ink-dark-soft">(من جلسات التركيز)</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">شيل العلامة لو عايز تكتب القيمة بنفسك.</p>
                @else
                    <x-text-input wire:model="actualMinutes" type="number" min="0" class="mt-1 block w-full" placeholder="اكتب الوقت الفعلي بالدقايق" />
                    <x-input-error :messages="$errors->get('actualMinutes')" class="mt-1" />
                @endif
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ التفاصيل</button>
            </div>

            <div wire:loading.remove wire:target="save">
                <p x-data="{ show: false }" x-on:task-detail-saved.window="show = true; setTimeout(() => show = false, 2000)" x-show="show" x-cloak class="text-xs text-success text-center">تم الحفظ ✓</p>
            </div>
        </form>
    </div>
</div>
