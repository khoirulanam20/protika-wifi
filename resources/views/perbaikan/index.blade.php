@extends('layouts.app')

@section('title', 'Laporan Perbaikan')
@section('page-title', 'Laporan Perbaikan')
@section('page-subtitle', 'Riwayat perbaikan teknisi: lokasi, waktu, dan foto')

@push('styles')
<style>
    /* Self-contained responsive layout (does not depend on the compiled Tailwind build) */
    .pb-toolbar { display: grid; gap: .75rem; padding: 1rem; border-bottom: 1px solid #e5e7eb; }
    .pb-add { order: -1; display: flex; align-items: center; justify-content: center; gap: .5rem; width: 100%; white-space: nowrap; }
    .pb-filter { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; align-items: end; }
    .pb-filter > * { min-width: 0; width: 100%; justify-content: center; text-align: center; }
    .pb-filter .pb-full { grid-column: 1 / -1; }
    .pb-table { display: none; }
    .pb-cards > .pb-card + .pb-card { border-top: 1px solid #e5e7eb; }
    .pb-card { display: flex; gap: .75rem; padding: 1rem; }
    .pb-card img { width: 5rem; height: 5rem; object-fit: cover; border-radius: .5rem; flex-shrink: 0; }
    .pb-card-body { min-width: 0; flex: 1; display: flex; flex-direction: column; gap: .25rem; }
    .pb-card-body a { overflow-wrap: anywhere; }
    .pb-card-actions { display: flex; align-items: center; gap: 1rem; padding-top: .25rem; font-size: .875rem; }
    .pb-card-actions form { display: inline; }
    .pb-card-actions button { background: none; border: 0; padding: 0; cursor: pointer; }
    .pb-pager { padding: 1rem; }
    @media (min-width: 768px) {
        .pb-table { display: block; overflow-x: auto; }
        .pb-cards { display: none; }
    }
    @media (min-width: 1024px) {
        .pb-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; }
        .pb-add { order: 0; width: auto; }
        .pb-filter { display: flex; flex-wrap: wrap; }
        .pb-filter > * { width: auto; }
        .pb-filter .pb-full { grid-column: auto; }
        .pb-pager { padding: 1rem 1.5rem; }
    }
</style>
@endpush

@section('content')
@php($canEdit = fn ($item) => auth()->user()->hasRole('superadmin') || $item->user_id === auth()->id())
<div class="card overflow-hidden" x-data="{ img: null }">
    <div x-show="img" x-transition.opacity style="display:none" @click="img = null" @keydown.escape.window="img = null"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80">
        <img :src="img" alt="Foto" class="rounded-lg" style="max-width:100%;max-height:90vh">
    </div>
    <div class="pb-toolbar">
        <a href="{{ route('perbaikan.create') }}" class="btn-primary pb-add">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah
        </a>

        <a href="{{ route('perbaikan.export', request()->query()) }}" class="btn-secondary pb-add" style="order:-1">Export Excel</a>

        <form method="GET" class="pb-filter">
            @if($teknisiUsers->isNotEmpty())
                <select name="user_id" class="input-field pb-full">
                    <option value="">Semua Petugas</option>
                    @foreach($teknisiUsers as $t)
                        <option value="{{ $t->id }}" @selected(request('user_id') == $t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>
            @endif
            @if(auth()->user()->hasRole('superadmin'))
                <select name="bayar" class="input-field pb-full">
                    <option value="">Semua Status Bayar</option>
                    <option value="1" @selected(request('bayar') === '1')>Sudah dibayar</option>
                    <option value="0" @selected(request('bayar') === '0')>Belum dibayar</option>
                </select>
            @endif
            <input type="date" name="dari" value="{{ request('dari') }}" class="input-field" aria-label="Dari tanggal">
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="input-field" aria-label="Sampai tanggal">
            @php($filtered = request()->hasAny(['user_id', 'bayar', 'dari', 'sampai']))
            <button class="btn-secondary {{ $filtered ? '' : 'pb-full' }}">Filter</button>
            @if($filtered)
                <a href="{{ route('perbaikan.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Desktop / tablet table --}}
    <div class="pb-table">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    @foreach(['Foto', 'Waktu', 'Petugas', 'Lokasi', 'Deskripsi', 'Aksi'] as $h)
                        <th class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($perbaikan as $item)
                    <tr class="hover:bg-base-page transition-colors">
                        <td class="px-6 py-4">
                            @if($item->foto_url)
                                <a href="{{ $item->foto_url }}" @click.prevent="img = '{{ $item->foto_url }}'"><img src="{{ $item->foto_url }}" style="width:4rem;height:4rem;object-fit:cover;border-radius:.5rem" alt="Foto"></a>
                            @else
                                <span class="text-content-tertiary text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-content-primary whitespace-nowrap">{{ $item->tanggal_perbaikan->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-content-secondary">{{ $item->user->petugasNama() }}</td>
                        <td class="px-6 py-4 text-sm text-content-secondary">
                            {{ $item->alamat ?: '—' }}<br>
                            <a href="{{ $item->maps_url }}" target="_blank" rel="noopener" class="text-primary text-xs">{{ $item->lokasi }} ↗</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-content-secondary" style="max-width:20rem">
                            @if($item->pelanggan)<span class="font-medium text-content-primary">{{ $item->pelanggan->nama_pelanggan }}</span><br>@endif
                            {{ \Illuminate\Support\Str::limit($item->deskripsi, 80) }}
                            <br>@if($item->dibayar_at)<span class="text-xs font-semibold text-status-success">✓ Jasa dibayar</span>@endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('perbaikan.show', $item) }}" class="text-primary">Detail</a>
                            @if($canEdit($item))
                                · <a href="{{ route('perbaikan.edit', $item) }}" class="text-primary">Edit</a>
                                <form method="POST" action="{{ route('perbaikan.destroy', $item) }}" style="display:inline" onsubmit="return confirm('Hapus laporan ini?')">
                                    @csrf @method('DELETE')
                                    · <button class="text-status-danger">Hapus</button>
                                </form>
                            @endif
                            @if(auth()->user()->hasRole('superadmin'))
                                <form method="POST" action="{{ route('perbaikan.bayar', $item) }}" style="display:inline">
                                    @csrf @method('PATCH')
                                    · <button class="text-primary">{{ $item->dibayar_at ? 'Batal Bayar' : 'Tandai Dibayar' }}</button>
                                </form>
                                @endif

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center text-content-tertiary">Belum ada laporan perbaikan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="pb-cards">
        @forelse($perbaikan as $item)
            <div class="pb-card">
                @if($item->foto_url)
                    <a href="{{ $item->foto_url }}" @click.prevent="img = '{{ $item->foto_url }}'"><img src="{{ $item->foto_url }}" alt="Foto"></a>
                @endif
                <div class="pb-card-body">
                    <p class="text-sm font-semibold text-content-primary">{{ $item->tanggal_perbaikan->format('d M Y H:i') }}</p>
                    <p class="text-xs text-content-secondary">{{ $item->user->petugasNama() }}@if($item->pelanggan) · {{ $item->pelanggan->nama_pelanggan }}@endif</p>
                    <p class="text-sm text-content-secondary">{{ \Illuminate\Support\Str::limit($item->deskripsi, 80) }}</p>
                    @if($item->alamat)<p class="text-xs text-content-secondary">{{ $item->alamat }}</p>@endif
                    <a href="{{ $item->maps_url }}" target="_blank" rel="noopener" class="text-primary text-xs">{{ $item->lokasi }} ↗</a>
                    @if($item->dibayar_at)<span class="text-xs font-semibold text-status-success">✓ Jasa dibayar</span>@endif
                    <div class="pb-card-actions">
                        <a href="{{ route('perbaikan.show', $item) }}" class="text-primary">Detail</a>
                        @if($canEdit($item))
                            <a href="{{ route('perbaikan.edit', $item) }}" class="text-primary">Edit</a>
                            <form method="POST" action="{{ route('perbaikan.destroy', $item) }}" onsubmit="return confirm('Hapus laporan ini?')">
                                @csrf @method('DELETE')
                                <button class="text-status-danger">Hapus</button>
                            </form>
                        @endif
                        @if(auth()->user()->hasRole('superadmin'))
                                <form method="POST" action="{{ route('perbaikan.bayar', $item) }}" style="display:inline">
                                    @csrf @method('PATCH')
                                    <button class="text-primary">{{ $item->dibayar_at ? 'Batal Bayar' : 'Tandai Dibayar' }}</button>
                                </form>
                                @endif

                    </div>
                </div>
            </div>
        @empty
            <p class="px-4 py-10 text-center text-content-tertiary">Belum ada laporan perbaikan.</p>
        @endforelse
    </div>

    <div class="pb-pager">{{ $perbaikan->links() }}</div>
</div>
@endsection
