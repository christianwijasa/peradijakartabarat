<x-guest-layout>
    <h1 class="font-serif text-xl text-navy mb-1">Konfirmasi kata sandi</h1>
    <p class="text-sm text-muted-foreground mb-6 leading-relaxed">
        Area ini memerlukan verifikasi. Masukkan kata sandi Anda untuk melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center">
                Lanjutkan
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
