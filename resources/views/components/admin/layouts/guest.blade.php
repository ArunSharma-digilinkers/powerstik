<!DOCTYPE html>
<html lang="en" class="h-full bg-zinc-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Admin' }} · Powerstik</title>
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="h-full font-sans text-ink antialiased">
    {{ $slot }}
</body>
</html>
