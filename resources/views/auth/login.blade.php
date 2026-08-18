<x-guest-layout>
    <h1 class="font-serif text-xl text-[#0d2a5c] mb-1">Masuk</h1>
    <p class="text-sm text-[#7a7d8b] mb-5">Masuk sesuai peran: calon advokat, law firm, atau admin DPC.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary" name="remember">
                <span class="ms-2 text-sm text-[#5b5d68]">Ingat saya</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-5">
            <a class="text-sm text-[#5b5d68] hover:text-[#191a20] underline underline-offset-2" href="{{ route('register') }}">
                Belum punya akun?
            </a>

            <x-primary-button>
                Masuk
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
