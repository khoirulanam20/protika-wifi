<header class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-border">
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 h-[60px] flex items-center justify-between">

        <div class="flex items-center gap-4 md:gap-6">
            {{-- Logo Pill --}}
            <div class="flex items-center gap-2 px-3 md:px-4 py-1.5 rounded-full border border-border bg-white shadow-sm">
                <div
                    class="w-5 h-5 md:w-6 md:h-6 rounded-full bg-primary flex items-center justify-center text-content-primary font-bold text-xs">
                    P</div>
                <span class="font-semibold text-content-primary text-xs md:text-sm">Protika</span>
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

        {{-- Right Actions Mobile --}}
        <div class="flex md:hidden items-center gap-2">
            <!-- Notifications Dropdown Mobile -->
            <div class="relative" x-data="notificationDropdown()" x-init="initNotifications({{ auth()->id() }})" @click.away="open = false">
                <button @click="open = !open" class="p-2 text-content-tertiary hover:text-content-primary transition-colors relative">
                    <div x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-0 right-0 flex items-center justify-center min-w-[1.1rem] h-[1.1rem] px-0.5 text-[9px] font-bold text-white bg-status-danger rounded-full border-2 border-white transform translate-x-1/4 -translate-y-1/4"></div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                </button>
                <div x-show="open" x-transition.opacity.duration.200ms
                    class="absolute top-full right-0 mt-2 bg-white border border-border rounded-xl shadow-lg z-50 overflow-hidden"
                    style="display:none; width: 320px; max-width: calc(100vw - 2rem);">
                    <div class="px-4 py-3 border-b border-border flex justify-between items-center bg-gray-50/50">
                        <span class="font-bold text-xs text-content-primary">Notifikasi</span>
                        <div class="flex gap-2">
                            <button @click="markAllAsRead" x-show="unreadCount > 0" class="text-[10px] text-primary font-semibold hover:text-primary-dark transition-colors">Tandai dibaca</button>
                            <button @click="deleteAllNotifications" x-show="notifications.length > 0" class="text-[10px] text-status-danger font-semibold hover:text-red-700 transition-colors">Hapus</button>
                        </div>
                    </div>
                    <div class="max-h-[350px] overflow-y-auto">
                        <template x-for="notif in notifications" :key="notif.id">
                            <div class="relative group px-3 py-2.5 border-b border-border hover:bg-gray-50 transition-colors" :class="{'bg-blue-50/30': !notif.read_at}">
                                <a :href="notif.data.url" @click="markAsRead(notif.id)" class="block pr-5">
                                    <div class="font-semibold text-xs text-content-primary" x-text="notif.data.title"></div>
                                    <div class="text-[11px] text-content-secondary mt-0.5" x-text="notif.data.message"></div>
                                    <div class="text-[10px] text-primary/70 mt-0.5" x-show="notif.data.kolektor" x-text="'Kolektor: ' + notif.data.kolektor"></div>
                                    <div class="text-[9px] text-gray-400 mt-0.5" x-text="new Date(notif.created_at).toLocaleString('id-ID')"></div>
                                </a>
                                <button @click.stop="deleteNotification(notif.id)" class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-status-danger hover:bg-red-50 rounded-md transition-colors opacity-0 group-hover:opacity-100" title="Hapus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <div x-show="notifications.length === 0" class="px-3 py-6 text-center text-xs text-content-secondary">
                            Belum ada notifikasi.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Actions Desktop --}}
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
            <!-- Notifications Dropdown -->
            <div class="relative" x-data="notificationDropdown()" x-init="initNotifications({{ auth()->id() }})" @click.away="open = false">
                <button @click="open = !open" class="p-2 text-content-tertiary hover:text-content-primary transition-colors relative">
                    <div x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-0 right-0 flex items-center justify-center min-w-[1.2rem] h-[1.2rem] px-1 text-[10px] font-bold text-white bg-status-danger rounded-full border-2 border-white transform translate-x-1/4 -translate-y-1/4"></div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                </button>
                <div x-show="open" x-transition.opacity.duration.200ms
                    class="absolute top-full right-0 mt-2 bg-white border border-border rounded-xl shadow-lg z-50 overflow-hidden"
                    style="display:none; width: 360px; max-width: 90vw;">
                    <div class="px-5 py-3 border-b border-border flex justify-between items-center bg-gray-50/50">
                        <span class="font-bold text-sm text-content-primary">Notifikasi</span>
                        <div class="flex gap-3">
                            <button @click="markAllAsRead" x-show="unreadCount > 0" class="text-[11px] text-primary font-semibold hover:text-primary-dark transition-colors">Tandai dibaca</button>
                            <button @click="deleteAllNotifications" x-show="notifications.length > 0" class="text-[11px] text-status-danger font-semibold hover:text-red-700 transition-colors">Hapus semua</button>
                        </div>
                    </div>
                    <div class="max-h-[350px] overflow-y-auto">
                        <template x-for="notif in notifications" :key="notif.id">
                            <div class="relative group px-4 py-3 border-b border-border hover:bg-gray-50 transition-colors" :class="{'bg-blue-50/30': !notif.read_at}">
                                <a :href="notif.data.url" @click="markAsRead(notif.id)" class="block pr-6">
                                    <div class="font-semibold text-sm text-content-primary" x-text="notif.data.title"></div>
                                    <div class="text-xs text-content-secondary mt-1" x-text="notif.data.message"></div>
                                    <div class="text-[11px] text-primary/70 mt-0.5" x-show="notif.data.kolektor" x-text="'Kolektor: ' + notif.data.kolektor"></div>
                                    <div class="text-[10px] text-gray-400 mt-1" x-text="new Date(notif.created_at).toLocaleString('id-ID')"></div>
                                </a>
                                <button @click.stop="deleteNotification(notif.id)" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-status-danger hover:bg-red-50 rounded-md transition-colors opacity-0 group-hover:opacity-100" title="Hapus notifikasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-sm text-content-secondary">
                            Belum ada notifikasi.
                        </div>
                    </div>
                </div>
            </div>

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
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5 transition-colors {{ request()->routeIs('profile.*') ? 'text-primary bg-primary/5' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>
                    <div class="border-t border-border my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-status-danger hover:bg-red-50 flex items-center gap-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>