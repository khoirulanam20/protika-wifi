@extends('layouts.app')

@section('title', 'Profil Akun')
@section('page-title', 'Profil Akun')
@section('page-subtitle', 'Kelola informasi akun dan keamanan Anda')

@section('content')

    <div class="max-w-4xl mx-auto space-y-6 sm:space-y-8">

        {{-- Informasi Akun --}}
        <div class="bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
            <div class="px-6 py-5 border-b border-border">
                <h2 class="text-content-primary font-semibold text-lg">Informasi Akun</h2>
                <p class="text-content-secondary text-sm mt-0.5">Perbarui informasi profil dan alamat email akun Anda.</p>
            </div>

            @if(session('status') === 'profile-updated')
                <div
                    class="mx-6 mt-5 p-4 rounded-xl bg-status-success/10 border border-status-success/20 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-status-success/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-status-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-status-success font-semibold">Berhasil Disimpan</p>
                        <p class="text-xs text-status-success/80 mt-0.5">Informasi profil Anda telah berhasil diperbarui.</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" class="p-6">
                @csrf @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-content-secondary mb-1.5">
                            Nama Lengkap <span class="text-status-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                            class="input-field @error('name') border-status-danger @enderror" required autocomplete="name">
                        @error('name')
                            <p class="mt-1 text-xs text-status-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-content-secondary mb-1.5">
                            Alamat Email <span class="text-status-danger">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                            class="input-field @error('email') border-status-danger @enderror" required
                            autocomplete="username">
                        @error('email')
                            <p class="mt-1 text-xs text-status-danger">{{ $message }}</p>
                        @enderror
                        @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                            <div
                                class="mt-2 flex items-center gap-2 p-2 rounded-md bg-status-warning/10 border border-status-warning/20">
                                <svg class="w-4 h-4 text-status-warning flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-xs text-status-warning font-medium">Email belum terverifikasi.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-4 border-t border-border pt-6">
                    <button type="submit" class="btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- Ganti Password --}}
        <div class="bg-white rounded-2xl shadow-sm border border-border overflow-hidden">
            <div class="px-6 py-5 border-b border-border">
                <h2 class="text-content-primary font-semibold text-lg">Keamanan Kata Sandi</h2>
                <p class="text-content-secondary text-sm mt-0.5">Pastikan akun Anda menggunakan kata sandi yang panjang dan
                    acak agar tetap aman.</p>
            </div>

            @if(session('status') === 'password-updated')
                <div
                    class="mx-6 mt-5 p-4 rounded-xl bg-status-success/10 border border-status-success/20 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-status-success/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-status-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-status-success font-semibold">Kata Sandi Diperbarui</p>
                        <p class="text-xs text-status-success/80 mt-0.5">Keamanan akun Anda telah berhasil ditingkatkan.</p>
                    </div>
                </div>
            @endif

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2">
                    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                        @csrf @method('PUT')

                        <div x-data="{ show: false }">
                            <label for="current_password" class="block text-sm font-medium text-content-secondary mb-1.5">
                                Kata Sandi Saat Ini <span class="text-status-danger">*</span>
                            </label>
                            <div class="relative w-full">
                                <input :type="show ? 'text' : 'password'" id="current_password" name="current_password"
                                    class="input-field w-full pr-12 @error('current_password', 'updatePassword') border-status-danger @enderror"
                                    autocomplete="current-password">
                                <button type="button" @click="show = !show" style="right: 12px;"
                                    class="absolute top-1/2 -translate-y-1/2 p-2 flex items-center justify-center text-content-tertiary hover:text-content-secondary focus:outline-none transition-colors z-10">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('current_password', 'updatePassword')
                                <p class="mt-1 text-xs text-status-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm font-medium text-content-secondary mb-1.5">
                                Kata Sandi Baru <span class="text-status-danger">*</span>
                            </label>
                            <div class="relative w-full">
                                <input :type="show ? 'text' : 'password'" id="password" name="password"
                                    class="input-field w-full pr-12 @error('password', 'updatePassword') border-status-danger @enderror"
                                    autocomplete="new-password">
                                <button type="button" @click="show = !show" style="right: 12px;"
                                    class="absolute top-1/2 -translate-y-1/2 p-2 flex items-center justify-center text-content-tertiary hover:text-content-secondary focus:outline-none transition-colors z-10">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password', 'updatePassword')
                                <p class="mt-1 text-xs text-status-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-content-secondary mb-1.5">
                                Konfirmasi Kata Sandi Baru <span class="text-status-danger">*</span>
                            </label>
                            <div class="relative w-full">
                                <input :type="show ? 'text' : 'password'" id="password_confirmation"
                                    name="password_confirmation" class="input-field w-full pr-12"
                                    autocomplete="new-password">
                                <button type="button" @click="show = !show" style="right: 12px;"
                                    class="absolute top-1/2 -translate-y-1/2 p-2 flex items-center justify-center text-content-tertiary hover:text-content-secondary focus:outline-none transition-colors z-10">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end border-t border-border pt-6">
                            <button type="submit" class="btn-primary">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Zona Berbahaya --}}
        @role('superadmin')
        <div class="bg-white rounded-2xl shadow-sm border border-status-danger/30 overflow-hidden relative">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-status-danger/5 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none">
            </div>

            <div class="px-6 py-5 border-b border-status-danger/20 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-status-danger/10 text-status-danger flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-status-danger font-semibold text-lg">Hapus Akun</h2>
                        <p class="text-content-secondary text-sm mt-0.5">Tindakan ini permanen dan tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 relative z-10" x-data="{ confirmDelete: false }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="flex-1">
                        <p class="text-sm text-content-secondary leading-relaxed">
                            Setelah akun dihapus, seluruh data dan hak akses Anda akan dihapus secara permanen dari sistem.
                            Pastikan Anda telah mendelegasikan tugas admin sebelum melanjutkan.
                        </p>
                    </div>
                    <button @click="confirmDelete = true"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-status-danger hover:bg-red-600 text-white rounded-md font-semibold transition-all shadow-sm active:scale-95 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Akun Saya
                    </button>
                </div>

                {{-- Confirm Delete Modal --}}
                <div x-show="confirmDelete" x-cloak
                    class="fixed inset-0 z-50 overflow-y-auto px-4 pt-4 pb-24 flex items-center justify-center">
                    <div x-show="confirmDelete" x-transition.opacity
                        class="fixed inset-0 bg-content-primary/60 backdrop-blur-sm" @click="confirmDelete = false"></div>

                    <div x-show="confirmDelete" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="relative bg-white rounded-2xl shadow-modal w-full max-w-md mx-auto z-10 overflow-hidden">

                        <div class="px-6 py-5 border-b border-border bg-base-page flex justify-between items-center">
                            <h4 class="text-lg font-bold text-content-primary">Konfirmasi Tindakan</h4>
                            <button @click="confirmDelete = false"
                                class="text-content-tertiary hover:text-content-primary transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6">
                            <p class="text-sm text-content-secondary mb-6">Masukkan password Anda untuk mengkonfirmasi
                                penghapusan akun secara permanen.</p>

                            <form method="POST" action="{{ route('profile.destroy') }}">
                                @csrf @method('DELETE')
                                <div class="mb-6">
                                    <label for="del_password"
                                        class="block text-sm font-medium text-content-secondary mb-1.5">Password</label>
                                    <input type="password" id="del_password" name="password"
                                        class="input-field @error('password', 'userDeletion') border-status-danger @enderror"
                                        placeholder="Masukkan password Anda">
                                    @error('password', 'userDeletion')
                                        <p class="mt-1 text-xs text-status-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex gap-3 justify-end">
                                    <button type="button" @click="confirmDelete = false"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-border hover:bg-base-input text-content-primary rounded-md font-semibold transition-all shadow-sm active:scale-95">Batal</button>
                                    <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-status-danger hover:bg-red-600 text-white rounded-md font-semibold transition-all shadow-sm active:scale-95">
                                        Ya, Hapus Akun
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole

    </div>

@endsection