<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>

        {{-- Hero --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-secondary/15 via-primary/10 to-transparent dark:from-secondary-dark/20 dark:via-primary-dark/10 p-6 sm:p-8 text-center">
            <div class="absolute -top-10 -start-10 w-40 h-40 rounded-full bg-secondary/20 dark:bg-secondary-dark/20 blur-2xl"></div>
            <div class="absolute -bottom-14 -end-14 w-48 h-48 rounded-full bg-primary/20 dark:bg-primary-dark/20 blur-2xl"></div>
            <p class="relative text-2xl sm:text-3xl font-extrabold text-ink dark:text-ink-dark leading-snug">
                💡 أفكار تراودني
            </p>
            <p class="relative text-sm text-ink-soft dark:text-ink-dark-soft mt-3 max-w-md mx-auto leading-relaxed">
                أي فكرة أو خاطرة تعدّي في دماغك وتستاهل توقف عندها — سجّلها هنا قبل ما تتنسى.
            </p>
        </div>

        {{-- List --}}
        @if ($ideas->isEmpty())
            <div class="text-center py-12 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش أفكار مسجّلة. ابدأ بأول فكرة تحت.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($ideas as $idea)
                    <div wire:key="idea-{{ $idea->id }}" class="group rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md border border-transparent hover:border-secondary/30 dark:hover:border-secondary-dark/30 overflow-hidden transition-all duration-200">
                        <div class="flex items-start gap-3 p-4">
                            <span class="mt-0.5 text-lg text-secondary dark:text-secondary-dark shrink-0">💡</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-ink dark:text-ink-dark leading-relaxed whitespace-pre-line">{{ $idea->body }}</p>
                                <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1.5">{{ $idea->created_at->translatedFormat('j M Y، g:i A') }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition">
                                <button type="button" wire:click="edit({{ $idea->id }})" class="text-xs text-primary dark:text-primary-dark hover:underline">تعديل</button>
                                <button type="button" wire:click="delete({{ $idea->id }})" wire:confirm="حذف الفكرة دي؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>

                        @if ($idea->action_taken)
                            <div class="flex items-start gap-3 px-4 py-3 border-t border-secondary/15 dark:border-secondary-dark/15 bg-secondary/5 dark:bg-secondary-dark/10">
                                <span class="mt-0.5 text-sm shrink-0">✅</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-secondary dark:text-secondary-dark">إزاي تعاملت معاها</p>
                                    <p class="text-sm text-ink dark:text-ink-dark leading-relaxed whitespace-pre-line mt-0.5">{{ $idea->action_taken }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Add / edit form --}}
        <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 space-y-4">
            <div class="space-y-2">
                <x-input-label :value="$editingId ? 'تعديل الفكرة' : 'فكرة جديدة'" />
                <textarea wire:model="body" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="اكتب الفكرة أو الخاطرة اللي جتلك…"></textarea>
                <x-input-error :messages="$errors->get('body')" />
            </div>

            <div class="space-y-2">
                <x-input-label value="إزاي تعاملت مع الفكرة؟ (اختياري)" />
                <textarea wire:model="actionTaken" rows="2" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="اكتب إيه اللي عملته أو قررته بخصوص الفكرة دي…"></textarea>
                <x-input-error :messages="$errors->get('actionTaken')" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-1">
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                @endif
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة' }}</button>
            </div>
        </form>
    </div>
</div>
