<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-5">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">🗄️ الأرشيف</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">تاريخ التطبيق كله في مكان واحد — كل سجلاتك عبر الأقسام. (المذكرات والتعافي مستبعدة للخصوصية.)</p>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <div class="flex-1 min-w-[200px] relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="ابحث في السجلات…"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm ps-9" />
                <svg class="w-4 h-4 absolute top-2.5 start-3 text-ink-soft dark:text-ink-dark-soft" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            </div>
            <select wire:model.live="type" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                <option value="">كل الأقسام</option>
                @foreach ($types as $key => $meta)
                    <option value="{{ $key }}">{{ $meta['emoji'] }} {{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Date range --}}
        <div class="flex flex-wrap items-end gap-3 mb-4">
            <div>
                <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">من تاريخ</label>
                <input type="date" wire:model.live="from" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
            </div>
            <div>
                <label class="block text-xs text-ink-soft dark:text-ink-dark-soft mb-1">إلى تاريخ</label>
                <input type="date" wire:model.live="to" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm" />
            </div>
            @if ($search !== '' || $type !== '' || $from || $to)
                <button wire:click="resetFilters" class="px-3 py-2 text-sm text-ink-soft dark:text-ink-dark-soft hover:text-ink dark:hover:text-ink-dark">إعادة ضبط</button>
            @endif
        </div>

        <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-2">{{ number_format($total) }} سجل</p>

        {{-- Table --}}
        <div class="rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-ink-soft dark:text-ink-dark-soft border-b border-ink-soft/10 dark:border-ink-dark-soft/10">
                        <tr>
                            <th class="text-start font-medium px-4 py-3 whitespace-nowrap">التاريخ</th>
                            <th class="text-start font-medium px-4 py-3 whitespace-nowrap">القسم</th>
                            <th class="text-start font-medium px-4 py-3">الوصف</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-soft/10 dark:divide-ink-dark-soft/10">
                        @forelse ($records as $r)
                            <tr wire:key="arch-{{ $loop->index }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="px-4 py-3 whitespace-nowrap text-ink-soft dark:text-ink-dark-soft" dir="ltr">{{ $r['date']->translatedFormat('j M Y') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $r['emoji'] }} {{ $r['typeLabel'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-ink dark:text-ink-dark">{{ $r['title'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-end">
                                    @if ($r['url'])<a href="{{ $r['url'] }}" wire:navigate class="text-xs text-primary dark:text-primary-dark hover:underline">فتح</a>@endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-ink-soft dark:text-ink-dark-soft">
                                    مفيش سجلات بالفلاتر دي.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination (controls appear once there are more than one page) --}}
        <div class="mt-4">
            {{ $records->links('livewire::tailwind') }}
        </div>
    </div>
</div>
