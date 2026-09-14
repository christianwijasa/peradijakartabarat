<x-guest-layout>
    <h1 class="font-serif text-xl text-navy mb-1">Verifikasi email</h1>
    <p class="text-sm text-muted-foreground mb-6 leading-relaxed">
        Sebelum mulai, verifikasi alamat email dengan tautan yang kami kirim. Tidak menerima email? Anda bisa minta kirim ulang.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="app-alert-success mb-4" role="status">
            Tautan verifikasi baru telah dikirim ke email yang Anda daftarkan.
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center">
                Kirim ulang email verifikasi
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full min-h-[44px] text-sm font-medium text-muted-foreground hover:text-ink underline underline-offset-2">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
