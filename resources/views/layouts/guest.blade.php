<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        @include('partials.head')
    </head>
    <body class="font-sans antialiased text-ink dark:text-ink-dark">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-bg-light dark:bg-bg-dark">
            <div>
                <a href="/" wire:navigate class="flex flex-col items-center gap-2">
                    <x-application-logo class="w-16 h-16 fill-current text-primary dark:text-primary-dark" />
                    <span class="text-lg font-semibold text-primary dark:text-primary-dark">سيبها على الله</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-surface-light dark:bg-surface-dark shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
