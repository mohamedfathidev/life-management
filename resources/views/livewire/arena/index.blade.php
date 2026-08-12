<div class="py-10 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">التحديات المشتركة</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">نافس أصحابك في الصلاة والورد، واطلع في لوحة الشرف.</p>
            </div>
            @if ($isOwner)
                <a href="{{ route('arena.challenges.create') }}" wire:navigate class="shrink-0 px-4 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-sm hover:opacity-90 transition">+ تحدي جديد</a>
            @endif
        </div>

        {{-- Join by code --}}
        <form wire:submit="join" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 mb-6">
            <label class="block text-sm font-medium text-ink dark:text-ink-dark mb-2">عندك كود دعوة؟ انضم للتحدي</label>
            <div class="flex items-center gap-2">
                <input type="text" wire:model="joinCode" placeholder="مثال: A1B2C3"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm uppercase" dir="ltr" />
                <button type="submit" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">انضم</button>
            </div>
            <x-input-error :messages="$errors->get('joinCode')" class="mt-1" />
        </form>

        {{-- Joined challenges --}}
        <h2 class="text-sm font-semibold text-ink-soft dark:text-ink-dark-soft mb-3">تحدياتك</h2>
        @if ($joined->isEmpty())
            <div class="text-center py-14 rounded-2xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🏟️</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">لسه ماانضميتش لأي تحدي.</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">استخدم كود الدعوة فوق@if ($isOwner) أو اعمل تحدي جديد@endif.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($joined as $c)
                    <a href="{{ route('arena.challenges.show', $c) }}" wire:navigate wire:key="sc-{{ $c->id }}"
                       class="block rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md transition p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-ink dark:text-ink-dark">{{ $c->name }}</h3>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                                    <span>👥 {{ $c->participants_count }} مشارك</span>
                                    <span>· 📅 {{ $c->start_date->translatedFormat('j M') }}@if ($c->end_date) → {{ $c->end_date->translatedFormat('j M') }}@endif</span>
                                    @if ($c->owner_id === auth()->id())<span class="text-primary dark:text-primary-dark">· إنت المدير</span>@endif
                                </div>
                            </div>
                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $c->statusLabel() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
