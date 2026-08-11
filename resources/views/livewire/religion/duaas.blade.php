<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('religion') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الدين</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">الأدعية</h1>
            </div>
            <button type="button" wire:click="openCreate" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ دعاء</button>
        </div>

        @if ($allTags->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <button type="button" wire:click="$set('tag', '')" @class(['px-3 py-1.5 text-sm rounded-full border transition', 'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === '', 'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft' => $tag !== ''])>الكل</button>
                @foreach ($allTags as $t)
                    <button type="button" wire:click="$set('tag', @js($t))" @class(['px-3 py-1.5 text-sm rounded-full border transition', 'bg-primary text-white border-primary dark:bg-primary-dark dark:border-primary-dark' => $tag === $t, 'border-transparent bg-surface-light dark:bg-surface-dark text-ink-soft dark:text-ink-dark-soft' => $tag !== $t])>#{{ $t }}</button>
                @endforeach
            </div>
        @endif

        @if ($duaas->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">أضف أول دعاء لمجموعتك.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($duaas as $duaa)
                    <div wire:key="duaa-{{ $duaa->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $duaa->title }}</h3>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" wire:click="toggleFavorite({{ $duaa->id }})" title="مفضّل" class="text-sm {{ $duaa->is_favorite ? 'text-warning' : 'text-ink-soft/50 dark:text-ink-dark-soft/50 hover:text-warning' }}">★</button>
                                <button type="button" wire:click="editDuaa({{ $duaa->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="deleteDuaa({{ $duaa->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                        @if ($duaa->content)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2 whitespace-pre-line leading-7">{{ $duaa->content }}</p>@endif
                        @if (! empty($duaa->tags))
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach ($duaa->tags as $t)
                                    <button type="button" wire:click="$set('tag', @js($t))" class="text-xs px-2 py-0.5 rounded-full bg-secondary/25 text-ink dark:text-ink-dark hover:opacity-80">#{{ $t }}</button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('open') }" x-show="open" x-cloak @keydown.escape.window="open && $wire.close()" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" wire:click="close"></div>
        <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">{{ $form->duaa ? 'تعديل الدعاء' : 'دعاء جديد' }}</h2>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <x-input-label for="du_title" value="العنوان" />
                    <x-text-input id="du_title" wire:model="form.title" type="text" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('form.title')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="du_content" value="نص الدعاء" />
                    <textarea id="du_content" wire:model="form.content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm leading-7"></textarea>
                </div>
                <div>
                    <x-input-label for="du_tags" value="الوسوم (بفاصلة)" />
                    <x-text-input id="du_tags" wire:model="form.tagsInput" type="text" class="mt-1 block w-full" placeholder="صباح، هم، رزق" />
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="close" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
