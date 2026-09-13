<x-layout
    crumb="Law Firm · Pendamping"
    title="Review logbook pemagang"
    subtitle="Setujui entri harian dan tanda tangani rekap bulanan secara digital."
    :menu="\App\Support\SidebarMenu::firm('logbook')"
    :user-meta="'Advokat Pendamping · '.$pendamping->lawFirm->nama"
>
    @if ($calonList->count() > 1)
        <form method="GET" class="flex items-center gap-2">
            <label class="text-xs text-[#7a7d8b]">Pemagang</label>
            <select name="calon" onchange="this.form.submit()" class="rounded-lg border-[#e0e2e9] text-sm focus:border-primary focus:ring-primary">
                @foreach ($calonList as $ca)
                    <option value="{{ $ca->id }}" @selected($selected && $selected->id === $ca->id)>{{ $ca->user->name }} · {{ $ca->kode_ca }}</option>
                @endforeach
            </select>
        </form>
    @endif

    @if (! $selected)
        <x-card class="p-8 text-center text-sm text-[#7a7d8b]">Belum ada calon advokat yang dibimbing.</x-card>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-5 items-start">
            <x-card class="p-6">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-semibold text-[15px]">Rekap bulanan · {{ $bulanLabel }}</h2>
                    <p class="text-xs text-[#7a7d8b]">{{ $selected->user->name }} · {{ $selected->kode_ca }}</p>
                </div>
                <div class="mt-3 divide-y divide-[#eceef2]">
                    @forelse ($entries->where('status', '!=', 'disetujui') as $e)
                        <div class="py-4 flex items-start gap-4">
                            <div class="w-14 shrink-0 text-xs text-[#7a7d8b] pt-0.5">{{ $e->tanggal->translatedFormat('j M') }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-[#7a7d8b]">{{ $e->jenis_kegiatan }} · {{ rtrim(rtrim(number_format($e->jam, 1), '0'), '.') }} jam</p>
                                <p class="text-sm mt-1">{{ $e->uraian }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0" x-data="{ showSignModal: false, entryId: {{ $e->id }} }">
                                <button @click="showSignModal = true" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-[12.5px] font-medium whitespace-nowrap transition bg-primary text-white border border-transparent hover:bg-navy-light">Setujui</button>
                                <form method="POST" action="{{ route('firm.logbook.revisi', $e) }}" x-data="{ catatan: '' }" @submit.prevent="if (catatan || confirm('Kirim revisi tanpa catatan?')) $el.submit()">
                                    @csrf
                                    <input type="hidden" name="catatan_revisi" x-model="catatan">
                                    <button @click="catatan = prompt('Catatan revisi untuk calon advokat:')" type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-[12.5px] font-medium whitespace-nowrap transition bg-white text-[#93321f] border border-[#e7cfc7] hover:bg-[#f9ebe7]">Revisi</button>
                                </form>

                                <div x-show="showSignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showSignModal = false">
                                    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4" @click.stop>
                                        <h3 class="font-semibold text-lg mb-4">Tanda Tangan Digital</h3>
                                        <form method="POST" :action="`{{ route('firm.logbook.setujui', '') }}/${entryId}`" x-data="{ signType: 'typed', signValue: '' }">
                                            @csrf
                                            <input type="hidden" name="signature_type" x-model="signType">
                                            <input type="hidden" name="signature_value" x-model="signValue">
                                            
                                            <div class="flex gap-2 mb-4">
                                                <button type="button" @click="signType = 'typed'" :class="signType === 'typed' ? 'bg-primary text-white' : 'bg-gray-100'" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium">Ketik Nama</button>
                                                <button type="button" @click="signType = 'drawn'" :class="signType === 'drawn' ? 'bg-primary text-white' : 'bg-gray-100'" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium">Tanda Tangan</button>
                                            </div>

                                            <div x-show="signType === 'typed'" class="mb-4">
                                                <input type="text" x-model="signValue" placeholder="Ketik nama lengkap Anda" class="w-full px-4 py-2 border rounded-lg">
                                            </div>

                                            <div x-show="signType === 'drawn'" class="mb-4">
                                                <canvas x-ref="signCanvas" width="400" height="150" class="border rounded-lg w-full cursor-crosshair" 
                                                    @mousedown="startDrawing($event)" @mousemove="draw($event)" @mouseup="stopDrawing()" @mouseleave="stopDrawing()"
                                                    @touchstart="startDrawing($event)" @touchmove="draw($event)" @touchend="stopDrawing()"></canvas>
                                                <button type="button" @click="clearCanvas()" class="text-xs text-gray-500 mt-2">Hapus</button>
                                            </div>

                                            <div class="flex gap-2">
                                                <button type="submit" class="flex-1 bg-primary text-white px-4 py-2 rounded-lg font-medium">Setujui & Tanda Tangani</button>
                                                <button type="button" @click="showSignModal = false" class="px-4 py-2 border rounded-lg">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-[#7a7d8b]">Semua entri bulan ini sudah disetujui.</p>
                    @endforelse
                </div>

                @if ($entries->where('status', 'disetujui')->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-[#eceef2]">
                        <p class="text-xs text-[#7a7d8b] mb-2">Sudah disetujui & ditandatangani</p>
                        <div class="flex flex-col gap-1.5">
                            @foreach ($entries->where('status', 'disetujui') as $e)
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-xs text-[#7a7d8b] w-14 shrink-0">{{ $e->tanggal->translatedFormat('j M') }}</span>
                                    <span class="flex-1 truncate text-[#5b5d68]">{{ $e->jenis_kegiatan }}</span>
                                    <x-tag variant="ok">✓ TTD</x-tag>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </x-card>

            <x-card class="p-6" x-data="signatureModal()">
                <h2 class="font-semibold text-[15px]">Tanda tangan digital</h2>
                <div class="mt-3 border border-dashed border-[#d8dbe3] rounded-xl px-4 py-6 text-center">
                    <p class="font-serif text-lg text-[#0d2a5c]">{{ $pendamping->nama }}</p>
                    <p class="text-xs text-[#7a7d8b] mt-1">Sertifikat e-sign aktif</p>
                    <p class="text-xs text-[#7a7d8b]">KTA {{ $pendamping->kta_nomor }}</p>
                </div>
                @php $approvedCount = $entries->where('status', 'disetujui')->count(); @endphp
                <p class="text-sm text-[#5b5d68] mt-4">
                    {{ $approvedCount }} dari {{ $entries->count() }} entri disetujui. Rekap bulanan terkirim ke Admin DPC setelah seluruh entri ditandatangani.
                </p>
                <button @click="showSignModal = true" :disabled="isDisabled" class="mt-4 w-full inline-flex items-center justify-center gap-1.5 rounded-lg px-3.5 py-2 text-[12.5px] font-medium whitespace-nowrap transition disabled:opacity-50 disabled:cursor-not-allowed bg-primary text-white border border-transparent hover:bg-navy-light">
                    {{ $rekap->status === 'ditandatangani' ? 'Rekap ditandatangani' : 'Setujui & tanda tangani semua' }}
                </button>

                <div x-show="showSignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showSignModal = false">
                    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4" @click.stop>
                        <h3 class="font-semibold text-lg mb-4">Tanda Tangan Digital - Rekap Bulanan</h3>
                        <form method="POST" action="{{ route('firm.logbook.tandatangani-semua', $rekap) }}">
                            @csrf
                            <input type="hidden" name="signature_type" x-model="signType">
                            <input type="hidden" name="signature_value" x-model="signValue">
                            
                            <div class="flex gap-2 mb-4">
                                <button type="button" @click="signType = 'typed'" :class="signType === 'typed' ? 'bg-primary text-white' : 'bg-gray-100'" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium">Ketik Nama</button>
                                <button type="button" @click="signType = 'drawn'" :class="signType === 'drawn' ? 'bg-primary text-white' : 'bg-gray-100'" class="flex-1 px-4 py-2 rounded-lg text-sm font-medium">Tanda Tangan</button>
                            </div>

                            <div x-show="signType === 'typed'" class="mb-4">
                                <input type="text" x-model="signValue" placeholder="Ketik nama lengkap Anda" class="w-full px-4 py-2 border rounded-lg">
                            </div>

                            <div x-show="signType === 'drawn'" class="mb-4">
                                <canvas x-ref="signCanvas" width="400" height="150" class="border rounded-lg w-full cursor-crosshair" 
                                    @mousedown="startDrawing($event)" @mousemove="draw($event)" @mouseup="stopDrawing()" @mouseleave="stopDrawing()"
                                    @touchstart.prevent="startDrawing($event)" @touchmove.prevent="draw($event)" @touchend="stopDrawing()"></canvas>
                                <button type="button" @click="clearCanvas()" class="text-xs text-gray-500 mt-2">Hapus</button>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-primary text-white px-4 py-2 rounded-lg font-medium">Setujui & Tanda Tangani</button>
                                <button type="button" @click="showSignModal = false" class="px-4 py-2 border rounded-lg">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function signatureModal() {
                        return {
                            showSignModal: false,
                            signType: 'typed',
                            signValue: '',
                            isDrawing: false,
                            isDisabled: {{ $entries->isEmpty() || $rekap->status === 'ditandatangani' ? 'true' : 'false' }},
                            
                            startDrawing(e) {
                                this.isDrawing = true;
                                const canvas = this.$refs.signCanvas;
                                const ctx = canvas.getContext('2d');
                                const rect = canvas.getBoundingClientRect();
                                const x = (e.clientX || e.touches[0].clientX) - rect.left;
                                const y = (e.clientY || e.touches[0].clientY) - rect.top;
                                ctx.beginPath();
                                ctx.moveTo(x, y);
                            },
                            
                            draw(e) {
                                if (!this.isDrawing) return;
                                const canvas = this.$refs.signCanvas;
                                const ctx = canvas.getContext('2d');
                                const rect = canvas.getBoundingClientRect();
                                const x = (e.clientX || e.touches[0].clientX) - rect.left;
                                const y = (e.clientY || e.touches[0].clientY) - rect.top;
                                ctx.lineTo(x, y);
                                ctx.strokeStyle = '#191a20';
                                ctx.lineWidth = 2;
                                ctx.lineCap = 'round';
                                ctx.stroke();
                                this.signValue = canvas.toDataURL();
                            },
                            
                            stopDrawing() {
                                this.isDrawing = false;
                            },
                            
                            clearCanvas() {
                                const canvas = this.$refs.signCanvas;
                                const ctx = canvas.getContext('2d');
                                ctx.clearRect(0, 0, canvas.width, canvas.height);
                                this.signValue = '';
                            }
                        }
                    }
                </script>
            </x-card>
        </div>
    @endif
</x-layout>
