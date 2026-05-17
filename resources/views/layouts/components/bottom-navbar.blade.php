<!-- Mobile Bottom Navigation -->
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-border z-50 pb-safe">
    <div class="flex justify-around items-center h-16">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-content-secondary' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] font-medium">Dashboard</span>
        </a>

        <a href="{{ route('tagihan.index') }}" class="flex flex-col items-center justify-center w-full h-full space-y-1 {{ request()->is('tagihan*') ? 'text-primary' : 'text-content-secondary' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-[10px] font-medium">Tagihan</span>
        </a>

        <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex flex-col items-center justify-center w-full h-full space-y-1 text-content-secondary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span class="text-[10px] font-medium">Menu</span>
        </button>
    </div>
</div>

<!-- Mobile Menu Overlay -->
<div x-show="mobileMenuOpen" class="md:hidden fixed inset-0 z-50 bg-black/50" style="display: none;" x-transition.opacity @click="mobileMenuOpen = false"></div>

<!-- Mobile Menu Bottom Sheet -->
<div x-show="mobileMenuOpen" 
    class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-2xl shadow-xl border-t border-border"
    style="display: none;"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-y-full"
    x-transition:enter-end="transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="transform translate-y-0"
    x-transition:leave-end="transform translate-y-full"
    @click.away="mobileMenuOpen = false">
    
    <div class="flex justify-center pt-3 pb-2">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
    </div>
    
    <div class="px-6 pb-6 pt-2 max-h-[80vh] overflow-y-auto">
        <div class="mb-4 flex items-center justify-between border-b border-border pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary-light text-primary-dark flex items-center justify-center font-bold text-base">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-content-primary">{{ auth()->user()->name }}</h3>
                    <p class="text-xs text-content-secondary">{{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'User') }}</p>
                </div>
            </div>
            <button @click="mobileMenuOpen = false" class="p-2 text-content-tertiary hover:bg-gray-100 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="space-y-4 pb-20">
            @hasanyrole('superadmin|kolektor')
            <div>
                <p class="text-xs font-semibold text-content-tertiary uppercase tracking-wider mb-2">Master Data</p>
                <div class="space-y-1">
                    <a href="{{ route('master.pelanggan.index') }}" class="block px-3 py-2 rounded-lg text-sm text-content-secondary hover:bg-primary/5 hover:text-primary">Pelanggan</a>
                    <a href="{{ route('master.dusun.index') }}" class="block px-3 py-2 rounded-lg text-sm text-content-secondary hover:bg-primary/5 hover:text-primary">Wilayah</a>
                    <a href="{{ route('master.bulanan.index') }}" class="block px-3 py-2 rounded-lg text-sm text-content-secondary hover:bg-primary/5 hover:text-primary">Paket Bulanan</a>
                    @role('superadmin')
                    <a href="{{ route('master.kolektor.index') }}" class="block px-3 py-2 rounded-lg text-sm text-content-secondary hover:bg-primary/5 hover:text-primary">Kolektor</a>
                    @endrole
                    <a href="{{ route('master.teknisi.index') }}" class="block px-3 py-2 rounded-lg text-sm text-content-secondary hover:bg-primary/5 hover:text-primary">Teknisi</a>
                    <a href="{{ route('master.penagih.index') }}" class="block px-3 py-2 rounded-lg text-sm text-content-secondary hover:bg-primary/5 hover:text-primary">Penagih</a>
                    @role('superadmin')
                    <a href="{{ route('master.users.index') }}" class="block px-3 py-2 rounded-lg text-sm text-content-secondary hover:bg-primary/5 hover:text-primary">Pengguna</a>
                    @endrole
                </div>
            </div>
            @endhasanyrole
            
            @role('superadmin')
            <div>
                <p class="text-xs font-semibold text-content-tertiary uppercase tracking-wider mb-2">Tagihan</p>
                <div class="space-y-1">
                    <a href="{{ route('tagihan.rekap') }}" class="block px-3 py-2 rounded-lg text-sm text-content-secondary hover:bg-primary/5 hover:text-primary">Rekap & Laporan</a>
                </div>
            </div>
            @endrole

            <div>
                <p class="text-xs font-semibold text-content-tertiary uppercase tracking-wider mb-2">Akun</p>
                <div class="space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-status-danger hover:bg-red-50 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
