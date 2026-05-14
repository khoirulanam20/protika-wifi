<aside class="fixed left-0 top-0 h-screen w-64 z-40 transition-transform duration-300
              lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       style="background: rgba(255,255,255,0.05); backdrop-filter: blur(24px);
              border-right: 1px solid rgba(255,255,255,0.1);">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-border">
        <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-xl text-content-primary
                    bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/40">
            P
        </div>
        <div>
            <p class="text-content-primary font-bold text-base leading-tight">Protika</p>
            <p class="text-content-secondary text-xs">Sistem Tagihan WiFi</p>
        </div>
    </div>

    {{-- User Info --}}
    <div class="px-5 py-4 border-b border-border">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-pink-500 to-purple-600
                        flex items-center justify-center text-content-primary font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-content-primary text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium
                             bg-purple-500/25 text-purple-300 border border-purple-500/30">
                    {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'User') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="px-3 py-4 overflow-y-auto h-[calc(100vh-200px)] space-y-1">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- Master Data (Superadmin Only) --}}
        @role('superadmin')
        <div x-data="{ open: {{ request()->is('master/*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="nav-item w-full justify-between {{ request()->is('master/*') ? 'nav-active' : '' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 7h16M4 12h16M4 17h7"/>
                    </svg>
                    Master Data
                </span>
                <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="ml-4 mt-1 space-y-1 pl-4
                                                    border-l border-border">
                <a href="{{ route('master.pelanggan.index') }}"
                   class="nav-sub {{ request()->routeIs('master.pelanggan.*') ? 'nav-sub-active' : '' }}">
                    Pelanggan
                </a>
                <a href="{{ route('master.dusun.index') }}"
                   class="nav-sub {{ request()->routeIs('master.dusun.*') ? 'nav-sub-active' : '' }}">
                    Dusun / Wilayah
                </a>
                <a href="{{ route('master.bulanan.index') }}"
                   class="nav-sub {{ request()->routeIs('master.bulanan.*') ? 'nav-sub-active' : '' }}">
                    Paket Bulanan
                </a>
                <a href="{{ route('master.kolektor.index') }}"
                   class="nav-sub {{ request()->routeIs('master.kolektor.*') ? 'nav-sub-active' : '' }}">
                    Kolektor
                </a>
                <a href="{{ route('master.teknisi.index') }}"
                   class="nav-sub {{ request()->routeIs('master.teknisi.*') ? 'nav-sub-active' : '' }}">
                    Teknisi
                </a>
                <a href="{{ route('master.penagih.index') }}"
                   class="nav-sub {{ request()->routeIs('master.penagih.*') ? 'nav-sub-active' : '' }}">
                    Penagih
                </a>
                <a href="{{ route('master.users.index') }}"
                   class="nav-sub {{ request()->routeIs('master.users.*') ? 'nav-sub-active' : '' }}">
                    Pengguna
                </a>
            </div>
        </div>
        @endrole

        {{-- Tagihan --}}
        <div x-data="{ open: {{ request()->is('tagihan*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    class="nav-item w-full justify-between {{ request()->is('tagihan*') ? 'nav-active' : '' }}">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Tagihan
                </span>
                <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="ml-4 mt-1 space-y-1 pl-4 border-l border-border">
                <a href="{{ route('tagihan.index') }}"
                   class="nav-sub {{ request()->routeIs('tagihan.index') ? 'nav-sub-active' : '' }}">
                    Daftar Tagihan
                </a>
                <a href="{{ route('tagihan.create') }}"
                   class="nav-sub {{ request()->routeIs('tagihan.create') ? 'nav-sub-active' : '' }}">
                    Input Tagihan
                </a>
                @role('superadmin')
                <a href="{{ route('tagihan.rekap') }}"
                   class="nav-sub {{ request()->routeIs('tagihan.rekap*') ? 'nav-sub-active' : '' }}">
                    Rekap & Laporan
                </a>
                @endrole
            </div>
        </div>

    </nav>

    {{-- Logout --}}
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-border">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-red-400/80 hover:text-red-400
                                         hover:bg-red-500/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- CSS helper classes --}}
<style>
.nav-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.625rem 1rem; border-radius: 0.75rem;
    font-size: 0.875rem; font-weight: 500;
    color: rgba(240,244,255,0.65);
    transition: all 0.2s;
    cursor: pointer;
}
.nav-item:hover { background: rgba(255,255,255,0.1); color: #F0F4FF; }
.nav-active {
    background: linear-gradient(135deg, rgba(108,99,255,0.5), rgba(79,70,229,0.3));
    border: 1px solid rgba(108,99,255,0.35);
    color: #F0F4FF;
    box-shadow: 0 4px 12px rgba(108,99,255,0.2);
}
.nav-sub {
    display: block; padding: 0.5rem 0.75rem; border-radius: 0.5rem;
    font-size: 0.8125rem; color: rgba(240,244,255,0.55);
    transition: all 0.15s;
}
.nav-sub:hover { color: #F0F4FF; background: rgba(255,255,255,0.08); }
.nav-sub-active { color: #a78bfa; font-weight: 600; }
</style>
