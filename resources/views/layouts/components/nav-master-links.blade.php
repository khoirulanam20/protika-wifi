@hasanyrole('superadmin|kolektor|admin_desa')
<a href="{{ route('master.pelanggan.index') }}"
    class="{{ $class ?? 'block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5' }} {{ request()->routeIs('master.pelanggan.*') ? 'text-primary bg-primary/5 font-medium' : '' }}">
    Pelanggan
</a>
<a href="{{ route('master.dusun.index') }}"
    class="{{ $class ?? 'block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5' }} {{ request()->routeIs('master.dusun.*') ? 'text-primary bg-primary/5 font-medium' : '' }}">
    Dusun / Wilayah
</a>
<a href="{{ route('master.bulanan.index') }}"
    class="{{ $class ?? 'block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5' }} {{ request()->routeIs('master.bulanan.*') ? 'text-primary bg-primary/5 font-medium' : '' }}">
    Paket Bulanan
</a>
<a href="{{ route('master.kolektor.index') }}"
    class="{{ $class ?? 'block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5' }} {{ request()->routeIs('master.kolektor.*') ? 'text-primary bg-primary/5 font-medium' : '' }}">
    Kolektor
</a>
<a href="{{ route('master.teknisi.index') }}"
    class="{{ $class ?? 'block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5' }} {{ request()->routeIs('master.teknisi.*') ? 'text-primary bg-primary/5 font-medium' : '' }}">
    Teknisi
</a>
<a href="{{ route('master.penagih.index') }}"
    class="{{ $class ?? 'block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5' }} {{ request()->routeIs('master.penagih.*') ? 'text-primary bg-primary/5 font-medium' : '' }}">
    Penagih
</a>
@endhasanyrole
@role('superadmin')
<a href="{{ route('master.admin-desa.index') }}"
    class="{{ $class ?? 'block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5' }} {{ request()->routeIs('master.admin-desa.*') ? 'text-primary bg-primary/5 font-medium' : '' }}">
    Admin Desa
</a>
<a href="{{ route('master.users.index') }}"
    class="{{ $class ?? 'block px-4 py-2 text-sm text-content-secondary hover:text-primary hover:bg-primary/5' }} {{ request()->routeIs('master.users.*') ? 'text-primary bg-primary/5 font-medium' : '' }}">
    Pengguna
</a>
@endrole
