<x-guest-layout>
    {{-- Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-extrabold text-content-primary tracking-tight">Selamat Datang</h1>
        <p class="text-sm text-content-secondary mt-1.5">Masuk ke akun Protika WiFi Anda</p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-5">
            {{-- Email --}}
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan email Anda" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div x-data="{ show: false }">
                <x-input-label for="password" :value="__('Password')" />
                <div class="relative mt-1.5">
                    <x-text-input id="password" class="block w-full pr-11" x-bind:type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Masukkan password" />
                    <button type="button" @click="show = !show" class="absolute top-1/2 -translate-y-1/2 right-3 text-content-tertiary hover:text-content-primary transition-colors focus:outline-none">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="w-4 h-4 rounded border-border text-primary focus:ring-primary/30 focus:ring-offset-0 cursor-pointer transition-shadow">
                    <span class="text-sm text-content-secondary group-hover:text-content-primary transition-colors">{{ __('Ingat Saya') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">
                        Lupa Password?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary w-full justify-center py-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Masuk
            </button>
        </div>
    </form>
</x-guest-layout>
