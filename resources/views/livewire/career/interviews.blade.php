<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-6">
            <a href="{{ route('career') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الكارير</a>
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mt-1">🎙️ انترفيوز</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">كل حاجة حالتها «إنترفيو» في مكان واحد — اكتب أكتر حاجة تركّز عليها وتستعد لها.</p>
        </div>

        @if (! count($items))
            <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🎙️</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش حاجة في مرحلة الإنترفيو دلوقتي.</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">لما تنقل أي نشاط/منحة/وظيفة لمرحلة «إنترفيو» هتظهر هنا.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($items as $item)
                    @php($key = $item['type'].'_'.$item['id'])
                    <div wire:key="iv-{{ $key }}" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ $item['url'] }}" wire:navigate class="font-semibold text-ink dark:text-ink-dark hover:text-primary dark:hover:text-primary-dark transition flex items-center gap-2">
                                    <span>{{ $item['emoji'] }}</span>
                                    <span class="truncate">{{ $item['title'] }}</span>
                                </a>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                                    <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $item['source'] }}</span>
                                    @if ($item['sub'])<span>{{ $item['sub'] }}</span>@endif
                                    @if ($item['date'])<span dir="ltr">📅 {{ $item['date']->translatedFormat('j M Y') }}</span>@endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-ink dark:text-ink-dark mb-1">🎯 أكتر حاجة أركّز عليها في الاستعداد</label>
                            <textarea wire:model="focus.{{ $key }}" rows="3"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm"
                                placeholder="مثال: أراجع مشاريعي، أتوقّع أسئلة الـ system design، أجهّز أسئلة أسألها…"></textarea>
                            <div class="flex items-center justify-end gap-3 mt-2">
                                <span wire:loading.remove wire:target="saveFocus('{{ $item['type'] }}', {{ $item['id'] }})"
                                      x-data="{ show: false }" x-on:interview-focus-saved.window="show = true; setTimeout(() => show = false, 1500)" x-show="show" x-cloak class="text-xs text-success">تم الحفظ ✓</span>
                                <button type="button" wire:click="saveFocus('{{ $item['type'] }}', {{ $item['id'] }})"
                                    class="px-4 py-1.5 rounded-lg bg-primary dark:bg-primary-dark text-white text-xs font-medium hover:opacity-90 transition">حفظ</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
