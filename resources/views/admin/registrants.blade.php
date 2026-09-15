@php
    $verificationVariant = ['VERIFIED' => 'ok', 'PENDING' => 'wait', 'NEEDS_CORRECTION' => 'bad'];
    $verificationLabel = [
        'VERIFIED' => 'Terverifikasi',
        'PENDING' => 'Menunggu',
        'NEEDS_CORRECTION' => 'Perlu perbaikan',
    ];
@endphp
<x-layout
    crumb="Admin DPC Jakarta Barat"
    title="Data terdaftar"
    subtitle="Seluruh calon advokat dan kantor hukum yang sudah registrasi di platform."
    :menu="\App\Support\SidebarMenu::admin('registrants')"
    user-meta="Admin Bidang Magang"
>
    <div class="grid grid-cols-2 gap-3 md:gap-4">
        @foreach ($stats as $s)
            <x-stat-tile :value="$s['value']" :label="$s['label']" />
        @endforeach
    </div>

    <x-card class="overflow-hidden">
        <div class="p-4 md:p-5 border-b border-line flex flex-col gap-4">
            <nav class="app-tab-bar" aria-label="Tab data terdaftar">
                <a href="{{ route('admin.registrants', array_filter(['tab' => 'candidate', 'q' => $keyword ?: null])) }}"
                   @class([$tab === 'candidate' ? 'app-tab-active' : 'app-tab-idle'])>
                    Calon advokat
                </a>
                <a href="{{ route('admin.registrants', array_filter(['tab' => 'firm', 'q' => $keyword ?: null])) }}"
                   @class([$tab === 'firm' ? 'app-tab-active' : 'app-tab-idle'])>
                    Kantor hukum
                </a>
            </nav>
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input
                    type="search"
                    name="q"
                    value="{{ $keyword }}"
                    placeholder="{{ $tab === 'candidate' ? 'Cari nama, email, atau kode calon' : 'Cari nama kantor atau SK Kemenkumham' }}"
                    class="app-input flex-1"
                >
                <x-btn type="submit" variant="ghost" class="shrink-0 justify-center">Cari</x-btn>
            </form>
        </div>

        @if ($tab === 'candidate')
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="bg-muted/60 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Calon advokat</th>
                            <th class="px-5 py-3 font-semibold">Kontak</th>
                            <th class="px-5 py-3 font-semibold">Pendidikan</th>
                            <th class="px-5 py-3 font-semibold">Verifikasi</th>
                            <th class="px-5 py-3 font-semibold">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($candidates as $ca)
                            <tr class="align-top">
                                <td class="px-5 py-4">
                                    <p class="font-medium">{{ $ca->user->name }}</p>
                                    <p class="text-xs text-muted-foreground mt-0.5">{{ $ca->candidate_code }}</p>
                                </td>
                                <td class="px-5 py-4 text-muted-foreground">
                                    <p>{{ $ca->user->email }}</p>
                                    @if ($ca->national_id_number)
                                        <p class="text-xs mt-1">NIK {{ substr($ca->national_id_number, 0, 4) }}••••</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-muted-foreground">
                                    <p>{{ $ca->university ?? '—' }}</p>
                                    @if ($ca->bar_exam_cohort)
                                        <p class="text-xs mt-1">UPA {{ $ca->bar_exam_cohort }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <x-tag :variant="$verificationVariant[$ca->verification_status] ?? 'mute'">
                                        {{ $verificationLabel[$ca->verification_status] ?? $ca->verification_status }}
                                    </x-tag>
                                    <p class="text-xs text-muted-foreground mt-1">Keanggotaan {{ strtolower($ca->membership_status) }}</p>
                                </td>
                                <td class="px-5 py-4 text-muted-foreground whitespace-nowrap">
                                    {{ $ca->created_at->translatedFormat('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-muted-foreground">
                                    {{ $keyword ? 'Tidak ada calon advokat yang cocok.' : 'Belum ada calon advokat terdaftar.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm">
                    <thead class="bg-muted/60 text-left text-xs uppercase tracking-wide text-muted-foreground">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Kantor hukum</th>
                            <th class="px-5 py-3 font-semibold">Advokat pendamping</th>
                            <th class="px-5 py-3 font-semibold">Kuota</th>
                            <th class="px-5 py-3 font-semibold">Verifikasi</th>
                            <th class="px-5 py-3 font-semibold">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($firms as $firm)
                            @php $pendamping = $firm->supervisingLawyers->first(); @endphp
                            <tr class="align-top">
                                <td class="px-5 py-4">
                                    <p class="font-medium">{{ $firm->name }}</p>
                                    <p class="text-xs text-muted-foreground mt-0.5">{{ $firm->ministry_registration_number ?? 'SK Kemenkumham —' }}</p>
                                    @if ($firm->address)
                                        <p class="text-xs text-muted-foreground mt-1 max-w-xs">{{ $firm->address }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-muted-foreground">
                                    @if ($pendamping)
                                        <p>{{ $pendamping->name }}</p>
                                        <p class="text-xs mt-1">{{ $pendamping->user?->email ?? '—' }}</p>
                                        @if ($pendamping->bar_membership_number)
                                            <p class="text-xs mt-1">KTA {{ $pendamping->bar_membership_number }}</p>
                                        @endif
                                    @else
                                        <p>—</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-muted-foreground whitespace-nowrap">
                                    Maks. {{ $firm->max_quota }} · sisa {{ $firm->kuotaTersisa() }}
                                </td>
                                <td class="px-5 py-4">
                                    <x-tag :variant="$verificationVariant[$firm->verification_status] ?? 'mute'">
                                        {{ $verificationLabel[$firm->verification_status] ?? $firm->verification_status }}
                                    </x-tag>
                                </td>
                                <td class="px-5 py-4 text-muted-foreground whitespace-nowrap">
                                    {{ $firm->created_at->translatedFormat('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-muted-foreground">
                                    {{ $keyword ? 'Tidak ada kantor hukum yang cocok.' : 'Belum ada kantor hukum terdaftar.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-layout>
