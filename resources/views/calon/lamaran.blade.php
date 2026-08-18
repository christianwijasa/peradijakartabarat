@php
    $tagVariant = [
        'diterima' => 'ok', 'interview' => 'info', 'review_cv' => 'wait',
        'tidak_lanjut' => 'bad', 'terkirim' => 'mute',
    ];
    $tagLabel = [
        'diterima' => 'Diterima', 'interview' => 'Interview', 'review_cv' => 'Review CV',
        'tidak_lanjut' => 'Tidak lanjut', 'terkirim' => 'Terkirim',
    ];
@endphp
<x-layout
    crumb="Calon Advokat"
    title="Lamaran saya"
    subtitle="Riwayat pengajuan magang dan tahapan seleksi di setiap kantor hukum."
    :menu="\App\Support\SidebarMenu::calon('lamaran')"
    :user-meta="Auth::user()->calonAdvokat->kode_ca.' · Alumni Lulus UPA'"
>
    <x-card class="overflow-hidden">
        @if ($lamarans->isEmpty())
            <p class="p-8 text-center text-sm text-[#7a7d8b]">Kamu belum mengajukan lamaran magang.</p>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] tracking-[0.1em] uppercase text-[#7a7d8b] bg-[#f7f8fa]">
                        <th class="px-6 py-3 font-medium">Kantor Hukum</th>
                        <th class="px-6 py-3 font-medium">Posisi</th>
                        <th class="px-6 py-3 font-medium">Dikirim</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eceef2]">
                    @foreach ($lamarans as $l)
                        <tr>
                            <td class="px-6 py-4 font-medium">{{ $l->lowongan->lawFirm->nama }}</td>
                            <td class="px-6 py-4 text-[#5b5d68]">{{ $l->lowongan->judul }}</td>
                            <td class="px-6 py-4 text-[#5b5d68]">{{ $l->tanggal_lamar->translatedFormat('j M Y') }}</td>
                            <td class="px-6 py-4"><x-tag :variant="$tagVariant[$l->status]">{{ $tagLabel[$l->status] }}</x-tag></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-card>
</x-layout>
