<x-layout
    crumb="Law Firm · {{ $firm->nama }}"
    title="Dashboard & kuota bimbingan"
    subtitle="Pantau kuota gabungan kantor dan advokat pendamping, serta progres seluruh pemagang."
    :menu="\App\Support\SidebarMenu::firm('dashboard')"
    :user-meta="'Advokat Pendamping · '.$firm->nama"
>
    @if ($firm->status_verifikasi !== 'terverifikasi')
        <x-card class="p-6 border-l-4 border-[#b8862f]">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-[#fef3e8] text-[#b8862f] flex items-center justify-center text-sm font-semibold shrink-0">!</div>
                <div class="flex-1">
                    <p class="font-medium text-[#191a20]">Menunggu verifikasi Admin DPC</p>
                    <p class="text-sm text-[#5b5d68] mt-1">
                        Kantor hukum Anda sedang dalam proses verifikasi oleh Admin DPC Jakarta Barat. 
                        Fitur lowongan magang dan review pelamar akan diaktifkan setelah verifikasi selesai.
                    </p>
                    @if ($firm->status_verifikasi === 'perlu_perbaikan')
                        <p class="text-sm text-[#93321f] mt-2 font-medium">⚠ Status: Perlu perbaikan dokumen</p>
                    @endif
                </div>
            </div>
        </x-card>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_360px] gap-5 items-start">
        <x-card class="p-6">
            <p class="text-[11px] tracking-[0.14em] uppercase text-[#7a7d8b]">Kuota bimbingan gabungan</p>
            <p class="mt-1 text-4xl font-serif text-[#0d2a5c]">
                {{ $kuotaTerpakai }} <span class="text-lg text-[#7a7d8b] font-sans">/ {{ $firm->kuota_maks }} calon advokat</span>
            </p>
            <div class="mt-4 flex gap-1.5">
                @foreach ($kuotaSlots as $filled)
                    <div class="flex-1 h-[30px] rounded-md {{ $filled ? 'bg-primary' : 'bg-[#edeef2]' }}"></div>
                @endforeach
            </div>
            <p class="text-sm text-[#7a7d8b] mt-4">Hard-cap Peraturan PERADI No. 1/2015: maksimum {{ $firm->kuota_maks }} calon advokat dalam waktu bersamaan untuk kantor + advokat pendamping.</p>
        </x-card>

        <x-card class="p-6">
            <h2 class="font-semibold text-[15px]">Perlu tindakan</h2>
            <div class="mt-3 divide-y divide-[#eceef2]">
                @forelse ($firmTasks as $t)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium truncate">{{ $t['label'] }}</p>
                            <p class="text-xs text-[#7a7d8b]">{{ $t['sub'] }}</p>
                        </div>
                        <a href="{{ $t['route'] }}" class="text-xs font-medium text-primary border border-[#c9d9f0] rounded-lg px-3 py-1.5 whitespace-nowrap hover:bg-[#eef3fb]">{{ $t['cta'] }}</a>
                    </div>
                @empty
                    <p class="py-6 text-sm text-[#7a7d8b]">Tidak ada tindakan yang perlu dilakukan saat ini.</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <x-card class="overflow-hidden">
        <div class="p-6 border-b border-[#eceef2]">
            <h2 class="font-semibold text-[15px]">Pemagang aktif</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] tracking-[0.1em] uppercase text-[#7a7d8b] bg-[#f7f8fa]">
                    <th class="px-6 py-3 font-medium">Nama</th>
                    <th class="px-6 py-3 font-medium">Bidang</th>
                    <th class="px-6 py-3 font-medium">Progres</th>
                    <th class="px-6 py-3 font-medium">Logbook bulan ini</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#eceef2]">
                @forelse ($pemagang as $row)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ $row['ca']->user->name }}</p>
                            <p class="text-xs text-[#7a7d8b]">{{ $row['ca']->kode_ca }}</p>
                        </td>
                        <td class="px-6 py-4 text-[#5b5d68]">{{ $row['ca']->bidang_penempatan }}</td>
                        <td class="px-6 py-4">
                            <div class="w-32 h-1.5 rounded-full bg-[#eceef2] overflow-hidden">
                                <div class="h-full bg-primary" style="width: {{ $row['ca']->progresPersen() }}%"></div>
                            </div>
                            <p class="text-xs text-[#7a7d8b] mt-1">bulan {{ $row['ca']->bulanBerjalan() }}/{{ $row['ca']->masa_magang_bulan }}</p>
                        </td>
                        <td class="px-6 py-4"><x-tag :variant="$row['variant']">{{ $row['logStatus'] }}</x-tag></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-[#7a7d8b]">Belum ada pemagang aktif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</x-layout>
