<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ $backRoute }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← {{ $backLabel }}</a>
        <div class="mt-1 mb-5">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">🔖 {{ $title_heading }}</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">مواقع وأشخاص وقنوات معروفة تدوّر فيها عن الفرص.</p>
        </div>

        {{-- Add / edit --}}
        <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 mb-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <x-text-input wire:model="title" type="text" class="block w-full" placeholder="العنوان (مثال: Opportunity Desk)" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>
                <select wire:model="type" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    @foreach ($types as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-text-input wire:model="url" type="url" class="block w-full" placeholder="https://… (أو ارفع صورة تحت)" dir="ltr" />
                <x-input-error :messages="$errors->get('url')" class="mt-1" />
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm text-primary dark:text-primary-dark cursor-pointer">
                    🖼️ <span class="hover:underline">إضافة صورة كمصدر (اختياري)</span>
                    <input type="file" wire:model="image" class="hidden" accept="image/*" />
                </label>
                <div wire:loading wire:target="image" class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">جارِ الرفع…</div>
                @if ($image)
                    <div class="mt-2"><img src="{{ $image->temporaryUrl() }}" class="h-24 rounded-lg object-cover" alt="preview" /></div>
                @endif
                <x-input-error :messages="$errors->get('image')" class="mt-1" />
            </div>
            <x-text-input wire:model="note" type="text" class="block w-full" placeholder="ملاحظة (اختياري)" />
            <div class="flex items-center justify-end gap-3">
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                @endif
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة مصدر' }}</button>
            </div>
        </form>

        {{-- Filter --}}
        @if ($resources->isNotEmpty() || $typeFilter !== '')
            <div class="flex items-center gap-2 mb-4">
                <select wire:model.live="typeFilter" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    <option value="">كل الأنواع</option>
                    @foreach ($types as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- List --}}
        @if ($resources->isEmpty())
            <div class="text-center py-12 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🔖</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">ابدأ بإضافة مصادرك.</p>
                <button type="button" wire:click="addSuggested" class="mt-3 text-xs text-primary dark:text-primary-dark hover:underline">✨ أضف مصادر مقترحة معروفة</button>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($resources as $r)
                    <div wire:key="res-{{ $r->id }}" class="flex items-start gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                        @if ($r->hasImage())
                            <a href="{{ route('career.resources.image', $r) }}" target="_blank" rel="noopener" class="shrink-0">
                                <img src="{{ route('career.resources.image', $r) }}" class="w-16 h-16 rounded-lg object-cover ring-1 ring-ink-soft/10" alt="{{ $r->title }}" />
                            </a>
                        @endif
                        <div class="flex-1 min-w-0">
                            @if ($r->url)
                                <a href="{{ $r->url }}" target="_blank" rel="noopener" class="text-sm font-semibold text-primary dark:text-primary-dark hover:underline flex items-center gap-2">
                                    {{ $r->title }}
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            @elseif ($r->hasImage())
                                <a href="{{ route('career.resources.image', $r) }}" target="_blank" rel="noopener" class="text-sm font-semibold text-primary dark:text-primary-dark hover:underline">{{ $r->title }}</a>
                            @else
                                <span class="text-sm font-semibold text-ink dark:text-ink-dark">{{ $r->title }}</span>
                            @endif
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                                <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $types[$r->type] ?? $r->type }}</span>
                                @if ($r->url)<span dir="ltr" class="truncate max-w-[200px]">{{ $r->url }}</span>@endif
                            </div>
                            @if ($r->note)<p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">{{ $r->note }}</p>@endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" wire:click="edit({{ $r->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                            <button type="button" wire:click="delete({{ $r->id }})" wire:confirm="حذف المصدر؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
