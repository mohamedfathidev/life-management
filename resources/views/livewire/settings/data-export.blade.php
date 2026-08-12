<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">تصدير بياناتي</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            نزّل نسخة احتياطية كاملة من كل بياناتك (أهداف، مخطط، تعافٍ، دين، مالية، وكل شيء) كملف JSON واحد.
        </p>
    </header>

    <div class="mt-4">
        <button type="button" wire:click="export"
            wire:loading.attr="disabled" wire:target="export"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary dark:bg-primary-dark text-white text-sm font-medium rounded-lg hover:opacity-90 transition disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
            <span wire:loading.remove wire:target="export">تحميل نسخة (JSON)</span>
            <span wire:loading wire:target="export">جارٍ التجهيز…</span>
        </button>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">النسخة بتحتوي بياناتك بصيغة قابلة للقراءة — احتفظ بها في مكان آمن.</p>
    </div>
</section>
