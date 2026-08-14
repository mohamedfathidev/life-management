<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('scholarships.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← المنح</a>
        <div class="flex items-start justify-between gap-4 mt-1 mb-5">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">📄 الأوراق</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">الأوراق المطلوبة للمنح — ارفعها وعلّم اللي جهّزته.</p>
            </div>
            @if ($documents->isNotEmpty())
                <div class="text-center shrink-0">
                    <p class="text-2xl font-bold text-success">{{ $readyCount }}/{{ $documents->count() }}</p>
                    <p class="text-xs text-ink-soft dark:text-ink-dark-soft">جاهزة</p>
                </div>
            @endif
        </div>

        {{-- Add --}}
        <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-4 mb-5">
            <form wire:submit="addDocument" class="flex items-center gap-2">
                <input type="text" wire:model="newName" placeholder="أضف ورقة (مثال: خطاب الدوافع)…"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition shrink-0">إضافة</button>
            </form>
            <x-input-error :messages="$errors->get('newName')" class="mt-1" />
            @if ($documents->isEmpty())
                <button type="button" wire:click="addCommon" class="mt-3 text-xs text-primary dark:text-primary-dark hover:underline">✨ أضف الأوراق الشائعة تلقائيًا</button>
            @endif
        </div>

        {{-- Checklist --}}
        @if ($documents->isEmpty())
            <div class="text-center py-14 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🗂️</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">ابدأ بإضافة أوراقك، أو اضغط «أضف الأوراق الشائعة».</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($documents as $doc)
                    <div wire:key="doc-{{ $doc->id }}" class="flex items-center gap-3 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-4">
                        {{-- ready toggle --}}
                        <button type="button" wire:click="toggleReady({{ $doc->id }})"
                            class="shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition {{ $doc->is_ready ? 'bg-success border-success text-white' : 'border-ink-soft/40 text-transparent hover:border-success' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </button>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-ink dark:text-ink-dark {{ $doc->is_ready ? 'line-through opacity-70' : '' }}">{{ $doc->name }}</p>
                            @if ($doc->hasFile())
                                <a href="{{ route('scholarships.documents.file', $doc) }}" target="_blank" class="text-xs text-primary dark:text-primary-dark hover:underline truncate inline-block max-w-full">📎 {{ $doc->original_name }}</a>
                            @endif
                        </div>

                        {{-- upload / actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <label class="cursor-pointer text-xs text-primary dark:text-primary-dark hover:underline">
                                {{ $doc->hasFile() ? 'استبدال' : 'رفع' }}
                                <input type="file" wire:model="uploads.{{ $doc->id }}" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                            </label>
                            @if ($doc->hasFile())
                                <button type="button" wire:click="removeFile({{ $doc->id }})" class="text-xs text-ink-soft dark:text-ink-dark-soft hover:text-danger">إزالة الملف</button>
                            @endif
                            <button type="button" wire:click="delete({{ $doc->id }})" wire:confirm="حذف الورقة؟" class="text-xs text-danger hover:underline">حذف</button>
                        </div>
                    </div>
                    <div wire:loading wire:target="uploads.{{ $doc->id }}" class="text-xs text-ink-soft dark:text-ink-dark-soft ps-9">جارِ الرفع…</div>
                    <x-input-error :messages="$errors->get('uploads.'.$doc->id)" class="ps-9" />
                @endforeach
            </div>
        @endif
    </div>
</div>
