<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <a href="{{ route('diary.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← المذكرات</a>

        {{-- Root of the tree: the fixed question --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary/15 via-secondary/10 to-transparent dark:from-primary-dark/20 dark:via-secondary-dark/10 p-6 sm:p-8 text-center">
            <div class="absolute -top-10 -end-10 w-40 h-40 rounded-full bg-secondary/25 dark:bg-secondary-dark/20 blur-2xl"></div>
            <div class="absolute -bottom-14 -start-14 w-48 h-48 rounded-full bg-primary/20 dark:bg-primary-dark/20 blur-2xl"></div>
            <p class="relative text-2xl sm:text-3xl font-extrabold text-ink dark:text-ink-dark leading-snug">
                🌳 ليه مبتغيرش… ولا حياتي بتتغير؟
            </p>
            <p class="relative text-sm text-ink-soft dark:text-ink-dark-soft mt-3 max-w-md mx-auto leading-relaxed">
                كل سبب هنا فرع من الشجرة دي. واجهه بصراحة، وضيف فروع جديدة كل ما تكتشف حاجة.
            </p>
        </div>

        {{-- The tree --}}
        @if ($tree->isEmpty())
            <div class="text-center py-12 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه مفيش أسباب مكتوبة. ابدأ بأول سبب حقيقي تحت.</p>
            </div>
        @else
            <div class="space-y-3">
                @include('livewire.diary._reason-node', ['nodes' => $tree])
            </div>
        @endif

        {{-- Add / edit form --}}
        <form wire:submit="save" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 space-y-3">
            <x-input-label :value="$editingId ? 'تعديل السبب' : 'سبب جديد'" />
            <textarea wire:model="body" rows="3" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" placeholder="اكتب السبب بصراحة مع نفسك…"></textarea>
            <x-input-error :messages="$errors->get('body')" />

            @unless ($editingId)
                <div>
                    <x-input-label value="فرع تحت سبب موجود؟ (اختياري)" />
                    <select wire:model="parentId" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                        <option value="">سبب جديد أساسي</option>
                        @foreach ($flatOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->label }}</option>
                        @endforeach
                    </select>
                </div>
            @endunless

            <div class="flex items-center justify-end gap-3 pt-1">
                @if ($editingId)
                    <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                @endif
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">{{ $editingId ? 'حفظ' : 'إضافة للشجرة' }}</button>
            </div>
        </form>
    </div>
</div>
