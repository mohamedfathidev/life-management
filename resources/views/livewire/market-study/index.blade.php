<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">مذاكرة السوق</h1>
            </div>
            <button type="button" wire:click="$dispatch('create-track')" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ مسار مذاكرة</button>
        </div>

        {{-- Motivational motto --}}
        <div class="rounded-2xl bg-gradient-to-br from-primary/15 to-secondary/10 dark:from-primary-dark/20 dark:to-transparent shadow-sm p-6 mb-8">
            @if ($editingMotto)
                <textarea wire:model="motto" rows="2" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="اكتب شعارك التحفيزي…"></textarea>
                <div class="flex items-center gap-2 mt-2">
                    <button type="button" wire:click="saveMotto" class="text-xs px-3 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">حفظ</button>
                    <button type="button" wire:click="$set('editingMotto', false)" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:underline">إلغاء</button>
                </div>
            @else
                <div class="flex items-start justify-between gap-3">
                    <p class="text-lg font-medium text-ink dark:text-ink-dark whitespace-pre-line">
                        {{ $motto !== '' ? $motto : 'اكتب شعارًا يفكّرك ليه لازم تكمّل…' }}
                    </p>
                    <button type="button" wire:click="$set('editingMotto', true)" class="text-xs text-primary dark:text-primary-dark hover:underline shrink-0">تعديل</button>
                </div>
            @endif
        </div>

        @if ($tracks->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">أضف أول مسار مذاكرة (سوفتوير، مانجمنت، تدريس…).</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($tracks as $track)
                    <a href="{{ route('market-study.show', $track) }}" wire:navigate wire:key="track-{{ $track->id }}"
                       class="block rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-5 {{ $track->is_completed ? 'opacity-70' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $track->title }}</h3>
                                @if ($track->field)<p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $track->field }}</p>@endif
                            </div>
                            @if ($track->is_completed)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-success/15 text-success shrink-0">مكتمل</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-3 text-xs text-ink-soft dark:text-ink-dark-soft">
                            @if ($track->end_date)<span>🎯 ينتهي {{ $track->end_date->translatedFormat('j M Y') }}</span>@endif
                            @if ($track->certificate)<span>🏅 {{ $track->certificate }}</span>@endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <livewire:market-study.manage-track />
</div>
