<div class="space-y-4" x-data="{ uploading: false }" wire:loading.class="opacity-60" wire:target="logo">
    <div>
        <h3 class="text-lg font-semibold text-ink dark:text-ink-dark mb-1">🖼️ لوجو التطبيق</h3>
        <p class="text-sm text-ink-soft dark:text-ink-dark-soft">الصورة اللي بتظهر جنب الاسم فوق في شريط التنقل</p>
    </div>

    <div class="flex items-center gap-4">
        <img src="{{ $currentLogoUrl }}" alt="اللوجو الحالي" class="w-16 h-16 rounded-xl object-cover border border-ink-soft/15 dark:border-ink-dark-soft/15 shadow-sm" />

        <form wire:submit="updateLogo" class="flex-1 space-y-2">
            <input type="file" wire:model="logo" accept="image/*"
                class="block w-full text-sm text-ink-soft dark:text-ink-dark-soft file:me-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary/10 dark:file:bg-primary-dark/15 file:text-primary dark:file:text-primary-dark hover:file:opacity-80" />

            @if ($logo)
                <div class="flex items-center gap-3">
                    <img src="{{ $logo->temporaryUrl() }}" alt="معاينة" class="w-12 h-12 rounded-lg object-cover border border-primary/30" />
                    <button type="submit" wire:loading.attr="disabled" wire:target="updateLogo"
                        class="px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="updateLogo">احفظ اللوجو الجديد</span>
                        <span wire:loading wire:target="updateLogo">جاري الحفظ…</span>
                    </button>
                </div>
            @endif

            <x-input-error :messages="$errors->get('logo')" class="mt-1" />
        </form>
    </div>
</div>

{{-- Refresh every "Logo.jpg" <img> on the page (e.g. the nav bar) without a full reload --}}
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('logo-updated', () => {
            const stamp = Date.now();
            document.querySelectorAll('img[src*="icons/Logo.jpg"]').forEach((img) => {
                img.src = img.src.split('?')[0] + '?v=' + stamp;
            });
        });
    });
</script>
