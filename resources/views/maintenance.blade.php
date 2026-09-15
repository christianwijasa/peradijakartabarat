<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="robots" content="noindex, nofollow">
        <meta name="theme-color" content="#0d2a5c">
        <title>Pemeliharaan · {{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans text-ink antialiased bg-surface min-h-screen flex items-center justify-center px-4 py-12">
        <div class="app-card max-w-md w-full p-8 md:p-10 text-center">
            <p class="app-crumb">DPC PERADI Jakarta Barat</p>
            <h1 class="app-page-title mt-2">Sedang pemeliharaan</h1>
            <p class="text-sm text-muted-foreground mt-4 leading-relaxed">{{ $message }}</p>
            <p class="text-xs text-muted-foreground mt-6">Terima kasih atas pengertian Anda.</p>
        </div>
    </body>
</html>
