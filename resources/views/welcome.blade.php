<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        @include('partials.head', ['title' => 'سيبها على الله — نظّم حياتك'])
    </head>
    <body class="antialiased font-sans text-ink dark:text-ink-dark">
        <div class="relative min-h-screen overflow-hidden bg-bg-light dark:bg-bg-dark flex items-center justify-center px-6">

            {{-- Calm green backdrop --}}
            <div class="absolute inset-0 bg-gradient-to-b from-success/10 via-transparent to-primary/5 dark:from-success/10 dark:to-primary/10"></div>
            <svg class="absolute -top-16 -start-16 w-72 h-72 text-success/10 rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
            <svg class="absolute -bottom-20 -end-10 w-80 h-80 text-primary/10 -rotate-12" fill="currentColor" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>

            <div class="relative w-full max-w-2xl text-center py-16">
                <div class="text-5xl mb-4">🌿</div>

                <h1 class="text-4xl sm:text-5xl font-bold text-primary dark:text-primary-dark">سيبها على الله</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">نظّم حياتك — أهدافك، عاداتك، صلواتك، ومشاريعك في مكان واحد.</p>

                {{-- Quote --}}
                <div class="mt-10 rounded-3xl bg-surface-light/70 dark:bg-surface-dark/70 backdrop-blur-sm shadow-sm p-8 sm:p-10">
                    <div class="text-3xl text-primary/40 dark:text-primary-dark/40 leading-none">﴿</div>
                    <p class="text-2xl sm:text-3xl font-semibold text-ink dark:text-ink-dark leading-relaxed">اعقِلها وتوكّل</p>
                    <div class="text-3xl text-primary/40 dark:text-primary-dark/40 leading-none mt-1">﴾</div>
                    <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-4 leading-loose">
                        خُذ بالأسباب ونظّم أمرك… وبعد ما تعمل اللي عليك، سيبها على الله.
                    </p>
                </div>

                {{-- Actions --}}
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-lg hover:opacity-90 transition">
                            ادخل تطبيقك ←
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-primary dark:bg-primary-dark text-white text-sm font-medium shadow-lg hover:opacity-90 transition">
                            الدخول
                        </a>
                        <a href="{{ route('arena.login') }}" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-surface-light dark:bg-surface-dark text-ink dark:text-ink-dark text-sm font-medium shadow-sm hover:shadow-md ring-1 ring-ink-soft/10 transition">
                            🏟️ ساحة التحديات
                        </a>
                    @endauth
                </div>

                <p class="mt-12 text-xs text-ink-soft dark:text-ink-dark-soft">صُنع بحبّ ليكون رفيقك في رحلتك 🤍</p>
            </div>
        </div>
    </body>
</html>
