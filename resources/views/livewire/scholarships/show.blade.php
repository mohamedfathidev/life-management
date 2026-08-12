<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('scholarships.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل المنح</a>

        {{-- Header --}}
        <div class="mt-3 flex items-start justify-between gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $scholarship->name }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $scholarship->stage->color() }}/15 text-{{ $scholarship->stage->color() }}">{{ $scholarship->stage->label() }}</span>
                </div>
                @if ($scholarship->institution)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $scholarship->institution }}</p>@endif
                @if ($scholarship->apply_from || $scholarship->apply_to)
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">
                        🗓️ التقديم:
                        @if ($scholarship->apply_from) من {{ $scholarship->apply_from->translatedFormat('j M Y') }} @endif
                        @if ($scholarship->apply_to) إلى {{ $scholarship->apply_to->translatedFormat('j M Y') }} @endif
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" wire:click="editScholarship" class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
                <button type="button" wire:click="delete" wire:confirm="حذف هذه المنحة؟" class="px-3 py-1.5 rounded-lg border border-danger/40 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
            </div>
        </div>

        {{-- Stations timeline --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-5">خط التقديم</h3>
            <x-stepper :steps="$steps" />

            {{-- Advance actions --}}
            <div class="flex flex-wrap items-center gap-2 mt-6 pt-5 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                @switch($scholarship->stage->value)
                    @case('preparing')
                        <button type="button" wire:click="markSubmitted" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm hover:opacity-90 transition">✓ قدّمت الأوراق</button>
                        @break
                    @case('submitted')
                        <button type="button" wire:click="markWaiting" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm hover:opacity-90 transition">في انتظار الرد →</button>
                        @break
                    @case('waiting')
                        <button type="button" wire:click="markInterview" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm hover:opacity-90 transition">🎙️ عندي إنترفيو</button>
                        <button type="button" wire:click="markAccepted" class="px-4 py-2 rounded-lg bg-success text-white text-sm hover:opacity-90 transition">✓ قبول</button>
                        <button type="button" wire:click="markRejected" class="px-4 py-2 rounded-lg bg-danger/15 text-danger text-sm hover:bg-danger/25 transition">✕ رفض</button>
                        @break
                    @case('interview')
                        <button type="button" wire:click="markAccepted" class="px-4 py-2 rounded-lg bg-success text-white text-sm hover:opacity-90 transition">✓ قبول</button>
                        <button type="button" wire:click="markRejected" class="px-4 py-2 rounded-lg bg-danger/15 text-danger text-sm hover:bg-danger/25 transition">✕ رفض</button>
                        @break
                    @default
                        <span class="text-sm text-ink-soft dark:text-ink-dark-soft">تم إغلاق التقديم.</span>
                        <button type="button" wire:click="reopen" class="px-3 py-1.5 rounded-lg border border-ink-soft/20 text-ink dark:text-ink-dark text-sm hover:bg-ink-soft/5 transition">إعادة فتح</button>
                @endswitch
            </div>

            {{-- Rejection reason (inline) --}}
            @if ($scholarship->stage->value === 'rejected')
                <div class="mt-4">
                    <x-input-label for="sch_rej" value="سبب الرفض (من الإيميل)" />
                    <textarea id="sch_rej" wire:model="rejectionReason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                    <button type="button" wire:click="saveRejectionReason" class="mt-2 text-xs px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">حفظ السبب</button>
                </div>
            @endif
        </div>

        {{-- Requirements + notes --}}
        @if ($scholarship->requirements || $scholarship->notes)
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($scholarship->requirements)
                    <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                        <h3 class="font-semibold text-ink dark:text-ink-dark mb-2">الشروط / المطلوب</h3>
                        <p class="text-sm text-ink-soft dark:text-ink-dark-soft whitespace-pre-line">{{ $scholarship->requirements }}</p>
                    </div>
                @endif
                @if ($scholarship->notes)
                    <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                        <h3 class="font-semibold text-ink dark:text-ink-dark mb-2">ملاحظات</h3>
                        <p class="text-sm text-ink-soft dark:text-ink-dark-soft whitespace-pre-line">{{ $scholarship->notes }}</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <livewire:scholarships.manage-scholarship />
</div>
