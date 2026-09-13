<x-layout
    crumb="Law Firm · {{ $firm->nama }}"
    title="Buat lowongan baru"
    subtitle="Publikasikan lowongan magang untuk calon advokat yang telah terverifikasi."
    :menu="\App\Support\SidebarMenu::firm('lowongan')"
    :user-meta="'Advokat Pendamping · '.$firm->nama"
>
    <x-card class="p-6 max-w-3xl">
        <form method="POST" action="{{ route('firm.lowongan.store') }}" class="flex flex-col gap-5">
            @csrf

            <div>
                <label for="judul" class="block text-sm font-medium mb-1.5">Judul lowongan</label>
                <input
                    type="text"
                    id="judul"
                    name="judul"
                    value="{{ old('judul') }}"
                    required
                    class="w-full rounded-lg border border-[#e0e2e9] px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Contoh: Magang Calon Advokat — Litigasi Perdata"
                >
                @error('judul')
                    <p class="text-xs text-[#93321f] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium mb-1.5">Deskripsi</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="4"
                    required
                    class="w-full rounded-lg border border-[#e0e2e9] px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Jelaskan bidang kerja, ekspektasi, dan benefit yang akan didapat..."
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-[#93321f] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Bidang magang</label>
                <div class="flex flex-wrap gap-2" x-data="{ bidang: @json(old('bidang', [])) }">
                    @foreach (['Litigasi', 'Perdata', 'Pidana', 'Korporasi', 'Keluarga', 'Kepailitan', 'Full-time', 'Hybrid', 'Prodeo'] as $b)
                        <label class="inline-flex items-center gap-2 px-3.5 py-2 border rounded-lg cursor-pointer transition"
                               :class="bidang.includes('{{ $b }}') ? 'border-primary bg-[#e9eef6] text-primary' : 'border-[#e0e2e9] hover:bg-[#f7f8fa]'">
                            <input
                                type="checkbox"
                                name="bidang[]"
                                value="{{ $b }}"
                                class="sr-only"
                                x-model="bidang"
                            >
                            <span class="text-sm">{{ $b }}</span>
                        </label>
                    @endforeach
                </div>
                @error('bidang')
                    <p class="text-xs text-[#93321f] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kuota" class="block text-sm font-medium mb-1.5">Kuota peserta</label>
                <input
                    type="number"
                    id="kuota"
                    name="kuota"
                    value="{{ old('kuota', 1) }}"
                    min="1"
                    max="50"
                    required
                    class="w-full rounded-lg border border-[#e0e2e9] px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-transparent"
                >
                @error('kuota')
                    <p class="text-xs text-[#93321f] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2.5 pt-2">
                <x-btn type="submit">Publikasikan lowongan</x-btn>
                <x-btn as="a" href="{{ route('firm.lowongan.index') }}" variant="ghost">Batal</x-btn>
            </div>
        </form>
    </x-card>
</x-layout>
