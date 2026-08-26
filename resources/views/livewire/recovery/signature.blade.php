<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8 text-center">
            <a href="{{ route('recovery.index') }}" wire:navigate class="inline-block text-sm text-primary dark:text-primary-dark hover:underline mb-4">← العودة للتعافي</a>
            <div class="inline-block px-4 py-1.5 rounded-full bg-primary/10 dark:bg-primary-dark/10 text-primary dark:text-primary-dark text-xs font-semibold mb-3">
                خط فاصل
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-ink dark:text-ink-dark mb-2">
                ✍️ توقيع التغيير
            </h1>
            <p class="text-base text-ink-soft dark:text-ink-dark-soft max-w-xl mx-auto leading-relaxed">
                مش تعهد بيتكتب وينسى، ده توقيع بخط الإيد على نهاية نسخة وبداية تانية
            </p>
        </div>

        <div class="relative rounded-2xl border-3 border-primary/40 dark:border-primary-dark/40 bg-gradient-to-br from-primary/5 via-surface-light to-secondary/5 dark:from-primary-dark/15 dark:via-surface-dark dark:to-secondary-dark/10 shadow-2xl overflow-hidden">
            <div class="h-1.5 bg-gradient-to-r from-transparent via-primary dark:via-primary-dark to-transparent"></div>

            <div class="p-8 sm:p-12">
                {{-- The declaration --}}
                <p class="text-2xl sm:text-3xl leading-loose text-ink dark:text-ink-dark text-center font-signature" style="letter-spacing: .5px;">
                    محمد القديم مات، مش بالكلام… بالأفعال اللي هتثبت ده، إن شاء الله.
                </p>

                {{-- Signature block --}}
                <div class="mt-12 flex flex-col items-end gap-1" style="direction: ltr;">
                    <p class="font-signature text-5xl sm:text-6xl text-primary dark:text-primary-dark" style="transform: rotate(-4deg); line-height: 1;">
                        محمد فتحي
                    </p>
                    <svg viewBox="0 0 220 20" class="w-40 sm:w-48 h-4 text-primary/60 dark:text-primary-dark/60 -mt-1 me-2" style="transform: rotate(-4deg);">
                        <path d="M2,14 C 40,2 70,18 100,8 S 160,2 218,10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft me-2 mt-1" style="direction: rtl;">23/8/2026</p>
                </div>
            </div>

            <div class="h-1.5 bg-gradient-to-r from-transparent via-primary dark:via-primary-dark to-transparent"></div>
        </div>
    </div>
</div>
