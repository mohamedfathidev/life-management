<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">الإحصائيات</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">مراجعات الأهداف الكبيرة ونسبة التحسن عبر الزمن.</p>
            </div>
            @if ($averageImprovement !== null)
                <div class="text-center rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm px-5 py-3">
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">متوسط التحسن</p>
                    <p class="text-2xl font-bold text-primary dark:text-primary-dark">{{ $averageImprovement }}%</p>
                </div>
            @endif
        </div>

        {{-- Improvement trend chart --}}
        @if (count($chartPoints) >= 2)
            <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-ink dark:text-ink-dark mb-3">تطوّر نسبة التحسن</h3>
                <x-line-chart :points="$chartPoints" />
            </div>
        @endif

        @forelse ($rows as $row)
            @php($review = $row['review'])
            <div wire:key="review-{{ $review->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mb-4 border-t-4" style="border-top-color: {{ $review->goal->color }}">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div>
                        <a href="{{ route('goals.show', $review->goal) }}" wire:navigate class="font-semibold text-ink dark:text-ink-dark hover:text-primary dark:hover:text-primary-dark transition">
                            {{ $review->goal->title }}
                        </a>
                        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">أُغلق في {{ $review->closed_on->translatedFormat('j M Y') }}</p>
                    </div>

                    {{-- Improvement + colored comparison vs previous goal --}}
                    <div class="text-center">
                        <p @class([
                            'text-2xl font-bold',
                            'text-success' => $row['trend'] === 'up',
                            'text-danger' => $row['trend'] === 'down',
                            'text-ink dark:text-ink-dark' => in_array($row['trend'], ['same', 'first']),
                        ])>
                            {{ $review->improvement_percent }}%
                        </p>
                        @if ($row['trend'] === 'up')
                            <p class="text-xs text-success">▲ أعلى بـ {{ $row['delta'] }}% عن السابق</p>
                        @elseif ($row['trend'] === 'down')
                            <p class="text-xs text-danger">▼ أقل بـ {{ abs($row['delta']) }}% عن السابق</p>
                        @elseif ($row['trend'] === 'same')
                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft">= زي السابق</p>
                        @else
                            <p class="text-xs text-ink-soft dark:text-ink-dark-soft">أول هدف مُغلق</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="text-sm font-medium text-danger mb-1">التقصيرات</p>
                        <ul class="list-disc ps-5 space-y-0.5 text-sm text-ink-soft dark:text-ink-dark-soft">
                            @forelse ($review->shortcomings ?? [] as $point)
                                <li>{{ $point }}</li>
                            @empty
                                <li class="list-none">—</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-success mb-1">المميزات</p>
                        <ul class="list-disc ps-5 space-y-0.5 text-sm text-ink-soft dark:text-ink-dark-soft">
                            @forelse ($review->strengths ?? [] as $point)
                                <li>{{ $point }}</li>
                            @empty
                                <li class="list-none">—</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">لا توجد أهداف مُغلقة بعد. أغلق هدفًا كبيرًا في نهاية مدته لتظهر إحصائياته هنا.</p>
            </div>
        @endforelse
    </div>
</div>
