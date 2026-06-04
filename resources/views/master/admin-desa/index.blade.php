@extends('layouts.app')

@section('title', 'Admin Desa')
@section('page-title', 'Admin Desa')
@section('page-subtitle', 'Kelola akun admin per desa')

@section('content')

    <div x-data="adminDesaData()">

        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                <h2 class="text-content-primary font-semibold text-lg">Daftar Admin Desa</h2>
                <button @click="openCreate()" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Admin Desa
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">Nama Admin</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">Wilayah</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-content-tertiary uppercase tracking-wider">Jml Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">Akun Login</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($adminDesa as $item)
                            <tr class="hover:bg-base-page transition-colors">
                                <td class="px-6 py-4 text-sm text-content-primary font-medium">{{ $item->nama_admin }}</td>
                                <td class="px-6 py-4 text-sm text-content-secondary">
                                    @if($item->desa || $item->kecamatan)
                                        {{ $item->desa }}{{ $item->desa && $item->kecamatan ? ', ' : '' }}{{ $item->kecamatan }}
                                    @else
                                        <span class="text-content-tertiary">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-content-primary font-medium text-right tabular-nums">
                                    {{ number_format($item->pelanggan_count ?? 0) }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($item->user)
                                        <div>
                                            <p class="text-content-primary font-medium text-xs">{{ $item->user->email }}</p>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-status-success/10 text-status-success">Aktif</span>
                                        </div>
                                    @else
                                        <span class="text-content-tertiary text-xs italic">Belum ada akun</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button @click="openEdit({{ $item->toJson() }}, '{{ optional($item->user)->email }}')"
                                            class="p-1.5 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500/20 transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('master.admin-desa.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus admin desa ini? Akun login yang terhubung juga akan dihapus.')">
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
                                <td colspan="5" class="px-6 py-10 text-center text-content-tertiary">Belum ada data admin desa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($adminDesa->hasPages())
                <div class="px-6 py-4 border-t border-border">
                    {{ $adminDesa->links() }}
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
                class="relative bg-white rounded-xl shadow-modal w-full max-w-2xl mx-auto z-10 overflow-hidden">

                <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-base-page">
                    <h3 class="text-lg font-semibold text-content-primary"
                        x-text="mode === 'create' ? 'Tambah Admin Desa Baru' : 'Edit Data Admin Desa'"></h3>
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

                    <div class="p-6 space-y-5">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-content-primary border-b border-border pb-2 mb-4">
                                Informasi Admin Desa</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Nama Admin <span
                                            class="text-status-danger">*</span></label>
                                    <input type="text" name="nama_admin" x-model="formData.nama_admin" class="input-field"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Alamat Lengkap</label>
                                    <textarea name="alamat" rows="2" x-model="formData.alamat"
                                        class="input-field"></textarea>
                                </div>
                                @include('layouts.components.wilayah-api')
                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Kontak / No. HP</label>
                                    <input type="text" name="kontak" x-model="formData.kontak" class="input-field"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                                @include('layouts.components.location-picker')
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-content-primary border-b border-border pb-2 mb-4">
                                Akun Login
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-content-secondary text-sm mb-2">Email Login</label>
                                    <input type="email" name="email" x-model="formData.email" class="input-field"
                                        :placeholder="formData.existingEmail || 'contoh@email.com'">
                                </div>
                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">
                                        Password
                                        <span x-show="mode === 'edit'" class="text-xs text-content-tertiary">(kosongkan jika
                                            tidak diubah)</span>
                                    </label>
                                    <input type="password" name="password" class="input-field"
                                        placeholder="Min. 6 karakter">
                                </div>
                                <div>
                                    <label class="block text-content-secondary text-sm mb-2">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="input-field"
                                        placeholder="Ulangi password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-border flex justify-end gap-3 bg-base-page">
                        <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary"
                            x-text="mode === 'create' ? 'Simpan Admin Desa' : 'Update Admin Desa'"></button>
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
                Alpine.data('adminDesaData', () => ({
                    showModal: {{ $errors->any() ? 'true' : 'false' }},
                    mode: 'create',
                    formAction: '{{ route('master.admin-desa.store') }}',
                    formMethod: 'POST',
                    formData: {
                        nama_admin: '{{ old('nama_admin') }}',
                        alamat: '{{ old('alamat') }}',
                        kecamatan: '{{ old('kecamatan') }}',
                        desa: '{{ old('desa') }}',
                        desa_kode: '{{ old('desa_kode') }}',
                        kontak: '{{ old('kontak') }}',
                        lokasi: '{{ old('lokasi') }}',
                        email: '{{ old('email') }}',
                        existingEmail: '',
                    },

                    openCreate() {
                        this.mode = 'create';
                        this.formAction = '{{ route('master.admin-desa.store') }}';
                        this.formMethod = 'POST';
                        this.formData = {
                            nama_admin: '', alamat: '', kecamatan: '', desa: '', desa_kode: '',
                            kontak: '', lokasi: '', email: '', existingEmail: '',
                        };
                        this.showModal = true;
                    },

                    openEdit(item, existingEmail) {
                        this.mode = 'edit';
                        this.formAction = '/master/admin-desa/' + item.id;
                        this.formMethod = 'PUT';
                        this.formData = {
                            nama_admin: item.nama_admin,
                            alamat: item.alamat || '',
                            kecamatan: item.kecamatan || '',
                            desa: item.desa || '',
                            desa_kode: item.desa_kode || '',
                            kontak: item.kontak || '',
                            lokasi: item.lokasi || '',
                            email: existingEmail || '',
                            existingEmail: existingEmail || '',
                        };
                        this.showModal = true;
                    }
                }));
            });
        </script>
    @endonce
@endpush
