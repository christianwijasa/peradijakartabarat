<x-layout
    crumb="Admin DPC Jakarta Barat"
    title="Verifikasi dual-level"
    subtitle="Validasi kelulusan alumni serta kelayakan kantor hukum dan advokat pendamping."
    :menu="\App\Support\SidebarMenu::admin('verifikasi')"
    user-meta="Admin Bidang Magang"
>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($stats as $s)
            <x-card class="p-5">
                <p class="text-2xl font-serif text-[#0d2a5c]">{{ $s['value'] }}</p>
                <p class="text-xs text-[#7a7d8b] mt-1">{{ $s['label'] }}</p>
            </x-card>
        @endforeach
    </div>

    <x-card class="overflow-hidden">
        <div class="p-4 border-b border-[#eceef2] flex gap-2">
            <a href="{{ route('admin.verifikasi', ['tab' => 'calon']) }}"
               class="rounded-lg px-3.5 py-2 text-[13px] font-medium border {{ $tab === 'calon' ? 'bg-white text-primary border-[#c9d9f0] shadow-sm' : 'border-transparent text-[#7a7d8b]' }}">
                Calon Advokat
            </a>
            <a href="{{ route('admin.verifikasi', ['tab' => 'firm']) }}"
               class="rounded-lg px-3.5 py-2 text-[13px] font-medium border {{ $tab === 'firm' ? 'bg-white text-primary border-[#c9d9f0] shadow-sm' : 'border-transparent text-[#7a7d8b]' }}">
                Law Firm & Pendamping
            </a>
        </div>

        <div class="divide-y divide-[#eceef2]">
            @if ($tab === 'calon')
                @forelse ($queueCalon as $ca)
                    @php
                        $siap = $ca->checklistItems->every(fn ($c) => $c->is_checked);
                    @endphp
                    <div class="flex flex-col md:flex-row md:items-center gap-4 px-6 py-5">
                        <div class="md:w-64 shrink-0">
                            <p class="font-medium">{{ $ca->user->name }}</p>
                            <p class="text-xs text-[#7a7d8b] mt-0.5">NIK {{ $ca->nik ? substr($ca->nik, 0, 4).'••••' : '—' }} · UPA {{ $ca->upa_gelombang }}</p>
                        </div>
                        <div class="flex-1 flex flex-col gap-1.5">
                            @foreach ($ca->checklistItems as $item)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="w-2 h-2 rounded-full shrink-0 {{ $item->is_checked ? 'bg-[#2e6b4f]' : 'bg-[#c2492f]' }}"></span>
                                    {{ $item->label }}
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2.5 shrink-0">
                            <x-tag :variant="$siap ? 'wait' : 'bad'">{{ $siap ? 'Siap disetujui' : 'Perlu perbaikan' }}</x-tag>
                            <form method="POST" action="{{ route('admin.verifikasi.calon.setujui', $ca) }}">
                                @csrf
                                <x-btn>Setujui</x-btn>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-[#7a7d8b]">Tidak ada calon advokat menunggu verifikasi.</p>
                @endforelse
            @else
                @forelse ($queueFirm as $firm)
                    @php
                        $siap = $firm->checklistItems->every(fn ($c) => $c->is_checked);
                    @endphp
                    <div class="flex flex-col md:flex-row md:items-center gap-4 px-6 py-5">
                        <div class="md:w-64 shrink-0">
                            <p class="font-medium">{{ $firm->nama }}</p>
                            <p class="text-xs text-[#7a7d8b] mt-0.5">{{ $firm->sk_kemenkumham ?? 'SK Kemenkumham —' }} · {{ $firm->advokatPendampings()->count() }} advokat pendamping</p>
                        </div>
                        <div class="flex-1 flex flex-col gap-1.5">
                            @foreach ($firm->checklistItems as $item)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="w-2 h-2 rounded-full shrink-0 {{ $item->is_checked ? 'bg-[#2e6b4f]' : 'bg-[#c2492f]' }}"></span>
                                    {{ $item->label }}
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2.5 shrink-0">
                            <x-tag :variant="$siap ? 'wait' : 'bad'">{{ $siap ? 'Penetapan kuota' : 'Perlu perbaikan' }}</x-tag>
                            <form method="POST" action="{{ route('admin.verifikasi.firm.tetapkan-kuota', $firm) }}">
                                @csrf
                                <x-btn>Tetapkan kuota</x-btn>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-[#7a7d8b]">Tidak ada kantor hukum menunggu verifikasi.</p>
                @endforelse
            @endif
        </div>
    </x-card>
</x-layout>
