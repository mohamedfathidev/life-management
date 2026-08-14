<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">المواعيد</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">مواعيدك الجاية — إنترفيو، مشوار، أو أي حاجة مهمة.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="goToMonth(-1)" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">الشهر السابق ›</button>
                <button type="button" wire:click="goToday" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">النهاردة</button>
                <button type="button" wire:click="goToMonth(1)" class="px-3 py-1.5 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm hover:opacity-90">‹ الشهر التالي</button>
                <button type="button" wire:click="openCreate()" class="px-4 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">+ موعد</button>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
            <h2 class="text-center font-semibold text-ink dark:text-ink-dark mb-4">{{ $monthLabel }}</h2>

            <div class="grid grid-cols-7 gap-1" dir="rtl">
                @foreach ($weekDays as $wd)
                    <div class="text-center text-xs font-medium text-ink-soft dark:text-ink-dark-soft py-1">{{ $wd }}</div>
                @endforeach

                @foreach ($weeks as $week)
                    @foreach ($week as $cell)
                        <div wire:key="cell-{{ $cell['date'] }}"
                             class="min-h-[84px] rounded-lg p-1.5 border transition
                                {{ $cell['inMonth'] ? 'bg-bg-light dark:bg-bg-dark border-transparent' : 'bg-transparent border-transparent opacity-40' }}
                                {{ $cell['isToday'] ? 'ring-1 ring-primary dark:ring-primary-dark' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs {{ $cell['isToday'] ? 'font-bold text-primary dark:text-primary-dark' : 'text-ink-soft dark:text-ink-dark-soft' }}">{{ $cell['day'] }}</span>
                                <button type="button" wire:click="openCreate('{{ $cell['date'] }}')" class="text-ink-soft/50 dark:text-ink-dark-soft/50 hover:text-primary dark:hover:text-primary-dark text-xs leading-none">+</button>
                            </div>
                            <div class="mt-1 space-y-1">
                                @foreach ($cell['events']->take(3) as $ev)
                                    <button type="button" wire:click="editAppointment({{ $ev->id }})" wire:key="ev-{{ $ev->id }}"
                                            class="w-full text-right truncate rounded px-1 py-0.5 text-[10px] text-white"
                                            style="background: {{ $ev->type->hex() }}"
                                            title="{{ $ev->title }}">
                                        @if ($ev->timeLabel())<span dir="ltr">{{ $ev->timeLabel() }}</span> @endif{{ $ev->title }}
                                    </button>
                                @endforeach
                                @if ($cell['events']->count() > 3)
                                    <span class="block text-[10px] text-ink-soft dark:text-ink-dark-soft px-1">+{{ $cell['events']->count() - 3 }} أكثر</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Upcoming list --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h3 class="font-semibold text-ink dark:text-ink-dark mb-4">القادمة</h3>
            @forelse ($upcoming as $ev)
                <div wire:key="up-{{ $ev->id }}" class="flex items-start justify-between gap-3 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0">
                    <div class="flex items-start gap-3 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0" style="background: {{ $ev->type->hex() }}"></span>
                        <div class="min-w-0">
                            <p class="text-sm text-ink dark:text-ink-dark flex items-center gap-2 flex-wrap">
                                {{ $ev->type->emoji() }} {{ $ev->title }}
                                <span class="text-xs px-2 py-0.5 rounded-full bg-{{ $ev->type->color() }}/15 text-{{ $ev->type->color() }}">{{ $ev->type->label() }}</span>
                            </p>
                            <div class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">
                                {{ $ev->date->translatedFormat('l، j M Y') }}@if ($ev->timeLabel()) · <span dir="ltr">{{ $ev->timeLabel() }}</span>@endif
                                @if ($ev->location) · 📍 {{ $ev->location }}@endif
                            </div>
                            @if ($ev->note)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">{{ $ev->note }}</p>@endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ $ev->googleCalendarUrl() }}" target="_blank" rel="noopener" title="أضف لجوجل كاليندر" class="text-xs text-primary dark:text-primary-dark hover:underline">📅 جوجل</a>
                        <button type="button" wire:click="toggleDone({{ $ev->id }})" class="text-xs text-success hover:underline">تم ✓</button>
                        <button type="button" wire:click="editAppointment({{ $ev->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                        <button type="button" wire:click="deleteAppointment({{ $ev->id }})" wire:confirm="حذف الموعد؟" class="text-xs text-danger hover:underline">حذف</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-8">مفيش مواعيد قادمة.</p>
            @endforelse
        </div>

        {{-- Past / done --}}
        @if ($past->isNotEmpty())
            <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6"
                 x-data="{ open: false }">
                <button type="button" @click="open = ! open" class="w-full flex items-center justify-between">
                    <h3 class="font-semibold text-ink dark:text-ink-dark">المنتهية / السابقة</h3>
                    <svg class="w-4 h-4 text-ink-soft transition" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak class="mt-3">
                    @foreach ($past as $ev)
                        <div wire:key="past-{{ $ev->id }}" class="flex items-start justify-between gap-3 py-3 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0 opacity-75">
                            <div class="min-w-0">
                                <p class="text-sm text-ink dark:text-ink-dark {{ $ev->is_done ? 'line-through' : '' }}">
                                    {{ $ev->type->emoji() }} {{ $ev->title }}
                                    @if ($ev->is_done)<span class="text-xs text-success">تمّ ✓</span>@endif
                                </p>
                                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">{{ $ev->date->translatedFormat('j M Y') }}@if ($ev->timeLabel()) · <span dir="ltr">{{ $ev->timeLabel() }}</span>@endif</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if ($ev->is_done)
                                    <button type="button" wire:click="toggleDone({{ $ev->id }})" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:underline">تراجع</button>
                                @endif
                                <button type="button" wire:click="deleteAppointment({{ $ev->id }})" wire:confirm="حذف الموعد؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('open') }" x-show="open" x-cloak @keydown.escape.window="open && $wire.close()" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>
        <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->appointment ? 'تعديل الموعد' : 'موعد جديد' }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <x-input-label for="ap_title" value="العنوان" />
                    <x-text-input id="ap_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: إنترفيو شركة X" />
                    <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="ap_type" value="النوع" />
                        <select id="ap_type" wire:model="form.type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}">{{ $type->emoji() }} {{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="ap_date" value="التاريخ" />
                        <x-text-input id="ap_date" wire:model="form.date" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('form.date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="ap_time" value="الوقت" />
                        <x-text-input id="ap_time" wire:model="form.time" type="time" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('form.time')" class="mt-1" />
                    </div>
                </div>
                <div>
                    <x-input-label for="ap_loc" value="المكان (اختياري)" />
                    <x-text-input id="ap_loc" wire:model="form.location" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="ap_note" value="ملاحظة (اختياري)" />
                    <textarea id="ap_note" wire:model="form.note" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
