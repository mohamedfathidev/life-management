<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">التعافي</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تتبّع رحلتك بلطف وبدون قسوة على النفس.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('recovery.nutrition') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">🧠 التغذية الذهنية</a>
                <a href="{{ route('recovery.pledge') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">🤝 تعهد أمام الله</a>
                <a href="{{ route('recovery.topics') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">📚 تعلّم</a>
                <button type="button" wire:click="$dispatch('create-recovery')" class="inline-flex items-center gap-2 rounded-lg bg-primary dark:bg-primary-dark px-4 py-2 text-white text-sm font-medium shadow-sm hover:opacity-90 transition">
                    + تعافٍ جديد
                </button>
            </div>
        </div>

        @if ($recoveries->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">ابدأ رحلة تعافٍ جديدة عشان تتابع تقدّمك.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($recoveries as $recovery)
                    <a href="{{ route('recovery.show', $recovery) }}" wire:navigate wire:key="rec-{{ $recovery->id }}"
                       class="block rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-5">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $recovery->title }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $recovery->status->color() }}/15 text-{{ $recovery->status->color() }}">{{ $recovery->status->label() }}</span>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-4xl font-bold text-success">{{ $recovery->currentStreakDays() }}</p>
                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">يوم نظيف متواصل</p>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs text-ink-soft dark:text-ink-dark-soft">
                            <span>منذ {{ $recovery->streakSince()->translatedFormat('j M Y') }}</span>
                            @if ($recovery->setbackCount() > 0)<span>{{ $recovery->setbackCount() }} انتكاسة</span>@endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:recovery.manage-recovery />
</div>
