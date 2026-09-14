@php
    $tagVariant = ['SUBMITTED' => 'mute', 'CV_REVIEW' => 'wait', 'INTERVIEW' => 'info', 'ACCEPTED' => 'ok'];
    $tagLabel = ['SUBMITTED' => 'Terkirim', 'CV_REVIEW' => 'Review CV', 'INTERVIEW' => 'Interview', 'ACCEPTED' => 'Diterima'];
@endphp
<x-layout
    crumb="Law Firm · {{ $firm->name }}"
    title="Pelamar magang"
    subtitle="Review CV, jadwalkan interview, dan terbitkan surat penerimaan magang."
    :menu="\App\Support\SidebarMenu::firm('pelamar')"
    :user-meta="'Advokat Pendamping · '.$firm->name"
>
    <div class="flex flex-col gap-4">
        @forelse ($internshipApplications as $l)
            <x-card class="p-6 flex flex-col md:flex-row md:items-center gap-4">
                <div class="w-11 h-11 rounded-full bg-[#e9eef6] text-primary flex items-center justify-center text-sm font-semibold shrink-0">
                    {{ \Illuminate\Support\Str::of($l->candidateAdvocate->user->name)->explode(' ')->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-medium">{{ $l->candidateAdvocate->user->name }}</p>
                        <x-tag variant="ok">Lulus UPA {{ $l->candidateAdvocate->bar_exam_graduation_year }}</x-tag>
                    </div>
                    <p class="text-sm text-[#5b5d68] mt-1">{{ $l->candidateAdvocate->university }} · IPK {{ $l->candidateAdvocate->gpa }}</p>
                    <p class="text-xs text-[#7a7d8b] mt-0.5">Melamar {{ $l->applied_on->translatedFormat('j M Y') }} · {{ $l->jobPosting->title }}</p>
                </div>
                <div class="flex items-center gap-2.5 shrink-0">
                    <x-tag :variant="$tagVariant[$l->status]">{{ $tagLabel[$l->status] }}</x-tag>
                    @if ($l->status !== 'ACCEPTED')
                        <form method="POST" action="{{ route('firm.pelamar.terima', $l) }}">
                            @csrf
                            <x-btn>Terbitkan surat penerimaan</x-btn>
                        </form>
                    @else
                        <x-btn variant="done" disabled>Surat terkirim</x-btn>
                    @endif
                </div>
            </x-card>
        @empty
            <x-card class="p-8 text-center text-sm text-[#7a7d8b]">Belum ada pelamar untuk lowongan kantor ini.</x-card>
        @endforelse
    </div>
</x-layout>
