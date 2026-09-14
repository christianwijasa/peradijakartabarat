<x-guest-layout>
    <h1 class="font-serif text-xl text-navy mb-1">Lupa kata sandi</h1>
    <p class="text-sm text-muted-foreground mb-6 leading-relaxed">
        Masukkan email akun Anda. Kami akan mengirim tautan reset kata sandi ke email tersebut.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <a href="{{ route('login') }}" class="text-sm text-center sm:text-left text-muted-foreground hover:text-ink underline underline-offset-2">
                Kembali ke masuk
            </a>
            <x-primary-button class="w-full sm:w-auto justify-center">
                Kirim tautan reset
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
