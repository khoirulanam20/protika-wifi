@extends('layouts.app')

@section('title', 'Master Pelanggan')
@section('page-title', 'Master Pelanggan')
@section('page-subtitle', 'Kelola data seluruh pelanggan WiFi')

@section('content')

    <div x-data="pelangganData()">

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

            {{-- Filter Bar --}}
            <form method="GET" class="px-6 py-4 border-b border-border flex flex-wrap gap-3 bg-base-page">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelanggan..."
                    class="input-field flex-1 min-w-48" />
                <select name="kecamatan" class="input-field w-44">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatanList as $kec)
                        <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                    @endforeach
                </select>
                <select name="desa" class="input-field w-44">
                    <option value="">Semua Desa</option>
                    @foreach($desaList as $ds)
                        <option value="{{ $ds }}" {{ request('desa') == $ds ? 'selected' : '' }}>{{ $ds }}</option>
                    @endforeach
                </select>
                @role('superadmin')
                <select name="kolektor_id" class="input-field w-40">
                    <option value="">Semua Kolektor</option>
                    @foreach($kolektor as $kol)
                        <option value="{{ $kol->id }}" {{ request('kolektor_id') == $kol->id ? 'selected' : '' }}>{{ $kol->nama_kolektor }}</option>
                    @endforeach
                </select>
                @endrole
                <select name="status_alat" class="input-field w-36">
                    <option value="">Semua Alat</option>
                    <option value="beli" {{ request('status_alat') == 'beli' ? 'selected' : '' }}>Beli</option>
                    <option value="pinjam" {{ request('status_alat') == 'pinjam' ? 'selected' : '' }}>Pinjam</option>
                </select>
                <button type="submit" class="btn-primary px-5">Filter</button>
                @if(request()->hasAny(['search', 'kecamatan', 'desa', 'kolektor_id', 'status_alat']))
                    <a href="{{ route('master.pelanggan.index') }}" class="btn-secondary px-5">Reset</a>
                @endif
            </form>

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
                                Nama</th>
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
                                    {{ $item->desa }}, {{ $item->kecamatan }}
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
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
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
                                <td colspan="7" class="px-6 py-12 text-center text-content-tertiary text-sm">
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
                                        class="input-field" required>
                                </div>

                                @include('layouts.components.wilayah-api')

                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Dusun / Wilayah</label>
                                    <select name="dusun_id" x-model="formData.dusun_id" class="input-field"
                                        :disabled="!formData.desa">
                                        <option value="">Pilih Dusun</option>
                                        <template x-for="d in filteredDusun" :key="d.id">
                                            <option :value="d.id" x-text="d.dusun"></option>
                                        </template>
                                    </select>
                                    <p class="text-[10px] text-content-tertiary mt-1" x-show="!formData.desa">Pilih
                                        desa/kelurahan terlebih dahulu</p>
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

                                <div class="grid grid-cols-2 gap-4">
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
    </div>

@endsection

@push('scripts')
    @once
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pelangganData', () => ({
                    showModal: {{ $errors->any() ? 'true' : 'false' }},
                    mode: 'create',
                    formAction: '{{ route('master.pelanggan.store') }}',
                    formMethod: 'POST',
                    formData: {
                        nama_pelanggan: '{{ old('nama_pelanggan') }}',
                        kecamatan: '{{ old('kecamatan') }}',
                        desa: '{{ old('desa') }}',
                        dusun_id: '{{ old('dusun_id') }}',
                        bulanan_id: '{{ old('bulanan_id') }}',
                        kolektor_id: '{{ old('kolektor_id') }}',
                        teknisi_id: '{{ old('teknisi_id') }}',
                        status_alat: '{{ old('status_alat', 'pinjam') }}',
                        tanggal_pemasangan: '{{ old('tanggal_pemasangan') }}',
                        kontak: '{{ old('kontak') }}',
                        lokasi: '{{ old('lokasi') }}'
                    },
                    dusunList: @json($dusun),

                    get filteredDusun() {
                        if (!this.formData.desa) return [];
                        const searchDesa = this.formData.desa.toUpperCase();
                        return this.dusunList.filter(d =>
                            d.desa && d.desa.toUpperCase() === searchDesa
                        );
                    },

                    init() {
                        // Reset dusun_id saat desa berubah, kecuali jika nilainya sama (misal diupdate ulang oleh API)
                        this.$watch('formData.desa', (newVal, oldVal) => {
                            if (oldVal && newVal && oldVal.toUpperCase() === newVal.toUpperCase()) {
                                return;
                            }

                            // Hanya reset jika modal sedang terbuka dan oldVal bukan kosong (artinya user benar-benar mengganti desa)
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
                            nama_pelanggan: '', kecamatan: '', desa: '', dusun_id: '',
                            bulanan_id: '', kolektor_id: '{{ auth()->user()->hasRole("kolektor") && !auth()->user()->hasRole("superadmin") ? auth()->user()->kolektor_id : "" }}', teknisi_id: '', status_alat: 'pinjam', tanggal_pemasangan: '',
                            kontak: '', lokasi: ''
                        };
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

                        this.formData = {
                            nama_pelanggan: item.nama_pelanggan,
                            kecamatan: item.kecamatan || '',
                            desa: item.desa || '',
                            dusun_id: '', // Biarkan kosong dulu saat DOM re-render
                            bulanan_id: item.bulanan_id || '',
                            kolektor_id: item.kolektor_id || '',
                            teknisi_id: item.teknisi_id || '',
                            status_alat: item.status_alat || 'pinjam',
                            tanggal_pemasangan: formattedDate,
                            kontak: item.kontak || '',
                            lokasi: item.lokasi || ''
                        };

                        // Gunakan $nextTick agar DOM <option> dari wilayah selesai di-render
                        // sebelum x-model mencoba memilih dusun_id-nya.
                        this.$nextTick(() => {
                            this.formData.dusun_id = item.dusun_id || '';
                        });

                        this.showModal = true;
                    }
                }));
            });
        </script>
    @endonce
@endpush