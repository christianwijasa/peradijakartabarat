@props(['crumb', 'title', 'subtitle' => null, 'menu' => [], 'userMeta' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;1,500&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface text-[#191a20] font-sans antialiased">
    <div x-data="{ navOpen: false }" class="min-h-screen md:grid md:grid-cols-[244px_minmax(0,1fr)]">
        <div class="md:hidden flex items-center justify-between bg-navy text-white px-4 py-3 sticky top-0 z-30">
            <span class="font-serif text-base">DPC PERADI · Jakarta Barat</span>
            <button @click="navOpen = !navOpen" class="text-sm border border-white/30 rounded-md px-3 py-1.5">
                <span x-text="navOpen ? 'Tutup' : 'Menu'"></span>
            </button>
        </div>

        <aside :class="navOpen ? 'flex' : 'hidden'"
               class="bg-navy text-white flex-col py-6 gap-5 md:sticky md:top-0 md:h-screen md:!flex">
            <div class="px-5">
                <p class="font-serif text-lg leading-tight">DPC PERADI</p>
                <p class="text-xs tracking-[0.15em] text-[#aec1e4]">JAKARTA BARAT</p>
            </div>
            <div class="border-t border-white/10 mx-5"></div>
            <div class="px-5">
                <p class="text-[11px] tracking-[0.14em] text-[#7a93c2] uppercase mb-2">Kantong Magang Advokat</p>
                <nav class="flex flex-col gap-1">
                    @foreach ($menu as $item)
                        <a href="{{ $item['route'] }}"
                           class="flex items-center gap-2 rounded-md pl-3 pr-2.5 py-2.5 text-[13.5px] border-l-2 transition
                               {{ ($item['active'] ?? false)
                                    ? 'bg-navy-light text-white border-primary-light font-medium'
                                    : 'text-[#aec1e4] border-transparent hover:bg-white/5' }}">
                            <span class="flex-1 text-left">{{ $item['label'] }}</span>
                            @if (! empty($item['badge']))
                                <span class="bg-primary text-white text-[11px] rounded-full min-w-[20px] h-5 flex items-center justify-center px-1.5">{{ $item['badge'] }}</span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>
            <div class="mt-auto px-5">
                <div class="border-t border-white/10 pt-4">
                    <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[#aec1e4]">
                        @switch(auth()->user()->role)
                            @case('calon_advokat') Calon Advokat @break
                            @case('law_firm') Law Firm @break
                            @case('admin_dpc') Admin DPC @break
                        @endswitch
                    </p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button class="text-xs text-[#aec1e4] hover:text-white underline underline-offset-2">Keluar</button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex flex-col min-w-0">
            <main class="px-5 md:px-9 pt-6 md:pt-7 pb-14 flex flex-col gap-5 max-w-[1180px] w-full">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <p class="text-[11px] tracking-[0.14em] uppercase text-[#7a7d8b]">{{ $crumb }}</p>
                        <h1 class="font-serif text-[26px] md:text-[28px] text-[#0d2a5c] mt-1">{{ $title }}</h1>
                        @if ($subtitle)
                            <p class="text-sm text-[#5b5d68] mt-2 max-w-xl">{{ $subtitle }}</p>
                        @endif
                    </div>
                    <div class="bg-white rounded-xl shadow-sm px-4 py-3 flex items-center gap-3 self-start">
                        <div class="w-9 h-9 rounded-full bg-[#e9eef6] text-primary flex items-center justify-center text-sm font-semibold shrink-0">
                            {{ Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[#7a7d8b] leading-tight">{{ $userMeta }}</p>
                        </div>
                    </div>
                </div>

                @if (session('status'))
                    <div class="bg-tag-ok-bg text-tag-ok-fg text-sm rounded-lg px-4 py-3">{{ session('status') }}</div>
                @endif

                {{ $slot }}

                <p class="text-xs text-[#9a9ca6] mt-10">Platform Kantong Magang Advokat · DPC PERADI Jakarta Barat.</p>
            </main>
        </div>
    </div>
</body>
</html>
