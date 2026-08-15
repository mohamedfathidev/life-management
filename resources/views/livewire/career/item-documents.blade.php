<div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $heading }}</h3>
        @if ($totalCount > 0)
            <span class="text-sm font-bold text-success">{{ $doneCount }}/{{ $totalCount }}</span>
        @endif
    </div>

    <form wire:submit="add" class="space-y-2 mb-4">
        @if ($showLibrary && $library->isNotEmpty())
            <select wire:model="newLinkId" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                <option value="">— اختر من مكتبتك «الأوراق» —</option>
                @foreach ($library as $lib)
                    <option value="{{ $lib->id }}">{{ $lib->name }} @if ($lib->is_ready)✓ جاهزة @endif</option>
                @endforeach
            </select>
        @endif
        <div class="flex items-center gap-2">
            <input type="text" wire:model="newName" placeholder="{{ $showLibrary && $library->isNotEmpty() ? 'أو اكتب ورقة جديدة…' : $placeholder }}"
                class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition shrink-0">إضافة</button>
        </div>
    </form>
    <x-input-error :messages="$errors->get('newName')" class="mb-2" />

    @forelse ($documents as $doc)
        <div wire:key="idoc-{{ $doc->id }}" class="py-2 border-b border-ink-soft/10 dark:border-ink-dark-soft/10 last:border-0"
             x-data="{ open: {{ $doc->note ? 'true' : 'false' }}, val: @js($doc->note) }">
            <div class="flex items-center gap-3">
                <button type="button" wire:click="toggle({{ $doc->id }})"
                    class="shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition {{ $doc->isReady() ? 'bg-success border-success text-white' : 'border-ink-soft/40 text-transparent hover:border-success' }}">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </button>
                <div class="flex-1 min-w-0">
                    <span class="text-sm text-ink dark:text-ink-dark {{ $doc->isReady() ? 'line-through opacity-60' : '' }}">{{ $doc->name }}</span>
                    @if ($doc->isLinked())
                        <span class="text-[10px] text-primary dark:text-primary-dark ms-1">🔗 من مكتبتك</span>
                        @if ($doc->generalDocument && $doc->generalDocument->hasFile())
                            <a href="{{ route('scholarships.documents.file', $doc->generalDocument) }}" target="_blank" class="text-[10px] text-primary dark:text-primary-dark hover:underline ms-1">📎 الملف</a>
                        @endif
                    @endif
                </div>
                <button type="button" @click="open = ! open" class="text-xs shrink-0 hover:underline {{ $doc->note ? 'text-primary dark:text-primary-dark' : 'text-ink-soft dark:text-ink-dark-soft' }}">📝 ملاحظة</button>
                <button type="button" wire:click="delete({{ $doc->id }})" wire:confirm="حذف؟" class="text-xs text-danger hover:underline shrink-0">حذف</button>
            </div>
            <div x-show="open" x-cloak class="mt-2 ps-8">
                <textarea x-model="val" rows="2" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="ملاحظاتك على مذاكرة/استعداد الجزء ده…"></textarea>
                <div class="flex justify-end mt-1">
                    <button type="button" @click="$wire.saveNote({{ $doc->id }}, val)" class="text-xs px-3 py-1 rounded-lg bg-primary dark:bg-primary-dark text-white hover:opacity-90">حفظ الملاحظة</button>
                </div>
            </div>

            {{-- Sub-steps --}}
            @if ($allowSubItems)
                <div class="ps-7 mt-1 space-y-1 border-s-2 border-ink-soft/10 dark:border-ink-dark-soft/10 ms-2">
                    @foreach ($doc->children as $child)
                        <div wire:key="idoc-child-{{ $child->id }}" class="flex items-center gap-2">
                            <button type="button" wire:click="toggle({{ $child->id }})"
                                class="shrink-0 w-4 h-4 rounded-full border-2 flex items-center justify-center transition {{ $child->isReady() ? 'bg-success border-success text-white' : 'border-ink-soft/40 text-transparent hover:border-success' }}">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <span class="flex-1 text-xs text-ink dark:text-ink-dark {{ $child->isReady() ? 'line-through opacity-60' : '' }}">{{ $child->name }}</span>
                            <button type="button" wire:click="delete({{ $child->id }})" wire:confirm="حذف؟" class="text-[11px] text-danger hover:underline shrink-0">حذف</button>
                        </div>
                    @endforeach
                    <div class="flex items-center gap-2">
                        <input type="text" wire:model="subInputs.{{ $doc->id }}" wire:keydown.enter.prevent="addSub({{ $doc->id }})" placeholder="+ خطوة فرعية…"
                            class="flex-1 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-xs py-1" />
                        <button type="button" wire:click="addSub({{ $doc->id }})" class="text-[11px] text-primary dark:text-primary-dark hover:underline shrink-0">إضافة</button>
                    </div>
                </div>
            @endif
        </div>
    @empty
        <p class="text-sm text-ink-soft dark:text-ink-dark-soft text-center py-4">لسه مفيش حاجات — أضف فوق.</p>
    @endforelse
</div>
