<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <div>
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">🏥 الصحة</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">أكلك، نومك، هاتفك — والأضرار اللي بتتراكم من غير ما تحس.</p>
        </div>

        {{-- Today's checklist --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h2 class="font-semibold text-ink dark:text-ink-dark mb-4">✅ النهاردة</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button type="button" wire:click="toggleToday('healthy_eating')"
                    class="flex items-center gap-3 rounded-xl p-4 text-start transition {{ $today?->healthy_eating ? 'bg-success/15 border border-success/30' : 'bg-bg-light dark:bg-bg-dark border border-ink-soft/15 dark:border-ink-dark-soft/15' }}">
                    <span class="text-2xl">🥗</span>
                    <div>
                        <p class="text-sm font-medium text-ink dark:text-ink-dark">أكلت أكل صحي</p>
                        <p class="text-[11px] {{ $today?->healthy_eating ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">{{ $today?->healthy_eating ? 'تم ✓' : 'لسه' }}</p>
                    </div>
                </button>
                <button type="button" wire:click="toggleToday('slept_early')"
                    class="flex items-center gap-3 rounded-xl p-4 text-start transition {{ $today?->slept_early ? 'bg-success/15 border border-success/30' : 'bg-bg-light dark:bg-bg-dark border border-ink-soft/15 dark:border-ink-dark-soft/15' }}">
                    <span class="text-2xl">🌙</span>
                    <div>
                        <p class="text-sm font-medium text-ink dark:text-ink-dark">نمت بدري</p>
                        <p class="text-[11px] {{ $today?->slept_early ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">{{ $today?->slept_early ? 'تم ✓' : 'لسه' }}</p>
                    </div>
                </button>
                <button type="button" wire:click="toggleToday('woke_early')"
                    class="flex items-center gap-3 rounded-xl p-4 text-start transition {{ $today?->woke_early ? 'bg-success/15 border border-success/30' : 'bg-bg-light dark:bg-bg-dark border border-ink-soft/15 dark:border-ink-dark-soft/15' }}">
                    <span class="text-2xl">☀️</span>
                    <div>
                        <p class="text-sm font-medium text-ink dark:text-ink-dark">صحيت بدري</p>
                        <p class="text-[11px] {{ $today?->woke_early ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">{{ $today?->woke_early ? 'تم ✓' : 'لسه' }}</p>
                    </div>
                </button>
                <button type="button" wire:click="toggleToday('phone_away_sleep')"
                    class="flex items-center gap-3 rounded-xl p-4 text-start transition {{ $today?->phone_away_sleep ? 'bg-success/15 border border-success/30' : 'bg-bg-light dark:bg-bg-dark border border-ink-soft/15 dark:border-ink-dark-soft/15' }}">
                    <span class="text-2xl">📵</span>
                    <div>
                        <p class="text-sm font-medium text-ink dark:text-ink-dark">بعّدت التليفون وقت النوم</p>
                        <p class="text-[11px] {{ $today?->phone_away_sleep ? 'text-success' : 'text-ink-soft dark:text-ink-dark-soft' }}">{{ $today?->phone_away_sleep ? 'تم ✓' : 'لسه' }}</p>
                    </div>
                </button>
            </div>
        </div>

        {{-- Unhealthy purchase streak --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h2 class="font-semibold text-ink dark:text-ink-dark mb-1">🍟 آخر مرة اشتريت حاجة غير صحية</h2>
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-4">زي شيبسي أو اندومي — من المحل</p>

            <div class="text-center mb-5">
                @if ($daysSincePurchase === null)
                    <p class="text-ink-soft dark:text-ink-dark-soft text-sm">لسه ماسجّلتش حاجة — يبقى ابدأ تتبّع من دلوقتي.</p>
                @else
                    <p class="text-4xl font-black text-success dark:text-success-dark">{{ $daysSincePurchase }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">{{ $daysSincePurchase === 1 ? 'يوم واحد' : 'يوم' }} من غير أكل غير صحي</p>
                @endif
            </div>

            <form wire:submit="logPurchase" class="grid grid-cols-1 sm:grid-cols-[1fr_auto_auto] gap-2 mb-4">
                <x-text-input wire:model="purchaseItem" type="text" placeholder="إيه اللي اشتريته؟ (شيبسي، اندومي...)" class="block w-full" />
                <input type="date" wire:model="purchaseDate" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-danger focus:ring-danger text-sm" />
                <button type="submit" class="px-4 py-2 rounded-lg bg-danger text-white text-sm font-medium hover:opacity-90 transition whitespace-nowrap">سجّل</button>
            </form>
            <x-input-error :messages="$errors->get('purchaseItem')" class="mb-2" />
            <x-input-error :messages="$errors->get('purchaseDate')" class="mb-2" />

            @if ($purchases->isNotEmpty())
                <div class="space-y-1.5">
                    @foreach ($purchases as $p)
                        <div wire:key="pu-{{ $p->id }}" class="flex items-center justify-between gap-3 text-xs text-ink-soft dark:text-ink-dark-soft bg-bg-light dark:bg-bg-dark rounded-lg px-3 py-2">
                            <span>{{ $p->item }} — {{ $p->date->translatedFormat('j M Y') }}</span>
                            <button type="button" wire:click="deletePurchase({{ $p->id }})" class="text-danger hover:underline shrink-0">حذف</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Health harms, ranked --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <h2 class="font-semibold text-ink dark:text-ink-dark mb-1">⚠️ أكبر الحاجات خطر على صحتك</h2>
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-4">مرتبة من الأخطر للأقل</p>

            <div class="space-y-3 mb-5">
                @forelse ($harms as $harm)
                    <div wire:key="hh-{{ $harm->id }}" class="rounded-xl bg-bg-light dark:bg-bg-dark p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-medium text-ink dark:text-ink-dark">{{ $harm->title }}</p>
                            <span class="text-xs font-bold shrink-0" style="color: hsl({{ $harm->hue() }}, 60%, 45%)">{{ $harm->severity }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-ink-soft/15 dark:bg-ink-dark-soft/15 overflow-hidden mt-2">
                            <div class="h-full rounded-full" style="width: {{ $harm->severity }}%; background: hsl({{ $harm->hue() }}, 60%, 45%)"></div>
                        </div>
                        @if ($harm->note)<p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2">{{ $harm->note }}</p>@endif
                        <div class="flex items-center gap-3 mt-2">
                            <button type="button" wire:click="editHarm({{ $harm->id }})" class="text-[11px] text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="deleteHarm({{ $harm->id }})" wire:confirm="حذف الضرر ده؟" class="text-[11px] text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-4">لسه مفيش أضرار مسجّلة.</p>
                @endforelse
            </div>

            <form wire:submit="saveHarm" class="space-y-3 pt-4 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                <p class="text-xs font-semibold text-ink-soft dark:text-ink-dark-soft">{{ $editingHarmId ? 'عدّل الضرر' : 'ضيف ضرر جديد' }}</p>
                <x-text-input wire:model="harmTitle" type="text" placeholder="الضرر (مثلاً: السهر لحد الفجر)" class="block w-full" />
                <x-input-error :messages="$errors->get('harmTitle')" class="mt-1" />
                <textarea wire:model="harmNote" rows="2" placeholder="ملاحظة (اختياري)"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                <div>
                    <label class="text-xs text-ink-soft dark:text-ink-dark-soft">درجة الخطورة: {{ $harmSeverity }}%</label>
                    <input type="range" wire:model="harmSeverity" min="0" max="100" class="block w-full accent-danger" />
                </div>
                <div class="flex items-center justify-end gap-3">
                    @if ($editingHarmId)
                        <button type="button" wire:click="resetHarmForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    @endif
                    <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">
                        {{ $editingHarmId ? 'احفظ' : '+ إضافة' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Protective habits summary --}}
        <div class="rounded-2xl bg-gradient-to-br from-success/10 to-primary/10 dark:from-success-dark/10 dark:to-primary-dark/10 p-6">
            <h2 class="font-semibold text-ink dark:text-ink-dark mb-3">🛡️ أكتر الحاجات اللي بتحافظ عليك</h2>
            <ul class="space-y-2 text-sm text-ink dark:text-ink-dark">
                <li class="flex items-center gap-2"><span class="text-success">✓</span> النوم بدري</li>
                <li class="flex items-center gap-2"><span class="text-success">✓</span> الصحيان بدري</li>
                <li class="flex items-center gap-2"><span class="text-success">✓</span> إبعاد التليفون عنك أثناء النوم</li>
            </ul>
            <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-3">دول موجودين في تشيك ليست "النهاردة" فوق — علّم عليهم كل يوم عشان تفضل متابع نفسك.</p>
        </div>
    </div>
</div>
