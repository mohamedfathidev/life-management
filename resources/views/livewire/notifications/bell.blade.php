<div class="relative"
     x-data="{ open: false }"
     @click.outside="open = false"
     x-init="
        try {
            if (localStorage.getItem('notif_enabled') === '1' && 'Notification' in window && Notification.permission === 'granted') {
                const reminders = @js($reminders);
                if (reminders.length) {
                    const last = parseInt(localStorage.getItem('notif_last') || '0', 10);
                    if (Date.now() - last > 3 * 60 * 60 * 1000) {
                        const top = reminders[0];
                        const body = reminders.length > 1
                            ? ('عندك ' + reminders.length + ' تذكير — أهمها: ' + top.text)
                            : (top.emoji + ' ' + top.text);
                        new Notification('سيبها على الله', { body: body, icon: '/icons/icon-192.png', tag: 'sebha-reminder' });
                        localStorage.setItem('notif_last', String(Date.now()));
                    }
                }
            }
        } catch (e) {}
     ">
    {{-- Bell button --}}
    <button type="button" @click="open = ! open" title="التذكيرات"
            class="relative p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary-dark hover:bg-gray-100 dark:hover:bg-gray-700 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @if ($count > 0)
            <span class="absolute -top-0.5 -end-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-danger text-white text-[10px] font-bold flex items-center justify-center">{{ $count > 9 ? '9+' : $count }}</span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-transition x-cloak @click="open = false"
         class="absolute top-full end-0 z-50 mt-2 w-80 max-w-[90vw] rounded-xl bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black/5 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-600 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">التذكيرات</span>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $count }}</span>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse ($reminders as $r)
                <a href="{{ $r['url'] }}" wire:navigate wire:key="rem-{{ $loop->index }}"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-600/50 transition border-b border-gray-50 dark:border-gray-600/50 last:border-0">
                    <span class="text-lg shrink-0">{{ $r['emoji'] }}</span>
                    <span class="text-sm text-gray-800 dark:text-gray-100">{{ $r['text'] }}</span>
                </a>
            @empty
                <p class="px-4 py-8 text-sm text-gray-500 dark:text-gray-400 text-center">مفيش تذكيرات دلوقتي 🎉</p>
            @endforelse
        </div>
    </div>
</div>
