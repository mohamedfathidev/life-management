<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#3F7D7A">

{{-- PWA --}}
<link rel="manifest" href="/manifest.webmanifest">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="إدارة الحياة">

<title>{{ $title ?? config('app.name', 'إدارة الحياة') }}</title>

{{-- No-flash dark mode. Applied before paint AND re-applied after every
     wire:navigate swap (Livewire morphs in a <html> without the .dark class). --}}
<script>
    window.applyTheme = function () {
        try {
            var t = localStorage.getItem('theme');
            var systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            var dark = t === 'dark' || ((t === 'system' || ! t) && systemDark);
            document.documentElement.classList.toggle('dark', dark);
        } catch (e) {}
    };
    window.applyTheme();
    document.addEventListener('livewire:navigated', window.applyTheme);
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
