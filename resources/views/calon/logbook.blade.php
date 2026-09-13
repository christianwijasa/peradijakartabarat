@php
    $tagVariant = ['disetujui' => 'ok', 'menunggu_ttd' => 'wait', 'revisi' => 'bad'];
    $tagLabel = ['disetujui' => 'Disetujui', 'menunggu_ttd' => 'Menunggu ttd', 'revisi' => 'Revisi'];
@endphp
<x-layout
    crumb="Calon Advokat"
    title="Logbook digital"
    subtitle="Catat riset hukum dan pendampingan sidang harian. Pendamping menandatangani rekap bulanan."
    :menu="\App\Support\SidebarMenu::calon('logbook')"
    :user-meta="Auth::user()->calonAdvokat->kode_ca.' · Alumni Lulus UPA'"
>
    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start">
        <x-card class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-[15px]">Catatan {{ $bulanLabel }}</h2>
                <p class="text-xs text-[#7a7d8b]">{{ $entries->count() }} entri · {{ $pending }} menunggu tanda tangan</p>
            </div>
            <div class="mt-3 divide-y divide-[#eceef2]">
                @forelse ($entries as $e)
                    <div class="py-4" x-data="{ editing: false }">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs text-[#7a7d8b]">{{ $e->tanggal->translatedFormat('j M') }}</span>
                            <x-tag variant="mute">{{ $e->jenis_kegiatan }}</x-tag>
                            <span class="text-xs text-[#7a7d8b]">{{ rtrim(rtrim(number_format($e->jam, 1), '0'), '.') }} jam</span>
                            <span class="flex-1"></span>
                            <x-tag :variant="$tagVariant[$e->status]">{{ $tagLabel[$e->status] }}</x-tag>
                        </div>
                        <p class="text-sm text-[#191a20] mt-2" x-show="!editing">{{ $e->uraian }}</p>
                        @if ($e->status === 'disetujui' && $e->ditandatangani_oleh)
                            <div x-show="!editing" class="mt-3 border-l-2 border-tag-ok-fg pl-3 py-2 bg-tag-ok-bg/30 rounded-r">
                                <p class="text-xs text-tag-ok-fg font-medium">✓ Ditandatangani digital oleh {{ $e->ditandatangani_oleh }}</p>
                                <p class="text-xs text-[#7a7d8b] mt-0.5">{{ $e->tanggal_ttd?->translatedFormat('j F Y, H:i') }} · KTA {{ $e->kta_penandatangan }}</p>
                            </div>
                        @endif
                        @if ($e->status === 'revisi' && $e->catatan_revisi)
                            <div x-show="!editing">
                                <p class="text-xs text-tag-bad-fg bg-tag-bad-bg rounded-md px-3 py-2 mt-2">Catatan revisi: {{ $e->catatan_revisi }}</p>
                                <button @click="editing = true" class="text-xs text-primary hover:underline mt-2">Edit & kirim ulang</button>
                            </div>
                            <form method="POST" action="{{ route('calon.logbook.update', $e->id) }}" x-show="editing" class="mt-3 flex flex-col gap-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="text-xs text-[#7a7d8b]">Jenis kegiatan</label>
                                    <select name="jenis_kegiatan" class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                                        <option {{ $e->jenis_kegiatan === 'Riset hukum' ? 'selected' : '' }}>Riset hukum</option>
                                        <option {{ $e->jenis_kegiatan === 'Pendampingan sidang' ? 'selected' : '' }}>Pendampingan sidang</option>
                                        <option {{ $e->jenis_kegiatan === 'Drafting dokumen' ? 'selected' : '' }}>Drafting dokumen</option>
                                        <option {{ $e->jenis_kegiatan === 'Konsultasi internal' ? 'selected' : '' }}>Konsultasi internal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-[#7a7d8b]">Uraian kegiatan</label>
                                    <textarea name="uraian" rows="4" required class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">{{ $e->uraian }}</textarea>
                                </div>
                                <div>
                                    <label class="text-xs text-[#7a7d8b]">Durasi (jam)</label>
                                    <input type="number" step="0.5" min="0.5" max="24" name="jam" value="{{ $e->jam }}"
                                        class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                                </div>
                                <div class="flex gap-2">
                                    <x-btn type="submit">Kirim ulang</x-btn>
                                    <button type="button" @click="editing = false" class="text-xs text-[#7a7d8b] hover:underline px-3">Batal</button>
                                </div>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-[#7a7d8b]">Belum ada catatan bulan ini.</p>
                @endforelse
            </div>
        </x-card>

        <x-card class="p-6">
            <h2 class="font-semibold text-[15px]">Tambah catatan harian</h2>
            <form method="POST" action="{{ route('calon.logbook.store') }}" class="mt-4 flex flex-col gap-4">
                @csrf
                <div>
                    <label class="text-xs text-[#7a7d8b]">Jenis kegiatan</label>
                    <select name="jenis_kegiatan" class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                        <option>Riset hukum</option>
                        <option>Pendampingan sidang</option>
                        <option>Drafting dokumen</option>
                        <option>Konsultasi internal</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-[#7a7d8b]">Uraian kegiatan</label>
                    <textarea name="uraian" rows="5" required placeholder="Contoh: Riset yurisprudensi terkait actio pauliana untuk perkara No. 55/Pdt.Sus-PKPU."
                        class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">{{ old('uraian') }}</textarea>
                    @error('uraian') <p class="text-xs text-tag-bad-fg mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-[#7a7d8b]">Durasi (jam)</label>
                    <input type="number" step="0.5" min="0.5" max="24" name="jam" value="4"
                        class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                </div>
                <x-btn type="submit" class="w-full justify-center">Kirim ke pendamping</x-btn>
                <p class="text-xs text-[#7a7d8b]">Catatan terkirim akan menunggu tanda tangan digital advokat pendamping pada rekap bulanan.</p>
            </form>
        </x-card>
    </div>
</x-layout>
