@extends('layouts.app')

@section('title', 'Master Pelanggan')
@section('page-title', 'Master Pelanggan')
@section('page-subtitle', 'Kelola data seluruh pelanggan WiFi')

@section('content')

    <div x-data="pelangganData()">

        <x-list-filter-bar
            :reset-url="route('master.pelanggan.index')"
            :active-count="$activeFilterCount"
            :show-search="true"
            search-placeholder="Cari nama pelanggan..."
            :show-reset="request()->hasAny(['search', 'kecamatan', 'desa', 'dusun_id', 'kolektor_id', 'status_alat', 'sort_nama'])"
            :kecamatan-list="$kecamatanList"
            :desa-options="$desaOptions"
            :dusun-options="$dusunOptions"
            :show-wilayah-dusun="true">
            @role('superadmin')
            <div class="w-full md:w-auto space-y-1">
                <label class="block text-xs font-medium text-content-secondary md:hidden">Kolektor</label>
                <select name="kolektor_id" class="input-field w-full md:min-w-[9rem] md:max-w-[11rem] text-sm">
                    <option value="">Semua Kolektor</option>
                    @foreach($kolektor as $kol)
                        <option value="{{ $kol->id }}" {{ request('kolektor_id') == $kol->id ? 'selected' : '' }}>{{ $kol->nama_kolektor }}</option>
                    @endforeach
                </select>
            </div>
            @endrole
            <div class="w-full md:w-auto space-y-1">
                <label class="block text-xs font-medium text-content-secondary md:hidden">Status Alat</label>
                <select name="status_alat" class="input-field w-full md:min-w-[9rem] md:max-w-[11rem] text-sm">
                    <option value="">Semua Alat</option>
                    <option value="beli" {{ request('status_alat') == 'beli' ? 'selected' : '' }}>Beli</option>
                    <option value="pinjam" {{ request('status_alat') == 'pinjam' ? 'selected' : '' }}>Pinjam</option>
                </select>
            </div>
        </x-list-filter-bar>

        <div class="card overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-content-primary font-semibold text-lg">Daftar Pelanggan</h2>
                    <p class="text-content-secondary text-sm mt-0.5">Total: <span
                            class="text-content-primary font-medium">{{ $pelanggan->total() }}</span> pelanggan</p>
                </div>
                <button @click="openCreate()" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pelanggan
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                ID</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                <a href="{{ route('master.pelanggan.index', array_merge(request()->query(), ['sort_nama' => request('sort_nama') === 'nama_asc' ? 'nama_desc' : 'nama_asc'])) }}"
                                    class="inline-flex items-center gap-1.5 hover:text-content-primary transition-colors"
                                    title="Urutkan nama pelanggan">
                                    <span>Nama</span>
                                    @if(request('sort_nama') === 'nama_asc')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @elseif(request('sort_nama') === 'nama_desc')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8M8 12h8M8 17h8" />
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Wilayah</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Paket</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Kolektor</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Alat</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($pelanggan as $item)
                            <tr class="hover:bg-base-page transition-colors">
                                <td class="px-6 py-4 text-sm text-content-secondary font-mono">
                                    {{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-content-primary font-medium">{{ $item->nama_pelanggan }}</td>
                                <td class="px-6 py-4 text-sm text-content-secondary">
                                    {{ $item->kecamatan }}, {{ $item->desa }}<br>
                                    <span class="text-content-tertiary text-[11px]">{{ $item->dusun?->dusun ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-content-secondary">
                                    @if($item->bulanan)
                                        Rp {{ number_format($item->bulanan->nominal, 0, ',', '.') }}
                                    @else
                                        <span class="text-content-tertiary">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-content-secondary">
                                    {{ $item->kolektor?->nama_kolektor ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->status_alat === 'beli')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-primary-light text-primary-deep">Beli</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-status-info/20 text-status-info">Pinjam</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($item->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-status-success/20 text-status-success">Aktif</span>
                                        @if($item->aktif_kembali_at)
                                            <p class="text-[11px] text-content-tertiary mt-1">Aktif lagi: {{ $item->aktif_kembali_at->format('d M Y H:i') }}</p>
                                        @endif
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-status-danger/20 text-status-danger">Nonaktif</span>
                                        @if($item->nonaktif_at)
                                            <p class="text-[11px] text-content-tertiary mt-1">Nonaktif sejak: {{ $item->nonaktif_at->format('d M Y H:i') }}</p>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($item->is_active)
                                            <button type="button"
                                                @click="openStatusModal('{{ route('master.pelanggan.nonaktif', $item) }}', 'Nonaktifkan', '{{ $item->nama_pelanggan }}')"
                                                class="p-1.5 rounded-lg bg-status-danger/10 text-status-danger hover:bg-status-danger/20 transition-colors"
                                                title="Nonaktifkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                @click="openStatusModal('{{ route('master.pelanggan.aktifkan', $item) }}', 'Aktifkan', '{{ $item->nama_pelanggan }}')"
                                                class="p-1.5 rounded-lg bg-status-success/10 text-status-success hover:bg-status-success/20 transition-colors"
                                                title="Aktifkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        @endif
                                        <button @click="openEdit({{ $item->toJson() }})"
                                            class="p-1.5 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500/20 transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('master.pelanggan.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus pelanggan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg bg-status-danger/10 text-status-danger hover:bg-status-danger/20 transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-content-tertiary text-sm">
                                    Belum ada data pelanggan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($pelanggan->hasPages())
                <div class="px-6 py-4 border-t border-border">
                    {{ $pelanggan->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Pop-up --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto px-4 pt-4 pb-24" style="display: none;">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-content-primary/40 backdrop-blur-sm"
                @click="showModal = false"></div>

            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-xl shadow-modal w-full max-w-4xl mx-auto z-10 overflow-hidden">

                <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-base-page">
                    <h3 class="text-lg font-semibold text-content-primary"
                        x-text="mode === 'create' ? 'Tambah Pelanggan Baru' : 'Edit Data Pelanggan'"></h3>
                    <button @click="showModal = false"
                        class="text-content-tertiary hover:text-content-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="formAction" method="POST">
                    @csrf
                    <input type="hidden" name="_method" :value="formMethod">

                    <div class="p-6 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Data Pribadi --}}
                            <div class="space-y-4">
                                <h3 class="text-content-primary font-semibold text-base border-b border-border pb-2">
                                    Informasi Dasar</h3>

                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Nama Lengkap</label>
                                    <input type="text" name="nama_pelanggan" x-model="formData.nama_pelanggan"
                                        class="input-field @error('nama_pelanggan') border-status-danger @enderror" required>
                                    @error('nama_pelanggan')
                                        <p class="text-sm text-status-danger mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>

                                @hasanyrole('superadmin|kolektor')
                                @include('layouts.components.wilayah-api')
                                @else
                                @include('layouts.components.wilayah-fixed-admin-desa', ['withDesaKode' => true])
                                @endhasanyrole

                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Dusun / Wilayah</label>
                                    <select name="dusun_id" x-model="formData.dusun_id" class="input-field"
                                        @unlessrole('admin_desa') :disabled="!formData.desa" @endunlessrole>
                                        <option value="">Pilih Dusun</option>
                                        <template x-for="d in filteredDusun" :key="d.id">
                                            <option :value="d.id" x-text="d.dusun"></option>
                                        </template>
                                    </select>
                                    @unlessrole('admin_desa')
                                    <p class="text-[10px] text-content-tertiary mt-1" x-show="!formData.desa">Pilih
                                        desa/kelurahan terlebih dahulu</p>
                                    @endunlessrole
                                </div>

                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Kontak / No. HP</label>
                                    <input type="text" name="kontak" x-model="formData.kontak" class="input-field"
                                        placeholder="08xxxxxxxxxx">
                                </div>

                            </div>

                            {{-- Data Layanan --}}
                            <div class="space-y-4">
                                <h3 class="text-content-primary font-semibold text-base border-b border-border pb-2">Detail
                                    Layanan</h3>

                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Paket Bulanan</label>
                                    <select name="bulanan_id" x-model="formData.bulanan_id" class="input-field">
                                        <option value="">Pilih Paket</option>
                                        @foreach($bulanan as $b)
                                            <option value="{{ $b->id }}">Rp {{ number_format($b->nominal, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-content-secondary text-sm mb-2">Kolektor</label>
                                        <select name="kolektor_id" x-model="formData.kolektor_id" class="input-field" {{ auth()->user()->hasRole('kolektor') && !auth()->user()->hasRole('superadmin') ? 'disabled' : '' }}>
                                            <option value="">Pilih Kolektor</option>
                                            @foreach($kolektor as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama_kolektor }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-content-secondary text-sm mb-2">Teknisi Pemasangan</label>
                                        <select name="teknisi_id" x-model="formData.teknisi_id" class="input-field">
                                            <option value="">Pilih Teknisi</option>
                                            @foreach($teknisi as $t)
                                                <option value="{{ $t->id }}">{{ $t->nama_teknisi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-content-secondary text-sm mb-2">Tanggal Pasang</label>
                                        <input type="date" name="tanggal_pemasangan" x-model="formData.tanggal_pemasangan"
                                            class="input-field">
                                    </div>
                                    <div>
                                        <label class="block text-content-secondary text-sm mb-2">Status Alat</label>
                                        <select name="status_alat" x-model="formData.status_alat" class="input-field"
                                            required>
                                            <option value="pinjam">Pinjam</option>
                                            <option value="beli">Beli</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-content-secondary text-sm mb-2">Status Pelanggan</label>
                                        <select name="is_active" x-model="formData.is_active" class="input-field">
                                            <option value="1">Aktif</option>
                                            <option value="0">Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Lokasi Pemasangan --}}
                        <div class="space-y-4">
                            <h3 class="text-content-primary font-semibold text-base border-b border-border pb-2">Peta Lokasi
                                Pemasangan</h3>
                            @include('layouts.components.location-picker')
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-border flex justify-end gap-3 bg-base-page">
                        <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary"
                            x-text="mode === 'create' ? 'Simpan Pelanggan' : 'Update Pelanggan'"></button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Konfirmasi Status --}}
        <div x-show="showStatusModal" class="fixed inset-0 z-[60] overflow-y-auto px-4 pt-10 pb-24" style="display: none;">
            <div x-show="showStatusModal" x-transition.opacity class="fixed inset-0 bg-content-primary/40 backdrop-blur-sm"
                @click="showStatusModal = false"></div>
            <div x-show="showStatusModal" x-transition
                class="relative bg-white rounded-xl shadow-modal w-full max-w-md mx-auto z-10 overflow-hidden">
                <div class="px-6 py-4 border-b border-border bg-base-page">
                    <h3 class="text-base font-semibold text-content-primary">Konfirmasi Aksi</h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-content-secondary">
                        Yakin ingin <span class="font-semibold text-content-primary" x-text="statusActionLabel.toLowerCase()"></span>
                        pelanggan <span class="font-semibold text-content-primary" x-text="statusActionName"></span>?
                    </p>
                </div>
                <div class="px-6 py-4 border-t border-border flex justify-end gap-3 bg-base-page">
                    <button type="button" @click="showStatusModal = false" class="btn-secondary">Batal</button>
                    <form :action="statusActionUrl" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary" x-text="statusActionLabel"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @once
        @php
            $adminWilayahForJs = auth()->user()->hasRole('admin_desa')
                ? \App\Support\AdminDesaScope::wilayahDisplay()
                : null;
        @endphp
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pelangganData', () => ({
                    adminWilayah: @json($adminWilayahForJs),
                    showModal: {{ $errors->any() ? 'true' : 'false' }},
                    showStatusModal: false,
                    statusActionUrl: '',
                    statusActionLabel: 'Nonaktifkan',
                    statusActionName: '',
                    mode: 'create',
                    formAction: '{{ route('master.pelanggan.store') }}',
                    formMethod: 'POST',
                    formData: {
                        nama_pelanggan: '{{ old('nama_pelanggan') }}',
                        kecamatan: '{{ old('kecamatan') }}',
                        desa: '{{ old('desa') }}',
                        desa_kode: '{{ old('desa_kode') }}',
                        dusun_id: '{{ old('dusun_id') }}',
                        bulanan_id: '{{ old('bulanan_id') }}',
                        kolektor_id: '{{ old('kolektor_id') }}',
                        teknisi_id: '{{ old('teknisi_id') }}',
                        status_alat: '{{ old('status_alat', 'pinjam') }}',
                        is_active: '{{ old('is_active', '1') }}',
                        tanggal_pemasangan: '{{ old('tanggal_pemasangan') }}',
                        kontak: '{{ old('kontak') }}',
                        lokasi: '{{ old('lokasi') }}'
                    },
                    dusunList: @json($dusun),

                    get filteredDusun() {
                        if (!this.formData.desa_kode) return [];
                        return this.dusunList.filter(d =>
                            d.desa_kode === this.formData.desa_kode
                        );
                    },

                    init() {
                        this.$watch('formData.desa_kode', (newVal, oldVal) => {
                            if (oldVal && newVal && oldVal === newVal) {
                                return;
                            }
                            if (this.showModal && oldVal) {
                                this.formData.dusun_id = '';
                            }
                        });
                    },

                    openCreate() {
                        this.mode = 'create';
                        this.formAction = '{{ route('master.pelanggan.store') }}';
                        this.formMethod = 'POST';
                        this.formData = {
                            nama_pelanggan: '', kecamatan: '', desa: '', desa_kode: '', dusun_id: '',
                            bulanan_id: '', kolektor_id: '{{ auth()->user()->hasRole("kolektor") && !auth()->user()->hasRole("superadmin") ? auth()->user()->kolektor_id : "" }}', teknisi_id: '', status_alat: 'pinjam', is_active: '1', tanggal_pemasangan: '',
                            kontak: '', lokasi: ''
                        };
                        if (this.adminWilayah && this.adminWilayah.desa_kode) {
                            this.formData.kecamatan = this.adminWilayah.kecamatan || '';
                            this.formData.desa = this.adminWilayah.desa || '';
                            this.formData.desa_kode = this.adminWilayah.desa_kode;
                        }
                        this.showModal = true;
                    },

                    openEdit(item) {
                        this.mode = 'edit';
                        this.formAction = '/master/pelanggan/' + item.id;
                        this.formMethod = 'PUT';

                        let formattedDate = '';
                        if (item.tanggal_pemasangan) {
                            formattedDate = new Date(item.tanggal_pemasangan).toISOString().split('T')[0];
                        }

                        const dusun = this.dusunList.find(d => d.id == item.dusun_id);

                        this.formData = {
                            nama_pelanggan: item.nama_pelanggan,
                            kecamatan: item.kecamatan || '',
                            desa: item.desa || '',
                            desa_kode: dusun ? dusun.desa_kode : '',
                            dusun_id: '',
                            bulanan_id: item.bulanan_id || '',
                            kolektor_id: item.kolektor_id || '',
                            teknisi_id: item.teknisi_id || '',
                            status_alat: item.status_alat || 'pinjam',
                            is_active: item.is_active ? '1' : '0',
                            tanggal_pemasangan: formattedDate,
                            kontak: item.kontak || '',
                            lokasi: item.lokasi || ''
                        };

                        this.$nextTick(() => {
                            this.formData.dusun_id = item.dusun_id || '';
                        });

                        this.showModal = true;
                    },

                    openStatusModal(url, label, name) {
                        this.statusActionUrl = url;
                        this.statusActionLabel = label;
                        this.statusActionName = name;
                        this.showStatusModal = true;
                    }
                }));
            });
        </script>
    @endonce
@endpush