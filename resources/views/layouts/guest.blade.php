<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;1,500&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#191a20] antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-surface px-4 py-10">
            <div class="text-center mb-6">
                <p class="font-serif text-2xl text-[#0d2a5c]">DPC PERADI</p>
                <p class="text-xs tracking-[0.2em] text-[#5b5d68] mt-0.5">JAKARTA BARAT</p>
                <p class="text-xs text-[#7a7d8b] mt-2">Kantong Magang Advokat</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-7 bg-white shadow-sm rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
