<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">خارج زون الأمان</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تجارب أول مرة وتحديات صعبة تطلّعك من منطقة راحتك.</p>
            </div>
            <div class="flex items-center gap-3">
                @if ($doneCount > 0)
                    <div class="text-center rounded-xl bg-gradient-to-br from-success/15 to-success/5 dark:from-success/20 dark:to-transparent shadow-sm px-4 py-2">
                        <p class="text-xl font-bold text-success">{{ $doneCount }}</p>
                        <p class="text-[10px] text-ink-soft dark:text-ink-dark-soft">مرة خرجت من الزون</p>
                    </div>
                @endif
                <button type="button" wire:click="openCreate" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ تجربة</button>
            </div>
        </div>

        {{-- Ongoing --}}
        <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">عايز أجرّبها</h2>
        @forelse ($ongoing as $exp)
            <div wire:key="exp-{{ $exp->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 mb-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-ink dark:text-ink-dark flex items-center gap-2 flex-wrap">
                            <span>{{ $exp->kind->emoji() }}</span> {{ $exp->title }}
                            <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $exp->status->color() }}/15 text-{{ $exp->status->color() }}">{{ $exp->status->label() }}</span>
                        </h3>
                        <div class="flex items-center gap-3 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                            <span>{{ $exp->kind->label() }}</span>
                            @if ($exp->difficulty)<span>· 🔥 الصعوبة {{ $exp->difficulty }}/5</span>@endif
                            @if ($exp->target_date)<span>· 🎯 {{ $exp->target_date->translatedFormat('j M Y') }}</span>@endif
                        </div>
                        @if ($exp->fear)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">😰 اللي حابسني: {{ $exp->fear }}</p>@endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="markDone({{ $exp->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-success text-white hover:opacity-90">✓ تمّت</button>
                        <button type="button" wire:click="edit({{ $exp->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="delete({{ $exp->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mb-6">مفيش تجارب في القائمة. أضف تجربة تطلّعك من الزون.</p>
        @endforelse

        {{-- Done --}}
        <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mt-8 mb-3">تمّت</h2>
        @forelse ($done as $exp)
            <div wire:key="expd-{{ $exp->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 mb-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-ink dark:text-ink-dark flex items-center gap-2 flex-wrap">
                            <span>{{ $exp->kind->emoji() }}</span> {{ $exp->title }}
                            <span class="text-xs px-2 py-0.5 rounded-full bg-success/15 text-success">تمّت</span>
                        </h3>
                        <div class="flex items-center gap-3 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft flex-wrap">
                            @if ($exp->difficulty)<span>🔥 {{ $exp->difficulty }}/5</span>@endif
                            @if ($exp->done_on)<span>· {{ $exp->done_on->translatedFormat('j M Y') }}</span>@endif
                        </div>
                        @if ($exp->reflection)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">💡 {{ $exp->reflection }}</p>@endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" wire:click="edit({{ $exp->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="delete({{ $exp->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft">لسه مخرجتش من الزون — أول تجربة بتكون أصعب واحدة 💪</p>
        @endforelse
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('open') }" x-show="open" x-cloak @keydown.escape.window="open && $wire.close()" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>
        <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->experience ? 'تعديل التجربة' : 'تجربة جديدة' }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <x-input-label for="ex_title" value="التجربة" />
                    <x-text-input id="ex_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: أتكلم قدّام جمهور" />
                    <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="ex_kind" value="النوع" />
                        <select id="ex_kind" wire:model="form.kind" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                            @foreach ($kinds as $kind)
                                <option value="{{ $kind->value }}">{{ $kind->emoji() }} {{ $kind->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="ex_status" value="الحالة" />
                        <select id="ex_status" wire:model="form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="ex_diff" value="الصعوبة (١–٥)" />
                        <x-text-input id="ex_diff" wire:model="form.difficulty" type="number" min="1" max="5" class="mt-1 block w-full" />
                    </div>
                </div>
                <div>
                    <x-input-label for="ex_target" value="تاريخ مستهدف (اختياري)" />
                    <x-text-input id="ex_target" wire:model="form.target_date" type="date" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="ex_fear" value="الخوف / اللي حابسني (اختياري)" />
                    <textarea id="ex_fear" wire:model="form.fear" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
                <div>
                    <x-input-label for="ex_ref" value="انعكاس / اللي اتعلمته (بعد ما تعملها)" />
                    <textarea id="ex_ref" wire:model="form.reflection" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
