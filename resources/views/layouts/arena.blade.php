<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        @include('partials.head')
    </head>
    <body class="font-sans antialiased text-ink dark:text-ink-dark">
        <div class="min-h-screen bg-bg-light dark:bg-bg-dark">
            <nav class="bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-700">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-3">
                    <a href="{{ route('arena.index') }}" wire:navigate class="font-bold text-primary dark:text-primary-dark">🏟️ ساحة التحديات</a>
                    @auth
                        <div class="flex items-center gap-4 text-sm">
                            @if (auth()->user()->isOwner())
                                <a href="{{ route('dashboard') }}" wire:navigate class="text-ink-soft dark:text-ink-dark-soft hover:text-primary dark:hover:text-primary-dark">← تطبيقي</a>
                            @endif
                            <span class="text-ink-soft dark:text-ink-dark-soft hidden sm:inline">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('arena.logout') }}">
                                @csrf
                                <button type="submit" class="text-danger hover:underline">خروج</button>
                            </form>
                        </div>
                    @endauth
                </div>
            </nav>

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
