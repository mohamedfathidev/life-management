<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        @include('partials.head')
    </head>
    <body class="font-sans antialiased text-ink dark:text-ink-dark">
        <div class="min-h-screen bg-bg-light dark:bg-bg-dark">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-surface-light dark:bg-surface-dark shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Global task hub: one modal any page can open via the `create-task` event --}}
        <livewire:planner.manage-task />

        {{-- Floating quick-capture: drop a task straight into today's plan from anywhere --}}
        <button type="button" title="أضف تاسك النهاردة"
            onclick="Livewire.dispatch('create-task', { today: true, kind: 'other' })"
            class="fixed bottom-6 left-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-primary dark:bg-primary-dark text-white shadow-lg hover:scale-105 hover:opacity-95 transition print:hidden">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </button>
    </body>
</html>
