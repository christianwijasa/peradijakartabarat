<x-layout
    crumb="Calon Advokat"
    title="Verifikasi admisi"
    subtitle="Unggah berkas kelulusan UPA dan identitas untuk diverifikasi Admin DPC sebelum melamar magang."
    :menu="\App\Support\SidebarMenu::calon('verifikasi')"
    :user-meta="$ca->candidate_code.' · Verifikasi '.strtoupper($ca->verification_status)"
>
    @if (session('status'))
        <div class="app-alert-success mb-5" role="status">{{ session('status') }}</div>
    @endif

    <x-card class="overflow-hidden">
        <div class="p-5 md:p-6 border-b border-line">
            <h2 class="app-section-title">Checklist berkas verifikasi</h2>
            <p class="text-sm text-muted-foreground mt-1 leading-relaxed">
                Setiap poin di bawah akan ditinjau Admin DPC. Unggah PDF atau gambar (maks. 5 MB per berkas).
            </p>
        </div>

        <div class="divide-y divide-line">
            @foreach (\App\Support\CandidateVerificationChecklist::defaultItems() as $def)
                @php
                    $item = $items->firstWhere('label', $def['label']);
                    if (! $item) {
                        continue;
                    }
                    $needsUpload = $def['requires_upload'];
                    $uploaded = filled($item->file_path);
                @endphp
                <div class="px-5 md:px-6 py-5 flex flex-col lg:flex-row lg:items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-2.5 text-sm leading-snug">
                            <span @class([
                                $item->is_checked ? 'app-check-ok' : ($uploaded ? 'app-check-wait' : 'app-check-bad'),
                                'mt-1.5 shrink-0',
                            ])></span>
                            <div>
                                <p class="font-medium">{{ $item->label }}</p>
                                @if ($uploaded)
                                    <p class="text-xs text-muted-foreground mt-1">
                                        Diunggah · {{ $item->file_size_label ?? '—' }}
                                        @if ($item->is_checked)
                                            · <span class="text-tag-ok-fg">Disetujui Admin</span>
                                        @else
                                            · <span class="text-tag-wait-fg">Menunggu review Admin</span>
                                        @endif
                                    </p>
                                @elseif ($needsUpload)
                                    <p class="text-xs text-muted-foreground mt-1">Belum ada berkas diunggah.</p>
                                @else
                                    <p class="text-xs text-muted-foreground mt-1">
                                        {{ $item->is_checked ? 'Terpenuhi' : 'Menunggu validasi Admin DPC' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($needsUpload)
                        <form
                            method="POST"
                            action="{{ route('calon.verifikasi.upload', $item) }}"
                            enctype="multipart/form-data"
                            class="lg:w-80 shrink-0 flex flex-col sm:flex-row lg:flex-col gap-2"
                        >
                            @csrf
                            <input
                                type="file"
                                name="document"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required
                                class="block w-full text-sm text-ink-secondary file:mr-3 file:rounded-lg file:border-0 file:bg-muted file:px-3 file:py-2 file:text-sm file:font-medium file:text-ink hover:file:bg-line/80"
                            />
                            <x-btn type="submit" class="w-full sm:w-auto justify-center">
                                {{ $uploaded ? 'Unggah ulang' : 'Unggah' }}
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
        <p class="mt-4 text-sm text-tag-bad-fg">Ada berkas yang perlu diperbaiki. Unggah ulang sesuai catatan Admin DPC.</p>
    @endif
</x-layout>
