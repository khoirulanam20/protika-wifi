<header class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-border">
    <div class="max-w-[1440px] mx-auto px-8 h-[60px] flex items-center justify-between">

        <div class="flex items-center gap-6">
            {{-- Logo Pill --}}
            <div class="flex items-center gap-2 px-4 py-1.5 rounded-full border border-border bg-white shadow-sm">
                <div
                    class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-content-primary font-bold text-xs">
                    P</div>
                <span class="font-semibold text-content-primary text-sm">Protika</span>
            </div>

            {{-- Primary Navigation --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'nav-active' : 'nav-item' }}">
                    Dashboard
                </a>

                @hasanyrole('superadmin|kolektor')
                <div class="relative" @click.away="masterMenuOpen = false">
                    <button @click="masterMenuOpen = !masterMenuOpen"
                        class="{{ request()->is('master/*') ? 'nav-active' : 'nav-item' }} inline-flex items-center gap-1">
                        Master Data
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="masterMenuOpen" x-transition.opacity.duration.200ms
                        class="absolute top-full left-0 mt-2 w-48 bg-white border border-border rounded-lg shadow-modal py-2 z-50"
                        style="display:none;">
                        <a href="{{ route('master.pelanggan.index') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Pelanggan</a>
                        <a href="{{ route('master.dusun.index') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Wilayah</a>
                        <a href="{{ route('master.bulanan.index') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Paket
                            Bulanan</a>
                        @role('superadmin')
                        <a href="{{ route('master.kolektor.index') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Kolektor</a>
                        @endrole
                        <a href="{{ route('master.teknisi.index') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Teknisi</a>
                        <a href="{{ route('master.penagih.index') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Penagih</a>
                        @role('superadmin')
                        <a href="{{ route('master.users.index') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Pengguna</a>
                        @endrole
                    </div>
                </div>
                @endhasanyrole

                <div class="relative" @click.away="tagihanMenuOpen = false">
                    <button @click="tagihanMenuOpen = !tagihanMenuOpen"
                        class="{{ request()->is('tagihan*') ? 'nav-active' : 'nav-item' }} inline-flex items-center gap-1">
                        Tagihan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="tagihanMenuOpen" x-transition.opacity.duration.200ms
                        class="absolute top-full left-0 mt-2 w-48 bg-white border border-border rounded-lg shadow-modal py-2 z-50"
                        style="display:none;">
                        <a href="{{ route('tagihan.index') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Daftar
                            Tagihan</a>
                        @role('superadmin')
                        <a href="{{ route('tagihan.rekap') }}"
                            class="block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5">Rekap
                            & Laporan</a>
                        @endrole
                    </div>
                </div>
            </nav>
        </div>

        {{-- Right Actions --}}
        <div class="hidden md:flex items-center gap-2">
            <button class="p-2 text-content-tertiary hover:text-content-primary transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
            <button class="p-2 text-content-tertiary hover:text-content-primary transition-colors relative">
                <div class="absolute top-1.5 right-1.5 w-2 h-2 bg-status-danger rounded-full"></div>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </button>

            <div class="h-6 w-px bg-border mx-2"></div>

            <div class="relative" @click.away="userMenuOpen = false">
                <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 focus:outline-none">
                    <div
                        class="w-8 h-8 rounded-full bg-primary-light text-primary-dark flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </button>
                <div x-show="userMenuOpen" x-transition.opacity.duration.200ms
                    class="absolute top-full right-0 mt-2 w-48 bg-white border border-border rounded-lg shadow-modal py-2 z-50"
                    style="display:none;">
                    <div class="px-4 py-2 border-b border-border mb-2">
                        <p class="text-sm font-medium text-content-primary">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-content-secondary">
                            {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'User') }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-status-danger hover:bg-red-50">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>