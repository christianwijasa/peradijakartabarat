@php
    $tagVariant = [
        'ACCEPTED' => 'ok', 'INTERVIEW' => 'info', 'CV_REVIEW' => 'wait',
        'REJECTED' => 'bad', 'SUBMITTED' => 'mute',
    ];
    $tagLabel = [
        'ACCEPTED' => 'Diterima', 'INTERVIEW' => 'Interview', 'CV_REVIEW' => 'Review CV',
        'REJECTED' => 'Tidak lanjut', 'SUBMITTED' => 'Terkirim',
    ];
@endphp
<x-layout
    crumb="Calon Advokat"
    title="InternshipApplication saya"
    subtitle="Riwayat pengajuan magang dan tahapan seleksi di setiap kantor hukum."
    :menu="\App\Support\SidebarMenu::candidate('lamaran')"
    :user-meta="Auth::user()->candidateAdvocate->candidate_code.' · Alumni Lulus UPA'"
>
    <x-card class="overflow-hidden">
        @if ($internshipApplications->isEmpty())
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
                    @foreach ($internshipApplications as $l)
                        <tr>
                            <td class="px-6 py-4 font-medium">{{ $l->jobPosting->lawFirm->name }}</td>
                            <td class="px-6 py-4 text-[#5b5d68]">{{ $l->jobPosting->title }}</td>
                            <td class="px-6 py-4 text-[#5b5d68]">{{ $l->applied_on->translatedFormat('j M Y') }}</td>
                            <td class="px-6 py-4"><x-tag :variant="$tagVariant[$l->status]">{{ $tagLabel[$l->status] }}</x-tag></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-card>
</x-layout>
