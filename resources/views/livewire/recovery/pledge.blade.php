<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">🤝 تعهد أمام الله</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">اكتب التزامك بينك وبين ربك — كلماتك أنت، تفتكرها وقت الضعف.</p>
        </div>

        @if ($editing)
            <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 space-y-4">
                <div>
                    <x-input-label for="pledge_body" value="نص التعهد" />
                    <textarea id="pledge_body" wire:model="body" rows="7"
                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-base leading-loose"
                        placeholder="أتعهّد أمام الله أن…"></textarea>
                    <x-input-error :messages="$errors->get('body')" class="mt-1" />
                </div>
                <div class="flex items-center justify-end gap-3">
                    @if ($hasPledge)
                        <button type="button" wire:click="$set('editing', false)" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    @endif
                    <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ التعهد</button>
                </div>
            </form>
        @else
            {{-- The committed pledge, framed --}}
            <div class="relative rounded-2xl border-2 border-primary/30 dark:border-primary-dark/30 bg-gradient-to-br from-primary/5 to-transparent dark:from-primary-dark/10 shadow-sm p-8">
                <div class="text-center text-3xl text-primary/40 dark:text-primary-dark/40 leading-none mb-3">﴿</div>
                <p class="text-lg leading-loose text-ink dark:text-ink-dark whitespace-pre-line text-center">{{ $body }}</p>
                <div class="text-center text-3xl text-primary/40 dark:text-primary-dark/40 leading-none mt-3">﴾</div>

                <div class="flex items-center justify-between mt-6 pt-4 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                    <span class="text-xs text-ink-soft dark:text-ink-dark-soft">
                        @if ($savedAt)تعهّدت — {{ $savedAt->translatedFormat('j F Y') }}@endif
                    </span>
                    <button type="button" wire:click="edit" class="text-sm text-primary dark:text-primary-dark hover:underline">تعديل</button>
                </div>
            </div>
        @endif
    </div>
</div>
