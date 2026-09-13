<x-guest-layout>
    <h1 class="font-serif text-xl text-[#0d2a5c] mb-1">Registrasi Kantor Hukum</h1>
    <p class="text-sm text-[#7a7d8b] mb-5">Daftarkan kantor hukum Anda sebagai mitra bimbingan magang calon advokat.</p>

    <form method="POST" action="{{ route('register.firm.store') }}">
        @csrf

        <div class="border-b border-[#e0e2e9] pb-4 mb-4">
            <p class="text-xs font-medium text-[#7a7d8b] uppercase tracking-wider mb-3">Data Kantor Hukum</p>
            
            <div>
                <x-input-label for="nama_firm" value="Nama Kantor Hukum" />
                <x-text-input id="nama_firm" class="block mt-1 w-full" type="text" name="nama_firm" :value="old('nama_firm')" required autofocus placeholder="Contoh: Wibisono & Rekan" />
                <x-input-error :messages="$errors->get('nama_firm')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="alamat" value="Alamat lengkap" />
                <textarea id="alamat" name="alamat" rows="2" required class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('alamat') }}</textarea>
                <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="sk_kemenkumham" value="SK Kemenkumham (opsional jika LBH setara)" />
                <x-text-input id="sk_kemenkumham" class="block mt-1 w-full" type="text" name="sk_kemenkumham" :value="old('sk_kemenkumham')" placeholder="Contoh: AHU-0041223.AH.01.01" />
                <x-input-error :messages="$errors->get('sk_kemenkumham')" class="mt-2" />
            </div>

            <div class="mt-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="setara_kantor_advokat" value="1" {{ old('setara_kantor_advokat') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="text-sm text-[#5b5d68]">LBH Kampus / setara kantor advokat (Peraturan PERADI No. 1/2015)</span>
                </label>
            </div>

            <div class="mt-4">
                <x-input-label for="kuota_maks" value="Kuota maksimal bimbingan" />
                <x-text-input id="kuota_maks" class="block mt-1 w-full" type="number" name="kuota_maks" :value="old('kuota_maks', 10)" required min="1" max="50" />
                <x-input-error :messages="$errors->get('kuota_maks')" class="mt-2" />
                <p class="text-xs text-[#7a7d8b] mt-1">Jumlah calon advokat yang dapat dibimbing bersamaan</p>
            </div>
        </div>

        <div class="border-b border-[#e0e2e9] pb-4 mb-4">
            <p class="text-xs font-medium text-[#7a7d8b] uppercase tracking-wider mb-3">Data Advokat Pendamping</p>
            
            <div>
                <x-input-label for="nama_pendamping" value="Nama lengkap (dengan gelar)" />
                <x-text-input id="nama_pendamping" class="block mt-1 w-full" type="text" name="nama_pendamping" :value="old('nama_pendamping')" required placeholder="Contoh: Dr. Hendra Wibisono, S.H., M.H." />
                <x-input-error :messages="$errors->get('nama_pendamping')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="kta_nomor" value="Nomor KTA PERADI" />
                <x-text-input id="kta_nomor" class="block mt-1 w-full" type="text" name="kta_nomor" :value="old('kta_nomor')" required placeholder="Contoh: KTA-DPC-JB-00214" />
                <x-input-error :messages="$errors->get('kta_nomor')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="pengalaman_tahun" value="Pengalaman praktik (tahun)" />
                <x-text-input id="pengalaman_tahun" class="block mt-1 w-full" type="number" name="pengalaman_tahun" :value="old('pengalaman_tahun', 5)" required min="5" max="50" />
                <x-input-error :messages="$errors->get('pengalaman_tahun')" class="mt-2" />
            </div>
        </div>

        <div class="border-b border-[#e0e2e9] pb-4 mb-4">
            <p class="text-xs font-medium text-[#7a7d8b] uppercase tracking-wider mb-3">Akun Login</p>
            
            <div>
                <x-input-label for="name" value="Nama pengguna" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" placeholder="Nama untuk login" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
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
        </div>

        <div class="flex items-center justify-between mt-5">
            <a class="text-sm text-[#5b5d68] hover:text-[#191a20] underline underline-offset-2" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <x-primary-button>
                Daftar Kantor Hukum
            </x-primary-button>
        </div>

        <p class="text-xs text-[#7a7d8b] mt-4 text-center">
            Pendaftaran calon advokat? <a href="{{ route('register') }}" class="text-primary hover:underline">Klik di sini</a>
        </p>
    </form>
</x-guest-layout>
