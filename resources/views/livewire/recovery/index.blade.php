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
                <a href="{{ route('recovery.signature') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">✍️ توقيع التغيير</a>
                <a href="{{ route('recovery.mistakes') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">⛓️ أخطاء التعافي</a>
                <a href="{{ route('recovery.hard-moments') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">⚡ أصعب اللحظات</a>
                <a href="{{ route('recovery.ideas') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">💡 أفكار تراودني</a>
                <a href="{{ route('recovery.commitments') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">📜 حاجات لازم تلتزم بيها</a>
                <a href="{{ route('recovery.setbacks') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">💔 الانتكاسات</a>
                <a href="{{ route('recovery.damages') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">⚠️ أضرار الإدمان</a>
                <a href="{{ route('recovery.stories') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">📖 حكايات التعافي</a>
                <a href="{{ route('recovery.dreams') }}" wire:navigate class="px-4 py-2 rounded-lg bg-gradient-to-r from-primary/20 to-secondary/20 dark:from-primary-dark/25 dark:to-secondary-dark/25 text-ink dark:text-ink-dark text-sm font-medium hover:opacity-90">✨ أحلام التعافي</a>
                <a href="{{ route('recovery.changes') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">🧭 تغييرات جذرية</a>
                <a href="{{ route('recovery.topics') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">📚 تعلّم</a>
                <a href="{{ route('recovery.telegram') }}" wire:navigate class="px-4 py-2 rounded-lg bg-secondary/25 text-ink dark:text-ink-dark text-sm hover:opacity-90">📡 قناة تيليجرام</a>
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
                    @php($setbacks = $recovery->setbackCount())
                    <a href="{{ route('recovery.show', $recovery) }}" wire:navigate wire:key="rec-{{ $recovery->id }}"
                       class="block rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-5">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $recovery->title }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $recovery->status->color() }}/15 text-{{ $recovery->status->color() }}">{{ $recovery->status->label() }}</span>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-4xl font-bold text-success">{{ $recovery->currentStreakDays() }}</p>
                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">يوم نظيف متواصل</p>
                            <p class="text-[10px] text-ink-soft/80 dark:text-ink-dark-soft/80 mt-1 leading-relaxed">
                                {{ $setbacks > 0
                                    ? 'بتتعد من آخر انتكاسة ('.$recovery->streakSince()->translatedFormat('j M Y').')'
                                    : 'بتتعد من أول يوم تعافٍ ('.$recovery->start_date->translatedFormat('j M Y').')' }}
                            </p>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800/60 grid grid-cols-3 gap-1 text-center text-[11px] text-ink-soft dark:text-ink-dark-soft">
                            <div><p class="font-bold text-ink dark:text-ink-dark">{{ $recovery->periodTotalDays() }}</p><p>⏳ المدة</p></div>
                            <div><p class="font-bold text-success">{{ $recovery->cleanDaysCount() }}</p><p>🌿 نضيفة</p></div>
                            <div><p class="font-bold {{ $setbacks > 0 ? 'text-danger' : 'text-ink dark:text-ink-dark' }}">{{ $setbacks }}</p><p>💔 انتكاسة</p></div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($recoveries->hasPages())
                <div class="mt-6">{{ $recoveries->links() }}</div>
            @endif
        @endif
    </div>

    <livewire:recovery.manage-recovery />
</div>
