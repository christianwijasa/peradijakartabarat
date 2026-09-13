@php
    $tagVariant = ['lengkap' => 'ok', 'berjalan' => 'info', 'menunggu' => 'wait'];
    $tagLabel = ['lengkap' => 'Lengkap', 'berjalan' => 'Berjalan', 'menunggu' => 'Menunggu'];
@endphp
<x-layout
    crumb="Calon Advokat"
    title="Paket berkas sumpah"
    subtitle="Seluruh berkas digabung otomatis menjadi satu paket pengajuan sumpah di Pengadilan Tinggi."
    :menu="\App\Support\SidebarMenu::calon('berkas')"
    :user-meta="$ca->kode_ca.' · Alumni Lulus UPA'"
>
    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start">
        <x-card class="overflow-hidden">
            <div class="p-6 border-b border-[#eceef2]">
                <h2 class="font-semibold text-[15px]">Paket berkas sumpah advokat</h2>
                <p class="text-sm text-[#7a7d8b] mt-1">Digabung otomatis dari data admisi (PKPA/UPA) dan logbook magang.</p>
            </div>
            <div class="divide-y divide-[#eceef2]">
                @foreach ($berkas as $b)
                    <div class="flex items-start justify-between gap-4 px-6 py-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium">{{ $b->label() }}</p>
                            <p class="text-xs text-[#7a7d8b] mt-0.5">{{ $b->sumber }}</p>
                            
                            @if (in_array($b->jenis, ['sertifikat_pkpa', 'sertifikat_lulus_upa', 'ijazah_transkrip', 'sertifikat_selesai_magang']) && $b->status !== 'lengkap')
                                <form method="POST" action="{{ route('calon.berkas.upload', $b) }}" enctype="multipart/form-data" class="mt-2" x-data="{ uploading: false }">
                                    @csrf
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="file"
                                            name="file"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                            class="text-xs"
                                            @change="uploading = false"
                                        >
                                        <x-btn type="submit" x-bind:disabled="uploading" @click="uploading = true">Unggah</x-btn>
                                    </div>
                                    <p class="text-xs text-[#7a7d8b] mt-1">Format: PDF, JPG, PNG (max 10MB)</p>
                                </form>
                            @endif

                            @if ($b->file_path && $b->status === 'lengkap')
                                <a href="{{ route('calon.berkas.download', $b) }}" class="text-xs text-primary hover:underline mt-2 inline-block">
                                    Unduh berkas
                                </a>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <x-tag :variant="$tagVariant[$b->status]">{{ $tagLabel[$b->status] }}</x-tag>
                            <span class="text-sm text-[#7a7d8b] w-14 text-right">{{ $b->ukuran ?? '—' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>

        <div class="bg-[#0d2a5c] text-white rounded-xl p-6">
            <p class="text-[11px] tracking-[0.14em] uppercase text-[#aec1e4]">Status audit akhir</p>
            <p class="mt-2 font-serif text-xl">
                {{ $bisaUnduh ? 'Paket siap diunduh' : 'Terkunci hingga bulan ke-'.$ca->masa_magang_bulan }}
            </p>
            <p class="text-sm text-[#aec1e4] mt-2 leading-relaxed">
                Unduhan paket terbuka setelah sertifikat selesai magang diterbitkan kantor hukum dan audit Admin DPC dinyatakan lulus.
            </p>
            <button
                @disabled(! $bisaUnduh)
                class="mt-5 w-full rounded-lg border border-white/30 py-2.5 text-sm {{ $bisaUnduh ? 'bg-white text-navy font-medium hover:bg-[#f1f4fa]' : 'text-white/80 cursor-not-allowed' }}"
            >
                {{ $bisaUnduh ? 'Unduh paket' : 'Unduh paket (belum tersedia)' }}
            </button>
        </div>
    </div>
</x-layout>
