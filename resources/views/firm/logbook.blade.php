<x-layout
    crumb="Law Firm · Pendamping"
    title="Review logbook pemagang"
    subtitle="Setujui entri harian dan tanda tangani rekap bulanan secara digital."
    :menu="\App\Support\SidebarMenu::firm('logbook')"
    :user-meta="'Advokat Pendamping · '.$pendamping->lawFirm->nama"
>
    @if ($calonList->count() > 1)
        <form method="GET" class="flex items-center gap-2">
            <label class="text-xs text-[#7a7d8b]">Pemagang</label>
            <select name="calon" onchange="this.form.submit()" class="rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                @foreach ($calonList as $ca)
                    <option value="{{ $ca->id }}" @selected($selected && $selected->id === $ca->id)>{{ $ca->user->name }} · {{ $ca->kode_ca }}</option>
                @endforeach
            </select>
        </form>
    @endif

    @if (! $selected)
        <x-card class="p-8 text-center text-sm text-[#7a7d8b]">Belum ada calon advokat yang dibimbing.</x-card>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start">
            <x-card class="p-6">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-semibold text-[15px]">Rekap bulanan · {{ $bulanLabel }}</h2>
                    <p class="text-xs text-[#7a7d8b]">{{ $selected->user->name }} · {{ $selected->kode_ca }}</p>
                </div>
                <div class="mt-3 divide-y divide-[#eceef2]">
                    @forelse ($entries->where('status', '!=', 'disetujui') as $e)
                        <div class="py-4 flex items-start gap-4">
                            <div class="w-14 shrink-0 text-xs text-[#7a7d8b] pt-0.5">{{ $e->tanggal->translatedFormat('j M') }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-[#7a7d8b]">{{ $e->jenis_kegiatan }} · {{ rtrim(rtrim(number_format($e->jam, 1), '0'), '.') }} jam</p>
                                <p class="text-sm mt-1">{{ $e->uraian }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <form method="POST" action="{{ route('firm.logbook.setujui', $e) }}">
                                    @csrf
                                    <x-btn class="!px-3 !py-1.5">Setujui</x-btn>
                                </form>
                                <form method="POST" action="{{ route('firm.logbook.revisi', $e) }}">
                                    @csrf
                                    <x-btn variant="danger-outline" class="!px-3 !py-1.5">Revisi</x-btn>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-[#7a7d8b]">Semua entri bulan ini sudah disetujui.</p>
                    @endforelse
                </div>

                @if ($entries->where('status', 'disetujui')->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-[#eceef2]">
                        <p class="text-xs text-[#7a7d8b] mb-2">Sudah disetujui</p>
                        <div class="flex flex-col gap-1.5">
                            @foreach ($entries->where('status', 'disetujui') as $e)
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-xs text-[#7a7d8b] w-14 shrink-0">{{ $e->tanggal->translatedFormat('j M') }}</span>
                                    <span class="flex-1 truncate text-[#5b5d68]">{{ $e->jenis_kegiatan }}</span>
                                    <x-tag variant="ok">Disetujui</x-tag>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </x-card>

            <x-card class="p-6">
                <h2 class="font-semibold text-[15px]">Tanda tangan digital</h2>
                <div class="mt-3 border border-dashed border-[#d8dbe3] rounded-xl px-4 py-6 text-center">
                    <p class="font-serif text-lg text-[#0d2a5c]">{{ $pendamping->nama }}</p>
                    <p class="text-xs text-[#7a7d8b] mt-1">Sertifikat e-sign aktif</p>
                    <p class="text-xs text-[#7a7d8b]">KTA {{ $pendamping->kta_nomor }}</p>
                </div>
                @php $approvedCount = $entries->where('status', 'disetujui')->count(); @endphp
                <p class="text-sm text-[#5b5d68] mt-4">
                    {{ $approvedCount }} dari {{ $entries->count() }} entri disetujui. Rekap bulanan terkirim ke Admin DPC setelah seluruh entri ditandatangani.
                </p>
                <form method="POST" action="{{ route('firm.logbook.tandatangani-semua', $rekap) }}" class="mt-4">
                    @csrf
                    <x-btn class="w-full justify-center" :disabled="$entries->isEmpty() || $rekap->status === 'ditandatangani'">
                        {{ $rekap->status === 'ditandatangani' ? 'Rekap ditandatangani' : 'Setujui & tanda tangani semua' }}
                    </x-btn>
                </form>
            </x-card>
        </div>
    @endif
</x-layout>
