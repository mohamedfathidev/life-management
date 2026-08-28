<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-2">🌠 أحلام الكارير</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">الحلم، حالته، ووصلت لأيه فيه — بس كده.</p>
        </div>

        {{-- Glance row --}}
        @if ($dreams->isNotEmpty())
            <div class="flex items-start gap-5 overflow-x-auto pb-2 mb-8">
                @foreach ($dreams as $dream)
                    <div wire:key="glance-{{ $dream->id }}" class="shrink-0 flex flex-col items-center gap-2 w-20">
                        <div @class([
                            'w-16 h-16 rounded-full flex items-center justify-center text-xl shrink-0',
                            'bg-success text-white' => $dream->status->value === 'achieved',
                            'bg-primary dark:bg-primary-dark text-white' => $dream->status->value === 'pursuing',
                            'bg-transparent border-2 border-ink-soft/30 dark:border-ink-dark-soft/30 text-ink-soft dark:text-ink-dark-soft' => $dream->status->value === 'dreaming',
                        ])>
                            {{ $dream->status->emoji() }}
                        </div>
                        <p class="text-[11px] text-center text-ink dark:text-ink-dark leading-snug line-clamp-2">{{ $dream->title }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Add / edit form --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 mb-8">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">{{ $editingId ? 'عدّل الحلم' : 'ضيف حلم جديد' }}</h3>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <x-input-label for="cd_title" value="الحلم" />
                    <x-text-input id="cd_title" wire:model="title" type="text" class="mt-1 block w-full" placeholder="مثال: الحصول على أول وظيفة" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="cd_status" value="الحالة" />
                    <select id="cd_status" wire:model="status" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="dreaming">💭 لسه حلم</option>
                        <option value="pursuing">🚀 بشتغل عليها</option>
                        <option value="achieved">✓ تحققت</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="cd_progress" value="وصلت لأيه فيها؟ (اختياري)" />
                    <textarea id="cd_progress" wire:model="progressNote" rows="2"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="مثلاً: قدّمت في 3 شركات ولسه مستني ردود"></textarea>
                    <x-input-error :messages="$errors->get('progressNote')" class="mt-1" />
                </div>

                <div class="flex items-center justify-end gap-3">
                    @if ($editingId)
                        <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    @endif
                    <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">
                        {{ $editingId ? 'احفظ التعديل' : '+ إضافة' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- List --}}
        @if ($dreams->isEmpty())
            <div class="text-center py-16 rounded-3xl border-2 border-dashed border-primary/25 dark:border-primary-dark/25">
                <p class="text-5xl mb-3">🌠</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش أحلام كارير — ضيف أول واحدة فوق.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($dreams as $dream)
                    <div wire:key="row-{{ $dream->id }}" class="flex items-start gap-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                        <span @class([
                            'shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full',
                            'bg-success/15 text-success' => $dream->status->value === 'achieved',
                            'bg-primary/15 text-primary dark:text-primary-dark' => $dream->status->value === 'pursuing',
                            'bg-ink-soft/10 text-ink-soft dark:text-ink-dark-soft' => $dream->status->value === 'dreaming',
                        ])>
                            {{ $dream->status->emoji() }} {{ $dream->status->label() }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p @class(['text-sm font-medium text-ink dark:text-ink-dark', 'line-through opacity-70' => $dream->status->value === 'achieved'])>{{ $dream->title }}</p>
                            @if ($dream->progress_note)
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">{{ $dream->progress_note }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="edit({{ $dream->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="delete({{ $dream->id }})" wire:confirm="حذف الحلم ده؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
