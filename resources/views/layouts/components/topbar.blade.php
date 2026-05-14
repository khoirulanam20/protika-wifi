<header class="sticky top-0 z-20 px-6 py-4 flex items-center justify-between"
        style="background: rgba(255,255,255,0.04); backdrop-filter: blur(12px);
               border-bottom: 1px solid rgba(255,255,255,0.08);">

    {{-- Left: Hamburger + Breadcrumb --}}
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden p-2 rounded-xl hover:bg-white/10 transition-colors text-content-secondary hover:text-content-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div>
            <h1 class="text-content-primary font-semibold text-base">@yield('page-title', 'Dashboard')</h1>
            <p class="text-content-tertiary text-xs">@yield('page-subtitle', 'Selamat datang di Protika WiFi')</p>
        </div>
    </div>

    {{-- Right: Date + User --}}
    <div class="flex items-center gap-4">
        <p class="text-content-tertiary text-xs hidden sm:block">
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600
                    flex items-center justify-center text-content-primary font-bold text-sm">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
    </div>
</header>
