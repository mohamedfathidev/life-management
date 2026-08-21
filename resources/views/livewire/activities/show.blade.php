<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('activities.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← أنشطة ومشاركات</a>

        <div class="mt-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-ink dark:text-ink-dark flex items-center gap-2">
                        <span>{{ $activity->type->emoji() }}</span>
                        <span>{{ $activity->title }}</span>
                    </h1>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $activity->type->label() }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $activity->stage->color() }}/15 text-{{ $activity->stage->color() }} shrink-0">{{ $activity->stage->label() }}</span>
            </div>

            {{-- Pipeline --}}
            <div class="mt-6 overflow-x-auto">
                <x-stepper :steps="\App\Enums\ActivityStage::steps($activity->stage, $activity->start_date)" class="min-w-[320px]" />
            </div>

            {{-- Meta --}}
            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                @if ($activity->organizer)
                    <div><dt class="text-xs text-ink-soft dark:text-ink-dark-soft">الجهة المنظمة</dt><dd class="text-ink dark:text-ink-dark mt-0.5">{{ $activity->organizer }}</dd></div>
                @endif
                @if ($activity->location)
                    <div><dt class="text-xs text-ink-soft dark:text-ink-dark-soft">المكان</dt><dd class="text-ink dark:text-ink-dark mt-0.5">📍 {{ $activity->location }}</dd></div>
                @endif
                @if ($activity->start_date)
                    <div><dt class="text-xs text-ink-soft dark:text-ink-dark-soft">التاريخ</dt><dd class="text-ink dark:text-ink-dark mt-0.5">📅 {{ $activity->start_date->translatedFormat('j M Y') }}@if ($activity->end_date && ! $activity->end_date->isSameDay($activity->start_date)) → {{ $activity->end_date->translatedFormat('j M Y') }}@endif</dd></div>
                @endif
                @if ($activity->goal)
                    <div><dt class="text-xs text-ink-soft dark:text-ink-dark-soft">مربوط بهدف</dt><dd class="text-ink dark:text-ink-dark mt-0.5">🎯 {{ $activity->goal->title }}</dd></div>
                @endif
                @if ($activity->url)
                    <div class="col-span-2"><dt class="text-xs text-ink-soft dark:text-ink-dark-soft">الرابط</dt><dd class="mt-0.5"><a href="{{ $activity->url }}" target="_blank" rel="noopener" class="text-primary dark:text-primary-dark hover:underline break-all" dir="ltr">{{ $activity->url }}</a></dd></div>
                @endif
            </dl>

            @if ($activity->result)
                <div class="mt-4 text-sm rounded-lg bg-success/10 text-success px-3 py-2">🎉 النتيجة: {{ $activity->result }}</div>
            @endif

            @if ($activity->reasons)
                <div class="mt-4">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-1">أسباب المشاركة</p>
                    <ul class="list-disc ps-5 space-y-0.5 text-sm text-ink dark:text-ink-dark">
                        @foreach ($activity->reasons as $reason)
                            <li>{{ $reason }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($activity->notes)
                <div class="mt-4">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-1">ملاحظات</p>
                    <p class="text-sm text-ink dark:text-ink-dark whitespace-pre-line">{{ $activity->notes }}</p>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2 mt-6 pt-4 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                @if ($activity->stage->next())
                    <button type="button" wire:click="advance" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">
                        تقدّم → {{ $activity->stage->next()->label() }}
                    </button>
                @endif
                @unless ($activity->isClosed())
                    <button type="button" wire:click="markRejected" class="px-3 py-2 rounded-lg border border-danger/30 text-danger text-sm hover:bg-danger/10 transition">
                        {{ $activity->stage === \App\Enums\ActivityStage::Interview ? '❌ متوفقتش في الإنترفيو' : '❌ اترفضت / اعتذرت' }}
                    </button>
                @else
                    <button type="button" wire:click="reopen" class="px-3 py-2 rounded-lg border border-ink-soft/30 text-ink-soft dark:text-ink-dark-soft text-sm hover:bg-ink-soft/10 transition">إعادة فتح</button>
                @endunless

                <div class="flex-1"></div>
                <button type="button" wire:click="editActivity" class="px-3 py-2 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">تعديل</button>
                <button type="button" wire:click="delete" wire:confirm="حذف النشاط؟" class="px-3 py-2 rounded-lg border border-danger/40 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
            </div>

            {{-- Feedback after rejection --}}
            @if ($activity->stage === \App\Enums\ActivityStage::Rejected)
                <div class="mt-5 pt-5 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                    <label class="block text-sm font-medium text-ink dark:text-ink-dark mb-1">📝 تعليقك والفيدباك</label>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-2">اكتب رأيك في الإنترفيو، والفيدباك اللي اتقال لك، وإيه اللي تحسّنه المرة الجاية.</p>
                    <textarea wire:model="feedback" rows="4" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="مثال: كنت متوتر في أول سؤال… الفيدباك: محتاج أقوّي الـ system design…"></textarea>
                    <div class="flex items-center justify-end gap-3 mt-2">
                        <span x-data="{ show: false }" x-on:activity-feedback-saved.window="show = true; setTimeout(() => show = false, 1500)" x-show="show" x-cloak class="text-xs text-success">تم الحفظ ✓</span>
                        <button type="button" wire:click="saveFeedback" class="px-4 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white text-xs font-medium hover:opacity-90 transition">حفظ الفيدباك</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <livewire:activities.manage-activity />
</div>
