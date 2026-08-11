<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('jobs.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل الوظائف</a>

        {{-- Header --}}
        <div class="mt-3 flex items-start justify-between gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $job->position }}</h1>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $job->stage->color() }}/15 text-{{ $job->stage->color() }}">{{ $job->stage->label() }}</span>
                </div>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $job->company }}</p>
                <div class="flex items-center gap-3 mt-2 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                    @if ($job->applied_via)<span>عن طريق {{ $job->applied_via }}</span>@endif
                    @if ($job->applied_on)<span>· قدّمت {{ $job->applied_on->translatedFormat('j M Y') }}</span>@endif
                    @if ($job->url)<a href="{{ $job->url }}" target="_blank" rel="noopener" class="text-primary dark:text-primary-dark hover:underline">🔗 الإعلان</a>@endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" wire:click="editJob" class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
                <button type="button" wire:click="delete" wire:confirm="حذف هذه الوظيفة؟" class="px-3 py-1.5 rounded-lg border border-danger/40 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
            </div>
        </div>

        {{-- Stations timeline --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-5">خط التقديم</h3>
            <x-stepper :steps="$steps" />

            {{-- Advance actions --}}
            <div class="flex flex-wrap items-center gap-2 mt-6 pt-5 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                @switch($job->stage->value)
                    @case('wishlist')
                        <button type="button" wire:click="markApplied" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm hover:opacity-90 transition">✓ قدّمت</button>
                        @break
                    @case('applied')
                        <button type="button" wire:click="markInterview" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm hover:opacity-90 transition">وصلني رد للإنترفيو →</button>
                        <button type="button" wire:click="markRejected" class="px-4 py-2 rounded-lg bg-danger/15 text-danger text-sm hover:bg-danger/25 transition">✕ رفض</button>
                        @break
                    @case('interview')
                        <button type="button" wire:click="markOffer" class="px-4 py-2 rounded-lg bg-success text-white text-sm hover:opacity-90 transition">✓ حصلت على عرض</button>
                        <button type="button" wire:click="markRejected" class="px-4 py-2 rounded-lg bg-danger/15 text-danger text-sm hover:bg-danger/25 transition">✕ رفض</button>
                        @break
                    @default
                        <span class="text-sm text-ink-soft dark:text-ink-dark-soft">تم إغلاق هذا التقديم.</span>
                        <button type="button" wire:click="reopen" class="px-3 py-1.5 rounded-lg border border-ink-soft/20 text-ink dark:text-ink-dark text-sm hover:bg-ink-soft/5 transition">إعادة فتح</button>
                @endswitch
            </div>

            {{-- Rejection reason (inline) --}}
            @if ($job->stage->value === 'rejected')
                <div class="mt-4">
                    <x-input-label for="rej" value="سبب الرفض (من الإيميل)" />
                    <textarea id="rej" wire:model="rejectionReason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                    <button type="button" wire:click="saveRejectionReason" class="mt-2 text-xs px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">حفظ السبب</button>
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if ($job->description)
            <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-2">وصف الوظيفة</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft whitespace-pre-line">{{ $job->description }}</p>
            </div>
        @endif

        {{-- Interview prep (only once an interview is reached) --}}
        @if ($job->needsInterviewPrep())
            <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-1">الاستعداد للإنترفيو</h3>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-4">افهم طبيعة الشركة وحضّر تحضير خاص ليها.</p>

                {{-- Company research --}}
                <div class="mb-6">
                    <x-input-label for="research" value="بحث عن الشركة" />
                    <textarea id="research" wire:model="research" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="المنتج، الثقافة، آخر أخبارها، ليه عايز تشتغل معاهم…"></textarea>
                    <button type="button" wire:click="saveResearch" class="mt-2 text-xs px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">حفظ البحث</button>
                </div>

                {{-- Prep checklist --}}
                <div>
                    <x-input-label value="نقاط التحضير" />
                    <div class="flex items-center gap-2 mt-2">
                        <input type="text" wire:model="newPrepItem" wire:keydown.enter.prevent="addPrepItem" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="مثال: راجع أسئلة النظام، جهّز أسئلة ليهم…" />
                        <button type="button" wire:click="addPrepItem" class="px-3 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm hover:opacity-90">إضافة</button>
                    </div>
                    <div class="mt-3 space-y-1.5">
                        @forelse ($prepItems as $item)
                            <div wire:key="prep-{{ $item->id }}" class="flex items-center gap-3 rounded-lg bg-bg-light dark:bg-bg-dark px-3 py-2">
                                <button type="button" wire:click="togglePrepItem({{ $item->id }})" class="w-5 h-5 rounded flex items-center justify-center text-xs shrink-0 {{ $item->is_done ? 'bg-success text-white' : 'border border-ink-soft/40' }}">{{ $item->is_done ? '✓' : '' }}</button>
                                <span class="flex-1 text-sm {{ $item->is_done ? 'line-through text-ink-soft dark:text-ink-dark-soft' : 'text-ink dark:text-ink-dark' }}">{{ $item->title }}</span>
                                <button type="button" wire:click="deletePrepItem({{ $item->id }})" class="text-xs text-danger hover:underline shrink-0">حذف</button>
                            </div>
                        @empty
                            <p class="text-sm text-ink-soft dark:text-ink-dark-soft">مفيش نقاط تحضير لسه.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>

    <livewire:jobs.manage-job />
</div>
