<x-layout
    crumb="Admin DPC · Verifikasi"
    :title="$ca->user->name"
    :subtitle="'Review berkas admisi · '.$ca->candidate_code"
    :menu="\App\Support\SidebarMenu::admin('verification')"
    user-meta="Admin Bidang Magang"
>
    <div class="mb-4">
        <a href="{{ route('admin.verification', ['tab' => 'candidate']) }}" class="text-sm text-primary hover:underline underline-offset-2">
            ← Kembali ke antrian verifikasi
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_280px] gap-5 items-start">
        <x-card class="overflow-hidden">
            <div class="p-5 md:p-6 border-b border-line">
                <h2 class="app-section-title">Checklist berkas</h2>
                <p class="text-sm text-muted-foreground mt-1">
                    Setujui tiap berkas atau minta revisi dengan catatan yang jelas untuk calon advokat.
                </p>
            </div>

            <div class="divide-y divide-line">
                @foreach (\App\Support\CandidateVerificationChecklist::defaultItems() as $def)
                    @php
                        $item = $itemsByLabel->get($def['label']);
                        $icon = \App\Support\CandidateVerificationChecklist::itemIconClass($item);
                        $needsLink = $def['requires_upload'];
                    @endphp
                    <div class="p-5 md:p-6 flex flex-col gap-4">
                        <div class="flex items-start gap-3">
                            <span @class([$icon, 'mt-2 shrink-0'])></span>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm">{{ $def['label'] }}</p>
                                @if ($item?->document_url)
                                    <p class="text-xs text-muted-foreground mt-2 break-all">
                                        Link berkas:
                                        <a href="{{ $item->document_url }}" target="_blank" rel="noopener noreferrer" class="text-primary underline underline-offset-2">{{ $item->document_url }}</a>
                                    </p>
                                    <p class="mt-2">
                                        <x-btn as="a" href="{{ $item->document_url }}" target="_blank" rel="noopener noreferrer" variant="ghost" class="!px-3 !py-2">
                                            Buka berkas di tab baru
                                        </x-btn>
                                    </p>
                                @elseif ($item?->hasDocumentReference())
                                    <p class="text-xs text-muted-foreground mt-2">Berkas lokal (legacy) tersimpan di sistem.</p>
                                @elseif ($needsLink)
                                    <p class="text-xs text-tag-bad-fg mt-2">Calon advokat belum mengunggah link berkas.</p>
                                @else
                                    <p class="text-xs text-muted-foreground mt-2">Validasi otomatis / data profil & keanggotaan.</p>
                                @endif
                                @if ($item?->admin_note)
                                    <p class="text-sm text-tag-bad-fg mt-3 leading-relaxed rounded-xl border border-tag-bad-bg bg-tag-bad-bg/40 px-3 py-2">
                                        Catatan revisi aktif: {{ $item->admin_note }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if ($item)
                            @if ($item->is_checked)
                                <x-tag variant="ok" class="self-start">Berkas disetujui</x-tag>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1 border-t border-line/80">
                                    <form method="POST" action="{{ route('admin.verification.checklist.setujui', $item) }}" class="md:pt-3">
                                        @csrf
                                        <x-btn class="w-full justify-center">Setujui berkas ini</x-btn>
                                    </form>
                                    <form method="POST" action="{{ route('admin.verification.checklist.tolak', $item) }}" class="flex flex-col gap-2 md:pt-3">
                                        @csrf
                                        <label class="app-label-xs" for="note-{{ $item->id }}">Catatan revisi (wajib jika minta perbaikan)</label>
                                        <textarea
                                            id="note-{{ $item->id }}"
                                            name="admin_note"
                                            rows="4"
                                            required
                                            maxlength="500"
                                            placeholder="Jelaskan apa yang harus diperbaiki calon advokat…"
                                            class="app-input text-sm min-h-[100px]"
                                        >{{ old('admin_note') }}</textarea>
                                        <x-btn type="submit" variant="danger-outline" class="w-full justify-center">Minta revisi berkas ini</x-btn>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>

        <div class="flex flex-col gap-4 xl:sticky xl:top-6">
            <x-card class="p-5 md:p-6">
                <h3 class="text-sm font-semibold text-ink">Profil calon</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div>
                        <dt class="text-xs text-muted-foreground">Email</dt>
                        <dd class="mt-0.5 break-all">{{ $ca->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">NIK</dt>
                        <dd class="mt-0.5">{{ $ca->national_id_number ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Universitas</dt>
                        <dd class="mt-0.5">{{ $ca->university ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Angkatan UPA</dt>
                        <dd class="mt-0.5">{{ $ca->bar_exam_cohort ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted-foreground">Keanggotaan DPC</dt>
                        <dd class="mt-0.5">{{ $ca->membership_status }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card class="p-5 md:p-6 flex flex-col gap-3">
                <h3 class="text-sm font-semibold text-ink">Keputusan admisi</h3>
                @if ($queueStatus === 'belum_lengkap')
                    <x-tag variant="bad">Belum lengkap</x-tag>
                    <p class="text-xs text-muted-foreground leading-relaxed">Semua link berkas harus ada sebelum verifikasi penuh.</p>
                @elseif ($queueStatus === 'siap')
                    <x-tag variant="wait">Siap disetujui</x-tag>
                    <p class="text-xs text-muted-foreground leading-relaxed">Semua berkas sudah disetujui. Anda dapat menyelesaikan verifikasi admisi.</p>
                @else
                    <x-tag variant="info">Menunggu review</x-tag>
                    <p class="text-xs text-muted-foreground leading-relaxed">Tinjau dan setujui tiap berkas di checklist.</p>
                @endif
                <form method="POST" action="{{ route('admin.verification.candidate.setujui', $ca) }}">
                    @csrf
                    <x-btn class="w-full justify-center" :disabled="$queueStatus !== 'siap'">Setujui admisi</x-btn>
                </form>
            </x-card>
        </div>
    </div>
</x-layout>
