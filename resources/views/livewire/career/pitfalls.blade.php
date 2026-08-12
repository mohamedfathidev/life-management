<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">⚠️ أخطاء الكارير في عصر الـ AI</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">أخطاء شائعة تتجنّبها — علّم اللي بتقع فيه عشان تشتغل عليه، وضيف دروسك أنت.</p>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-2 mb-6 text-sm">
            <button wire:click="$set('tab', 'curated')"
                class="px-4 py-2 rounded-lg font-medium transition {{ $tab === 'curated' ? 'bg-primary dark:bg-primary-dark text-white' : 'bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' }}">
                ⚠️ الأخطاء العامة
            </button>
            <button wire:click="$set('tab', 'mine')"
                class="px-4 py-2 rounded-lg font-medium transition {{ $tab === 'mine' ? 'bg-primary dark:bg-primary-dark text-white' : 'bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark' }}">
                📝 أخطائي <span class="text-xs opacity-80">({{ $lessons->count() }})</span>
            </button>
        </div>

        {{-- Curated pitfalls --}}
        @if ($tab === 'curated')
        @foreach ($sections as $section)
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-3">{{ $section['emoji'] }} {{ $section['title'] }}</h2>
                <div class="space-y-3">
                    @foreach ($section['items'] as $item)
                        @php($marked = in_array($item['key'], $markedKeys, true))
                        <div wire:key="pf-{{ $item['key'] }}"
                             class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 border-e-4 {{ $marked ? 'border-warning' : 'border-transparent' }}">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $item['title'] }}</h3>
                                <button type="button" wire:click="toggleMark('{{ $item['key'] }}')"
                                    class="shrink-0 text-xs px-3 py-1 rounded-full transition {{ $marked ? 'bg-warning/15 text-warning' : 'bg-ink-soft/10 text-ink-soft dark:text-ink-dark-soft hover:bg-ink-soft/20' }}">
                                    {{ $marked ? '✓ بقع فيه' : 'بقع فيه؟' }}
                                </button>
                            </div>
                            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2"><span class="text-danger font-medium">ليه بيأذيك:</span> {{ $item['why'] }}</p>
                            <p class="text-sm text-ink dark:text-ink-dark mt-1"><span class="text-success font-medium">الصح:</span> {{ $item['fix'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        @endif

        {{-- User's own lessons --}}
        @if ($tab === 'mine')
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-1">📝 أخطائي ودروسي</h2>
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-4">سجّل خطأ وقعت فيه أو درس اتعلّمته من تجربتك.</p>

            <form wire:submit="addLesson" class="space-y-3 mb-5">
                <div>
                    <x-text-input wire:model="newTitle" type="text" class="block w-full" placeholder="الخطأ / الدرس…" />
                    <x-input-error :messages="$errors->get('newTitle')" class="mt-1" />
                </div>
                <textarea wire:model="newBody" rows="2" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="تفاصيل / إزاي أتجنّبه (اختياري)"></textarea>
                <div class="flex items-center gap-3">
                    <select wire:model="newCategory" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="general">عام</option>
                        <option value="ai">عصر الـ AI</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">إضافة</button>
                </div>
            </form>

            @if ($lessons->isNotEmpty())
                <div class="space-y-2">
                    @foreach ($lessons as $lesson)
                        <div wire:key="lesson-{{ $lesson->id }}" class="flex items-start gap-3 rounded-lg bg-bg-light dark:bg-bg-dark p-3">
                            <button type="button" wire:click="toggleAvoided({{ $lesson->id }})" title="اتجنّبته"
                                class="shrink-0 w-5 h-5 mt-0.5 rounded-full border-2 flex items-center justify-center text-[11px] transition {{ $lesson->avoided ? 'bg-success border-success text-white' : 'border-ink-soft/40 text-transparent hover:border-success' }}">✓</button>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-ink dark:text-ink-dark {{ $lesson->avoided ? 'line-through opacity-70' : '' }}">
                                    {{ $lesson->title }}
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-{{ $lesson->category === 'ai' ? 'primary' : 'secondary' }}/15 text-ink-soft dark:text-ink-dark-soft">{{ $lesson->category === 'ai' ? 'AI' : 'عام' }}</span>
                                </p>
                                @if ($lesson->body)<p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5 whitespace-pre-line">{{ $lesson->body }}</p>@endif
                            </div>
                            <button type="button" wire:click="deleteLesson({{ $lesson->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline shrink-0">حذف</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
