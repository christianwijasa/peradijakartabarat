@php
    $tagVariant = ['COMPLETE' => 'ok', 'IN_PROGRESS' => 'info', 'PENDING' => 'wait'];
    $tagLabel = ['COMPLETE' => 'Lengkap', 'IN_PROGRESS' => 'Berjalan', 'PENDING' => 'Menunggu'];
@endphp
<x-layout
    crumb="Calon Advokat"
    title="Paket berkas sumpah"
    subtitle="Seluruh berkas digabung otomatis menjadi satu paket pengajuan sumpah di Pengadilan Tinggi."
    :menu="\App\Support\SidebarMenu::candidate('berkas')"
    :user-meta="$ca->candidate_code.' · Alumni Lulus UPA'"
>
    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start">
        <x-card class="overflow-hidden">
            <div class="p-5 md:p-6 border-b border-line">
                <h2 class="app-section-title">Paket berkas sumpah advokat</h2>
                <p class="text-sm text-muted-foreground mt-1 leading-relaxed">Digabung otomatis dari data admisi (PKPA/UPA) dan logbook magang.</p>
            </div>
            <div class="divide-y divide-line">
                @foreach ($berkas as $b)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 md:px-6 py-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium">{{ $b->label() }}</p>
                            <p class="text-xs text-muted-foreground mt-0.5">{{ $b->source_label }}</p>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                            <x-tag :variant="$tagVariant[$b->status]">{{ $tagLabel[$b->status] }}</x-tag>
                            <span class="text-sm text-muted-foreground tabular-nums">{{ $b->file_size_label ?? '—' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-navy-callout
            eyebrow="Status audit akhir"
            :title="$bisaUnduh ? 'Paket siap diunduh' : 'Terkunci hingga bulan ke-'.$ca->internship_months"
            description="Unduhan paket terbuka setelah sertifikat selesai magang diterbitkan kantor hukum dan audit Admin DPC dinyatakan lulus."
        >
            <x-slot:actions>
                <button
                    type="button"
                    @disabled(! $bisaUnduh)
                    @class([
                        $bisaUnduh ? 'app-btn-on-navy-enabled' : 'app-btn-on-navy-disabled',
                    ])
                >
                    {{ $bisaUnduh ? 'Unduh paket' : 'Unduh paket (belum tersedia)' }}
                </button>
            </x-slot:actions>
        </x-navy-callout>
    </div>
</x-layout>
