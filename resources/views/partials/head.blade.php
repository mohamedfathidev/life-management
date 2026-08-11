<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#3F7D7A">

<title>{{ $title ?? config('app.name', 'إدارة الحياة') }}</title>

{{-- No-flash dark mode: apply the theme class before the page paints --}}
<script>
    (function () {
        try {
            var t = localStorage.getItem('theme');
            var systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (t === 'dark' || ((t === 'system' || !t) && systemDark)) {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
    })();
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
