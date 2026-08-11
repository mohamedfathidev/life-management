<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('cvs.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← كل الـ CVs</a>

        {{-- Header --}}
        <div class="mt-3 flex items-start justify-between gap-4 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">📄 {{ $cv->title }}</h1>
                @if ($cv->target)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">موجّه لـ: {{ $cv->target }}</p>@endif
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">{{ $cv->original_name }} · {{ $cv->sizeLabel() }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('cvs.file', $cv) }}" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-lg border border-primary/40 text-primary dark:text-primary-dark text-sm hover:bg-primary/10 transition">فتح في تبويب</a>
                <button type="button" wire:click="delete" wire:confirm="حذف هذا الـ CV؟" class="px-3 py-1.5 rounded-lg border border-danger/40 text-danger text-sm hover:bg-danger/10 transition">حذف</button>
            </div>
        </div>

        {{-- Inline PDF preview --}}
        <div class="mt-6 rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-2 overflow-hidden">
            <iframe src="{{ route('cvs.file', $cv) }}" class="w-full rounded-lg" style="height: 82vh;" title="{{ $cv->title }}"></iframe>
        </div>
    </div>
</div>
