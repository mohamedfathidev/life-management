<div class="py-10 px-4">
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('arena.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الساحة</a>
        <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1 mb-6">{{ $challenge ? 'تعديل التحدي' : 'تحدي جديد' }}</h1>

        <form wire:submit="save" class="space-y-6">
            {{-- Basics --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 space-y-4">
                <div>
                    <x-input-label for="sc_name" value="اسم التحدي" />
                    <x-text-input id="sc_name" wire:model="name" type="text" class="mt-1 block w-full" placeholder="مثال: تحدي رمضان" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="sc_desc" value="وصف (اختياري)" />
                    <textarea id="sc_desc" wire:model="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="sc_start" value="من تاريخ" />
                        <x-text-input id="sc_start" wire:model="start_date" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="sc_end" value="إلى تاريخ (اختياري)" />
                        <x-text-input id="sc_end" wire:model="end_date" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-1" />
                    </div>
                </div>
            </div>

            {{-- Prayer scoring --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <label class="flex items-center gap-2 font-semibold text-ink dark:text-ink-dark mb-3">
                    <input type="checkbox" wire:model="prayerEnabled" class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" />
                    🕌 نقاط الصلوات
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach (['jamaah' => 'جماعة', 'ontime' => 'في وقتها', 'prayed' => 'صلّاها', 'none' => 'لم يصلِّ'] as $k => $lbl)
                        <div>
                            <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">{{ $lbl }}</label>
                            <x-text-input wire:model="prayerPoints.{{ $k }}" type="number" min="0" class="block w-full text-center" />
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-2">النقط دي لكل صلاة من الخمسة.</p>
            </div>

            {{-- Wird scoring --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <label class="flex items-center gap-2 font-semibold text-ink dark:text-ink-dark mb-3">
                    <input type="checkbox" wire:model="wirdEnabled" class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" />
                    📖 نقاط ورد القرآن
                </label>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-ink-soft dark:text-ink-dark-soft">نقاط لكل صفحة:</label>
                    <x-text-input wire:model="wirdPerPage" type="number" min="0" class="w-24 text-center" />
                </div>
            </div>

            {{-- Extras --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-ink dark:text-ink-dark">✨ أنشطة إضافية (قيام/نوافل/أذكار…)</h3>
                    <button type="button" wire:click="addExtra" class="text-xs px-3 py-1 rounded-full bg-primary/10 text-primary dark:text-primary-dark hover:bg-primary/20">+ نشاط</button>
                </div>
                <div class="space-y-2">
                    @foreach ($extras as $i => $extra)
                        <div wire:key="ex-{{ $i }}" class="flex items-center gap-2">
                            <x-text-input wire:model="extras.{{ $i }}.label" type="text" class="flex-1" placeholder="اسم النشاط" />
                            <x-text-input wire:model="extras.{{ $i }}.points" type="number" min="0" class="w-20 text-center" placeholder="نقط" />
                            <button type="button" wire:click="removeExtra({{ $i }})" class="text-danger text-xs px-2 hover:underline">حذف</button>
                        </div>
                    @endforeach
                    @error('extras.*.label') <p class="text-xs text-danger">كل نشاط لازم له اسم.</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('arena.index') }}" wire:navigate class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $challenge ? 'حفظ' : 'إنشاء التحدي' }}</button>
            </div>
        </form>
    </div>
</div>
