<x-layout
    crumb="Admin DPC Jakarta Barat"
    title="Verifikasi dual-level"
    subtitle="Validasi kelulusan alumni serta kelayakan kantor hukum dan advokat pendamping."
    :menu="\App\Support\SidebarMenu::admin('verification')"
    user-meta="Admin Bidang Magang"
>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        @foreach ($stats as $s)
            <x-stat-tile :value="$s['value']" :label="$s['label']" />
        @endforeach
    </div>

    <x-card class="overflow-hidden">
        <div class="p-4 md:p-5 border-b border-line">
            <nav class="app-tab-bar" aria-label="Tab verifikasi">
                <a href="{{ route('admin.verification', ['tab' => 'candidate']) }}"
                   @class([$tab === 'candidate' ? 'app-tab-active' : 'app-tab-idle'])>
                    Calon Advokat
                </a>
                <a href="{{ route('admin.verification', ['tab' => 'firm']) }}"
                   @class([$tab === 'firm' ? 'app-tab-active' : 'app-tab-idle'])>
                    Law Firm & Pendamping
                </a>
            </nav>
        </div>

        <div class="divide-y divide-line">
            @if ($tab === 'candidate')
                @forelse ($queueCalon as $ca)
                    @php
                        $queueStatus = \App\Support\CandidateVerificationChecklist::adminQueueStatus($ca);
                        $itemsByLabel = $ca->checklistItems->keyBy('label');
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 px-5 md:px-6 py-5">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium">{{ $ca->user->name }}</p>
                            <p class="text-xs text-muted-foreground mt-0.5">
                                {{ $ca->candidate_code }}
                                · NIK {{ $ca->national_id_number ? substr($ca->national_id_number, 0, 4).'••••' : '—' }}
                                · UPA {{ $ca->bar_exam_cohort ?? '—' }}
                            </p>
                            <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1.5">
                                @foreach (\App\Support\CandidateVerificationChecklist::defaultItems() as $def)
                                    @php $item = $itemsByLabel->get($def['label']); @endphp
                                    <span class="inline-flex items-center gap-1.5 text-xs text-muted-foreground">
                                        <span @class([\App\Support\CandidateVerificationChecklist::itemIconClass($item), 'shrink-0'])></span>
                                        <span class="sr-only">{{ $def['label'] }}:</span>
                                        @if ($item?->is_checked)
                                            OK
                                        @elseif ($item?->admin_note)
                                            Revisi
                                        @elseif ($item?->hasDocumentReference())
                                            Review
                                        @else
                                            Kosong
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="app-queue-actions shrink-0">
                            @if ($queueStatus === 'belum_lengkap')
                                <x-tag variant="bad">Belum lengkap</x-tag>
                            @elseif ($queueStatus === 'siap')
                                <x-tag variant="wait">Siap disetujui</x-tag>
                            @else
                                <x-tag variant="info">Menunggu review</x-tag>
                            @endif
                            <x-btn as="a" href="{{ route('admin.verification.candidate.show', $ca) }}" class="w-full sm:w-auto">
                                Review berkas
                            </x-btn>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-muted-foreground">Tidak ada calon advokat menunggu verifikasi.</p>
                @endforelse
            @else
                @forelse ($queueFirm as $firm)
                    @php
                        $hasChecklist = $firm->checklistItems->isNotEmpty();
                        $siap = $hasChecklist && $firm->checklistItems->every(fn ($c) => $c->is_checked);
                    @endphp
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4 px-5 md:px-6 py-5">
                        <div class="lg:w-64 shrink-0">
                            <p class="font-medium">{{ $firm->name }}</p>
                            <p class="text-xs text-muted-foreground mt-0.5">{{ $firm->ministry_registration_number ?? 'SK Kemenkumham —' }} · {{ $firm->supervisingLawyers()->count() }} advokat pendamping</p>
                        </div>
                        <div class="flex-1 app-card-muted px-4 py-3 flex flex-col gap-2">
                            @foreach ($firm->checklistItems as $item)
                                <div class="flex items-start gap-2.5 text-sm leading-snug">
                                    <span @class([$item->is_checked ? 'app-check-ok' : 'app-check-bad', 'mt-1.5'])></span>
                                    <span>{{ $item->label }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="app-queue-actions">
                            <x-tag :variant="$siap ? 'wait' : 'bad'">{{ $siap ? 'Penetapan kuota' : 'Perlu perbaikan' }}</x-tag>
                            <form method="POST" action="{{ route('admin.verification.firm.tetapkan-kuota', $firm) }}" class="w-full sm:w-auto">
                                @csrf
                                <x-btn class="w-full sm:w-auto">Tetapkan kuota</x-btn>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-muted-foreground">Tidak ada kantor hukum menunggu verifikasi.</p>
                @endforelse
            @endif
        </div>
    </x-card>
</x-layout>
