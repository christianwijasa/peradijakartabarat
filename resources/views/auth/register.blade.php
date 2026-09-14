<x-guest-layout>
    <h1 class="font-serif text-xl text-[#0d2a5c] mb-1">Registrasi Calon Advokat</h1>
    <p class="text-sm text-[#7a7d8b] mb-5">Langkah pertama alur magang: registrasi akun dan data alumni lulus UPA.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nama lengkap (dengan gelar)" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Contoh: Citra Lestari, S.H." />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="nik" value="NIK" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="national_id_number" :value="old('national_id_number')" autocomplete="off" />
            <x-input-error :messages="$errors->get('national_id_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="universitas" value="Universitas" />
            <x-text-input id="universitas" class="block mt-1 w-full" type="text" name="university" :value="old('university')" autocomplete="off" />
            <x-input-error :messages="$errors->get('university')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-5">
            <a class="text-sm text-[#5b5d68] hover:text-[#191a20] underline underline-offset-2" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <x-primary-button>
                Daftar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
