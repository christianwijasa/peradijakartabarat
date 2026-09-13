@php
    $statusVariant = ['aktif' => 'ok', 'nonaktif' => 'mute'];
    $statusLabel = ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'];
@endphp
<x-layout
    crumb="Law Firm · {{ $firm->nama }}"
    title="Lowongan magang"
    subtitle="Kelola lowongan magang yang tersedia untuk calon advokat."
    :menu="\App\Support\SidebarMenu::firm('lowongan')"
    :user-meta="'Advokat Pendamping · '.$firm->nama"
>
    @if ($firm->status_verifikasi !== 'terverifikasi')
        <x-card class="p-6 text-center">
            <p class="text-sm text-[#7a7d8b]">Kantor hukum belum terverifikasi oleh Admin DPC. Anda tidak dapat membuat lowongan.</p>
        </x-card>
    @else
        <div class="flex justify-end mb-4">
            <x-btn as="a" href="{{ route('firm.lowongan.create') }}">+ Buat lowongan baru</x-btn>
        </div>
    @endif

    <div class="flex flex-col gap-4">
        @forelse ($lowongans as $l)
            <x-card class="p-6">
                <div class="flex flex-col md:flex-row md:items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-medium">{{ $l->judul }}</h3>
                            <x-tag :variant="$statusVariant[$l->status]">{{ $statusLabel[$l->status] }}</x-tag>
                        </div>
                        <p class="text-sm text-[#5b5d68] mt-2">{{ $l->deskripsi }}</p>
                        <div class="flex items-center gap-3 mt-3 text-sm text-[#7a7d8b]">
                            <span>Kuota: {{ $l->pelamar_diterima }}/{{ $l->kuota }}</span>
                            <span>·</span>
                            <span>Total pelamar: {{ $l->total_pelamar }}</span>
                        </div>
                        @if ($l->bidang)
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach ($l->bidang as $bidang)
                                    <span class="text-xs px-2.5 py-1 bg-[#e9eef6] text-primary rounded-full">{{ $bidang }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('firm.lowongan.toggle', $l) }}">
                            @csrf
                            <x-btn variant="ghost">
                                {{ $l->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                            </x-btn>
                        </form>
                        <x-btn as="a" href="{{ route('firm.lowongan.edit', $l) }}" variant="ghost">Edit</x-btn>
                    </div>
                </div>
            </x-card>
        @empty
            <x-card class="p-8 text-center text-sm text-[#7a7d8b]">
                Belum ada lowongan yang dibuat. Klik tombol "Buat lowongan baru" untuk memulai.
            </x-card>
        @endforelse
    </div>
</x-layout>
