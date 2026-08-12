<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">الدين</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">صلواتك وقرآنك وصدقاتك وأدعيتك في مكان واحد.</p>
            </div>
            <x-add-to-today kind="worship" label="تاسك عبادة النهاردة" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <a href="{{ route('religion.prayers') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">🕌</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">الصلوات</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تتبّع الصلوات الخمس و«في وقتها» ونسبة الشهر.</p>
            </a>
            <a href="{{ route('religion.quran') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">📖</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">القرآن</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">سجّل وردك وتابع تقدّم الختمة.</p>
            </a>
            <a href="{{ route('religion.donations') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">🤲</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">الصدقات</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">سجّل صدقاتك وشوف الإجمالي.</p>
            </a>
            <a href="{{ route('religion.duaas') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">📿</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">الأدعية</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">مجموعتك الشخصية من الأدعية بالوسوم.</p>
            </a>
        </div>
    </div>
</div>
