<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">السير الذاتية</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">نسخ CV موجّهة لكل هدف — ارفعها وشوفها هنا.</p>
            </div>
            <button type="button" wire:click="openUpload" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ رفع CV</button>
        </div>

        @if ($cvs->isEmpty())
            <div class="text-center py-20 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش CVs مرفوعة لسه.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($cvs as $cv)
                    <div wire:key="cv-{{ $cv->id }}" class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('cvs.show', $cv) }}" wire:navigate class="min-w-0">
                                <h3 class="font-semibold text-ink dark:text-ink-dark flex items-center gap-2">📄 {{ $cv->title }}</h3>
                                @if ($cv->target)<p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">موجّه لـ: {{ $cv->target }}</p>@endif
                                <p class="text-[11px] text-ink-soft dark:text-ink-dark-soft mt-1">{{ $cv->sizeLabel() }}</p>
                            </a>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('cvs.show', $cv) }}" wire:navigate class="text-xs text-primary dark:text-primary-dark hover:underline">عرض</a>
                                <button type="button" wire:click="delete({{ $cv->id }})" wire:confirm="حذف هذا الـ CV؟" class="text-xs text-danger hover:underline">حذف</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Upload modal --}}
    <div
        x-data="{ open: @entangle('uploadOpen') }"
        x-show="open"
        x-cloak
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40" @click="open = false"></div>

        <div x-show="open" x-transition class="relative w-full max-w-lg rounded-2xl bg-surface-light dark:bg-surface-dark shadow-xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 class="text-lg font-semibold text-ink dark:text-ink-dark mb-4">رفع CV</h2>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <x-input-label for="cv_title" value="العنوان" />
                    <x-text-input id="cv_title" wire:model="title" type="text" class="mt-1 block w-full" placeholder="مثال: CV باك إند" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="cv_target" value="موجّه لـ (اختياري)" />
                    <x-text-input id="cv_target" wire:model="target" type="text" class="mt-1 block w-full" placeholder="دور / شركة / مجال" />
                </div>

                <div>
                    <x-input-label for="cv_file" value="الملف (PDF)" />
                    <input id="cv_file" type="file" wire:model="file" accept="application/pdf" class="mt-1 block w-full text-sm text-ink dark:text-ink-dark file:me-3 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary dark:file:text-primary-dark file:px-3 file:py-1.5" />
                    <div wire:loading wire:target="file" class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">جارٍ الرفع…</div>
                    <x-input-error :messages="$errors->get('file')" class="mt-1" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="open = false" class="px-4 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إلغاء</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save,file" class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
