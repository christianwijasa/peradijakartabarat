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
                        $itemsByLabel = $ca->fresh()->checklistItems->keyBy('label');
                    @endphp
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4 px-5 md:px-6 py-5">
                        <div class="lg:w-64 shrink-0">
                            <p class="font-medium">{{ $ca->user->name }}</p>
                            <p class="text-xs text-muted-foreground mt-0.5">NIK {{ $ca->national_id_number ? substr($ca->national_id_number, 0, 4).'••••' : '—' }} · UPA {{ $ca->bar_exam_cohort ?? '—' }}</p>
                        </div>
                        <div class="flex-1 app-card-muted px-4 py-3 flex flex-col gap-3">
                            @foreach (\App\Support\CandidateVerificationChecklist::defaultItems() as $def)
                                @php
                                    $item = $itemsByLabel->get($def['label']);
                                    $icon = \App\Support\CandidateVerificationChecklist::itemIconClass($item);
                                @endphp
                                <div class="flex flex-col sm:flex-row sm:items-start gap-2 sm:gap-3">
                                    <div class="flex items-start gap-2.5 text-sm leading-snug flex-1 min-w-0">
                                        <span @class([$icon, 'mt-1.5 shrink-0'])></span>
                                        <span class="min-w-0">
                                            {{ $def['label'] }}
                                            @if ($item?->document_url)
                                                · <a href="{{ $item->document_url }}" target="_blank" rel="noopener noreferrer" class="text-primary underline underline-offset-2 text-xs">Buka link</a>
                                            @elseif ($item?->hasDocumentReference())
                                                <span class="text-xs text-muted-foreground"> · Berkas tersimpan</span>
                                            @endif
                                            @if ($item?->admin_note)
                                                <span class="block text-xs text-tag-bad-fg mt-1">Catatan: {{ $item->admin_note }}</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if ($item)
                                        <div class="flex flex-col gap-2 shrink-0 sm:max-w-[220px] pl-4 sm:pl-0">
                                            @if ($item->is_checked)
                                                <x-tag variant="ok" class="self-start">Disetujui</x-tag>
                                            @else
                                                <form method="POST" action="{{ route('admin.verification.checklist.setujui', $item) }}">
                                                    @csrf
                                                    <x-btn class="w-full !px-3 !py-2">Setujui berkas</x-btn>
                                                </form>
                                                <form method="POST" action="{{ route('admin.verification.checklist.tolak', $item) }}" class="flex flex-col gap-1.5">
                                                    @csrf
                                                    <textarea
                                                        name="admin_note"
                                                        rows="2"
                                                        required
                                                        maxlength="500"
                                                        placeholder="Catatan revisi untuk calon advokat"
                                                        class="app-input text-xs !py-2"
                                                    ></textarea>
                                                    <x-btn type="submit" variant="danger-outline" class="w-full !px-3 !py-2">Minta revisi</x-btn>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="app-queue-actions">
                            @if ($queueStatus === 'belum_lengkap')
                                <x-tag variant="bad">Belum lengkap</x-tag>
                            @elseif ($queueStatus === 'siap')
                                <x-tag variant="wait">Siap disetujui</x-tag>
                            @else
                                <x-tag variant="info">Menunggu review</x-tag>
                            @endif
                            <form method="POST" action="{{ route('admin.verification.candidate.setujui', $ca) }}" class="w-full sm:w-auto">
                                @csrf
                                <x-btn class="w-full sm:w-auto" :disabled="$queueStatus !== 'siap'">Setujui admisi</x-btn>
                            </form>
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
