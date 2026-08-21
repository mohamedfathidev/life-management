<div
    x-data="{ open: @entangle('open') }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open && $wire.close()"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>

    <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">
            {{ $form->damage ? 'تعديل الضرر' : 'ضرر جديد' }}
        </h2>

        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <x-input-label for="dmg_title" value="اسم الضرر" />
                    <x-text-input id="dmg_title" wire:model="form.title" type="text" class="mt-1 block w-full" placeholder="مثال: ضرر الجسم" />
                    <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="dmg_icon" value="الرمز" />
                    <x-text-input id="dmg_icon" wire:model="form.icon" type="text" maxlength="4" class="mt-1 block w-full text-center text-xl" placeholder="🫀" />
                </div>
            </div>

            <div>
                <x-input-label for="dmg_parent" value="الضرر الأب (اتركه فارغاً لضرر رئيسي)" />
                <select id="dmg_parent" wire:model="form.parent_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">— ضرر رئيسي —</option>
                    @foreach ($mainDamages as $main)
                        <option value="{{ $main->id }}">{{ $main->title }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('form.parent_id')" class="mt-1" />
            </div>

            {{-- Degree slider --}}
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="dmg_degree" value="درجة الضرر" />
                    <span class="text-sm font-extrabold px-2.5 py-0.5 rounded-full"
                          style="color: hsl({{ max(0, 150 - $form->degree * 1.5) }}, 70%, 38%); background: hsl({{ max(0, 150 - $form->degree * 1.5) }}, 70%, 38% / 0.12);">
                        {{ $form->degree }}%
                    </span>
                </div>
                <input id="dmg_degree" type="range" min="0" max="100" step="5" wire:model.live="form.degree"
                       class="mt-2 w-full accent-red-500 cursor-pointer" />
                <div class="flex justify-between text-[10px] text-ink-soft dark:text-ink-dark-soft mt-1">
                    <span>🟢 خفيف</span><span>🟡 متوسط</span><span>🔴 خطير</span>
                </div>
                <x-input-error :messages="$errors->get('form.degree')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="dmg_desc" value="وصف الضرر (اختياري)" />
                <textarea id="dmg_desc" wire:model="form.description" rows="3"
                          class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"
                          placeholder="إزاي الضرر ده بيأثر عليك…"></textarea>
                <x-input-error :messages="$errors->get('form.description')" class="mt-1" />
            </div>

            {{-- Life without bullets --}}
            <div>
                <x-input-label for="dmg_life" value="🌱 لو الضرر ده مش موجود — سطر لكل نقطة" />
                <textarea id="dmg_life" wire:model="form.lifeWithoutInput" rows="4"
                          class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"
                          placeholder="هنبقى أصحاء ومرتاحين
هوفر فلوسي
علاقاتي هترجع أحسن"></textarea>
                <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">اكتب كل نقطة في سطر منفصل.</p>
                <x-input-error :messages="$errors->get('form.lifeWithoutInput')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
            </div>
        </form>
    </div>
</div>