<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">الكارير</h1>
        <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 mb-6">كل اللي يخص مسارك المهني في مكان واحد.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            {{-- Scholarships (active) --}}
            <a href="{{ route('scholarships.index') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">🎓</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">المنح</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">متابعة التقديمات، الشروط، التعلّم عن المنح، والتطوّع.</p>
            </a>

            {{-- Jobs --}}
            <a href="{{ route('jobs.index') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">💻</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">الوظائف</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تتبّع التقديم للوظائف والاستعداد للإنترفيو.</p>
            </a>

            {{-- Market study --}}
            <a href="{{ route('market-study.index') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">📚</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">مذاكرة السوق</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">خطط مذاكرة لكل مجال + شعار تحفيزي.</p>
            </a>

            {{-- Personal marketing --}}
            <a href="{{ route('marketing.index') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">📣</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">التسويق الشخصي</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">جدولة بوستات لينكدإن وبناء حضورك.</p>
            </a>

            {{-- CVs --}}
            <a href="{{ route('cvs.index') }}" wire:navigate class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-6">
                <div class="text-3xl mb-2">📄</div>
                <h3 class="font-semibold text-ink dark:text-ink-dark">السير الذاتية (CVs)</h3>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">ارفع نسخك الموجّهة واعرضها هنا.</p>
            </a>
        </div>
    </div>
</div>
