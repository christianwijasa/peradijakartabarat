<x-layout
    crumb="Calon Advokat"
    title="Verifikasi admisi"
    subtitle="Kirim tautan berkas kelulusan UPA dan identitas (Google Drive, OneDrive, dll.) untuk diverifikasi Admin DPC."
    :menu="\App\Support\SidebarMenu::candidate('verification')"
    :user-meta="$ca->candidate_code.' · Verifikasi '.strtoupper($ca->verification_status)"
>
    @if (session('status'))
        <div class="app-alert-success mb-5" role="status">{{ session('status') }}</div>
    @endif

    <x-card class="overflow-hidden">
        <div class="p-5 md:p-6 border-b border-line">
            <h2 class="app-section-title">Checklist berkas verifikasi</h2>
            <p class="text-sm text-muted-foreground mt-1 leading-relaxed">
                Tempel link publik ke PDF atau gambar berkas (pastikan Admin DPC bisa membuka tanpa login khusus).
            </p>
        </div>

        <div class="divide-y divide-line">
            @foreach (\App\Support\CandidateVerificationChecklist::defaultItems() as $def)
                @php
                    $item = $items->firstWhere('label', $def['label']);
                    if (! $item) {
                        continue;
                    }
                    $needsLink = $def['requires_upload'];
                    $hasLink = $item->hasDocumentReference();
                @endphp
                <div class="px-5 md:px-6 py-5 flex flex-col lg:flex-row lg:items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-2.5 text-sm leading-snug">
                            <span @class([
                                $item->is_checked ? 'app-check-ok' : ($hasLink ? 'app-check-wait' : 'app-check-bad'),
                                'mt-1.5 shrink-0',
                            ])></span>
                            <div>
                                <p class="font-medium">{{ $item->label }}</p>
                                @if ($hasLink)
                                    <p class="text-xs text-muted-foreground mt-1 break-all">
                                        @if ($item->document_url)
                                            <a href="{{ $item->document_url }}" target="_blank" rel="noopener noreferrer" class="text-primary underline underline-offset-2">{{ $item->document_url }}</a>
                                        @else
                                            Berkas lokal (legacy)
                                        @endif
                                        @if ($item->is_checked)
                                            · <span class="text-tag-ok-fg">Disetujui Admin</span>
                                        @else
                                            · <span class="text-tag-wait-fg">Menunggu review Admin</span>
                                        @endif
                                    </p>
                                @elseif ($needsLink)
                                    <p class="text-xs text-muted-foreground mt-1">Belum ada link berkas.</p>
                                @else
                                    <p class="text-xs text-muted-foreground mt-1">
                                        {{ $item->is_checked ? 'Terpenuhi' : 'Menunggu validasi Admin DPC' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($needsLink)
                        <form
                            method="POST"
                            action="{{ route('candidate.verification.link', $item) }}"
                            class="lg:w-80 shrink-0 flex flex-col gap-2"
                        >
                            @csrf
                            <x-text-input
                                type="url"
                                name="document_url"
                                class="w-full"
                                :value="old('document_url', $item->document_url)"
                                placeholder="https://..."
                                required
                                autocomplete="off"
                            />
                            <x-input-error :messages="$errors->get('document_url')" class="mt-1" />
                            <x-btn type="submit" class="w-full justify-center">
                                {{ $hasLink ? 'Perbarui link' : 'Simpan link' }}
                            </x-btn>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </x-card>

    @if ($ca->verification_status === 'VERIFIED')
        <p class="mt-4 text-sm text-tag-ok-fg">Verifikasi admisi selesai. Anda dapat melamar lowongan magang.</p>
    @elseif ($ca->verification_status === 'NEEDS_CORRECTION')
        <p class="mt-4 text-sm text-tag-bad-fg">Ada berkas yang perlu diperbaiki. Perbarui link sesuai catatan Admin DPC.</p>
    @endif
</x-layout>
