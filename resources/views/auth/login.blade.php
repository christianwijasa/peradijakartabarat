<x-guest-layout>
    <h1 class="font-serif text-xl text-navy mb-1">Masuk</h1>
    <p class="text-sm text-muted-foreground mb-6 leading-relaxed">Masuk sesuai peran: calon advokat, law firm, atau admin DPC.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="remember_me" class="inline-flex min-h-[44px] items-center gap-2 sm:min-h-0">
                <input id="remember_me" type="checkbox" class="rounded-md border-line text-primary shadow-sm focus:ring-primary/30" name="remember">
                <span class="text-sm text-ink-secondary">Ingat saya</span>
            </label>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
            <div class="flex flex-col gap-1 text-sm text-center sm:text-left">
                <a class="text-muted-foreground hover:text-ink underline underline-offset-2" href="{{ route('register.advocate-candidate') }}">Register as advocate candidate</a>
                <a class="text-muted-foreground hover:text-ink underline underline-offset-2" href="{{ route('register.law-firm') }}">Daftar law firm</a>
            </div>

            <x-primary-button class="w-full sm:w-auto justify-center">
                Masuk
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
