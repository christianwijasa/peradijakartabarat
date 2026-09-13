@php
    $alertDot = ['warn' => 'bg-[#b8862f]', 'bad' => 'bg-[#c2492f]', 'info' => 'bg-[#2a4d78]'];
    $auditVariant = ['lulus_audit' => 'ok', 'dalam_proses' => 'wait', 'berkas_kurang' => 'bad'];
    $auditLabel = ['lulus_audit' => 'Lulus audit', 'dalam_proses' => 'Dalam proses', 'berkas_kurang' => 'Berkas kurang'];
@endphp
<x-layout
    crumb="Admin DPC Jakarta Barat"
    title="Monitoring & audit akhir"
    subtitle="Pantau kepatuhan logbook real-time dan audit kelayakan sumpah calon advokat."
    :menu="\App\Support\SidebarMenu::admin('monitoring')"
    user-meta="Admin Bidang Magang"
>
    <x-card class="p-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-[15px]">Kepatuhan logbook per kantor hukum</h2>
            <p class="text-xs text-[#7a7d8b]">Periode {{ now()->translatedFormat('F Y') }}</p>
        </div>
        <div class="mt-4 flex flex-col gap-4">
            @forelse ($kepatuhan as $row)
                @php
                    $color = $row['pct'] >= 85 ? '#3f7a5c' : ($row['pct'] >= 60 ? '#b8862f' : '#c2492f');
                @endphp
                <div class="flex items-center gap-4">
                    <p class="w-44 shrink-0 text-sm truncate">{{ $row['firm']->nama }}</p>
                    <div class="flex-1 h-2 rounded-full bg-[#eceef2] overflow-hidden">
                        <div class="h-full rounded-full" style="width: {{ $row['pct'] }}%; background: {{ $color }};"></div>
                    </div>
                    <p class="w-20 shrink-0 text-sm text-right text-[#5b5d68]">{{ $row['pct'] }}% entri</p>
                </div>
            @empty
                <p class="text-sm text-[#7a7d8b]">Belum ada kantor hukum terverifikasi.</p>
            @endforelse
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
        <x-card class="p-6">
            <h2 class="font-semibold text-[15px]">Peringatan sistem</h2>
            <div class="mt-3 divide-y divide-[#eceef2]">
                @forelse ($alerts as $a)
                    <div class="flex items-start gap-3 py-3">
                        <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $alertDot[$a['variant']] }}"></span>
                        <div>
                            <p class="text-sm font-medium">{{ $a['judul'] }}</p>
                            <p class="text-xs text-[#7a7d8b] mt-0.5">{{ $a['detail'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-sm text-[#7a7d8b]">Tidak ada peringatan saat ini.</p>
                @endforelse
            </div>
        </x-card>

        <x-card class="p-6">
            <h2 class="font-semibold text-[15px]">Audit akhir · siap sumpah</h2>
            <div class="mt-3 divide-y divide-[#eceef2]">
                @forelse ($auditList as $row)
                    <div class="py-3" x-data="{ open: false }">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium truncate">{{ $row['ca']->user->name }}</p>
                                <p class="text-xs text-[#7a7d8b]">{{ $row['detail'] }}</p>
                            </div>
                            <x-tag :variant="$auditVariant[$row['status']]">{{ $auditLabel[$row['status']] }}</x-tag>
                            <button @click="open = !open" class="text-xs text-primary hover:underline whitespace-nowrap">
                                <span x-text="open ? 'Tutup' : 'Ubah status'"></span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('admin.monitoring.audit', $row['ca']) }}" x-show="open" x-cloak class="mt-3 flex flex-col gap-3 p-3 bg-[#f7f8fa] rounded-lg">
                            @csrf
                            <div>
                                <label class="text-xs text-[#7a7d8b]">Status audit</label>
                                <select name="status" class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                                    <option value="lulus_audit" {{ $row['status'] === 'lulus_audit' ? 'selected' : '' }}>Lulus audit</option>
                                    <option value="dalam_proses" {{ $row['status'] === 'dalam_proses' ? 'selected' : '' }}>Dalam proses</option>
                                    <option value="berkas_kurang" {{ $row['status'] === 'berkas_kurang' ? 'selected' : '' }}>Berkas kurang</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-[#7a7d8b]">Catatan</label>
                                <textarea name="catatan" rows="2" placeholder="Catatan untuk calon advokat..."
                                    class="mt-1 w-full rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary"></textarea>
                            </div>
                            <div class="flex gap-2">
                                <x-btn type="submit">Simpan</x-btn>
                                <button type="button" @click="open = false" class="text-xs text-[#7a7d8b] hover:underline px-3">Batal</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <p class="py-6 text-sm text-[#7a7d8b]">Belum ada calon advokat mendekati audit akhir.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-layout>
