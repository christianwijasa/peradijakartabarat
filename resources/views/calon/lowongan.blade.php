<x-layout
    crumb="Calon Advokat"
    title="Cari lowongan magang"
    subtitle="Hanya kantor hukum terverifikasi Admin DPC dengan kuota bimbingan tersisa yang dapat menerima lamaran."
    :menu="\App\Support\SidebarMenu::calon('lowongan')"
    :user-meta="Auth::user()->calonAdvokat->kode_ca.' · Alumni Lulus UPA'"
>
    <form method="GET" class="flex flex-col md:flex-row gap-2.5">
        <input
            type="text" name="q" value="{{ $keyword }}" placeholder="Cari kantor hukum atau kata kunci"
            class="flex-1 rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary"
        >
        <div class="flex gap-2 overflow-x-auto">
            @foreach ($bidangFilter as $label)
                <button
                    type="submit" name="bidang" value="{{ $label }}"
                    class="rounded-lg px-3.5 py-2 text-[12.5px] whitespace-nowrap border shrink-0
                        {{ $filter === $label ? 'border-primary bg-[#eef3fb] text-primary' : 'border-[#e0e2e9] bg-white text-[#4a4c57]' }}"
                >{{ $label }}</button>
            @endforeach
        </div>
    </form>

    <div class="flex flex-col gap-4">
        @forelse ($lowongans as $lowongan)
            @php $sudahMelamar = in_array($lowongan->law_firm_id, $lamaranFirmIds); @endphp
            <x-card class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-serif text-lg text-[#0d2a5c]">{{ $lowongan->lawFirm->nama }}</p>
                        <x-tag variant="ok">Terverifikasi DPC</x-tag>
                    </div>
                    <p class="text-sm font-medium mt-1">{{ $lowongan->judul }}</p>
                    <p class="text-sm text-[#5b5d68] mt-1">{{ $lowongan->deskripsi }}</p>
                    <div class="flex gap-2 mt-3 flex-wrap">
                        @foreach ($lowongan->bidang ?? [] as $b)
                            <x-tag variant="mute">{{ $b }}</x-tag>
                        @endforeach
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm text-[#5b5d68]">Sisa slot <span class="font-semibold text-[#191a20]">{{ $lowongan->slotTersisa() }}</span></p>
                    <p class="text-xs text-[#7a7d8b] mb-3">dari kuota {{ $lowongan->kuota }}</p>
                    <form method="POST" action="{{ route('calon.lowongan.lamar', $lowongan) }}">
                        @csrf
                        <x-btn :variant="$sudahMelamar ? 'done' : 'primary'" :disabled="$sudahMelamar">
                            {{ $sudahMelamar ? 'Lamaran terkirim' : 'Ajukan lamaran' }}
                        </x-btn>
                    </form>
                </div>
            </x-card>
        @empty
            <x-card class="p-8 text-center text-sm text-[#7a7d8b]">Tidak ada lowongan yang cocok dengan pencarianmu.</x-card>
        @endforelse
    </div>
</x-layout>
