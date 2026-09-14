@php
    $tagVariant = ['APPROVED' => 'ok', 'PENDING_SIGNATURE' => 'wait', 'REVISION' => 'bad'];
    $tagLabel = ['APPROVED' => 'Disetujui', 'PENDING_SIGNATURE' => 'Menunggu ttd', 'REVISION' => 'Revisi'];
@endphp
<x-layout
    crumb="Calon Advokat"
    title="Logbook digital"
    subtitle="Catat riset hukum dan pendampingan sidang harian. Pendamping menandatangani rekap bulanan."
    :menu="\App\Support\SidebarMenu::calon('logbook')"
    :user-meta="Auth::user()->candidateAdvocate->candidate_code.' · Alumni Lulus UPA'"
>
    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start">
        <x-card class="p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-[15px]">Catatan {{ $bulanLabel }}</h2>
                <p class="text-xs text-[#7a7d8b]">{{ $entries->count() }} entri · {{ $pending }} menunggu tanda tangan</p>
            </div>
            <div class="mt-3 divide-y divide-[#eceef2]">
                @forelse ($entries as $e)
                    <div class="py-4">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs text-[#7a7d8b]">{{ $e->entry_date->translatedFormat('j M') }}</span>
                            <x-tag variant="mute">{{ $e->activity_type }}</x-tag>
                            <span class="text-xs text-[#7a7d8b]">{{ rtrim(rtrim(number_format($e->hours, 1), '0'), '.') }} jam</span>
                            <span class="flex-1"></span>
                            <x-tag :variant="$tagVariant[$e->status]">{{ $tagLabel[$e->status] }}</x-tag>
                        </div>
                        <p class="text-sm text-[#191a20] mt-2">{{ $e->description }}</p>
                        @if ($e->status === 'REVISION' && $e->revision_notes)
                            <p class="text-xs text-tag-bad-fg bg-tag-bad-bg rounded-md px-3 py-2 mt-2">Catatan revisi: {{ $e->revision_notes }}</p>
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
                    <select name="activity_type" class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                        <option>Riset hukum</option>
                        <option>Pendampingan sidang</option>
                        <option>Drafting dokumen</option>
                        <option>Konsultasi internal</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-[#7a7d8b]">Uraian kegiatan</label>
                    <textarea name="description" rows="5" required placeholder="Contoh: Riset yurisprudensi terkait actio pauliana untuk perkara No. 55/Pdt.Sus-PKPU."
                        class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-tag-bad-fg mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs text-[#7a7d8b]">Durasi (jam)</label>
                    <input type="number" step="0.5" min="0.5" max="24" name="hours" value="4"
                        class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                </div>
                <x-btn type="submit" class="w-full justify-center">Kirim ke pendamping</x-btn>
                <p class="text-xs text-[#7a7d8b]">Catatan terkirim akan menunggu tanda tangan digital advokat pendamping pada rekap bulanan.</p>
            </form>
        </x-card>
    </div>
</x-layout>
