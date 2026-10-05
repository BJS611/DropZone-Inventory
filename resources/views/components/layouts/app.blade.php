<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'DropZone Inventory') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bg text-ink antialiased">
    <a href="#dz-main" class="sr-only focus:not-sr-only focus:absolute focus:z-9999 focus:bg-surface focus:px-4 focus:py-2 focus:text-ink">
        Lewati ke konten utama
    </a>

    @include('partials.sidebar')

    <div id="dz-main" class="dz-main min-h-screen lg:pl-64">
        {{ $slot }}
    </div>

    @stack('modals')
</body>
</html>
