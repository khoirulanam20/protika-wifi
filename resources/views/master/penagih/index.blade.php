@extends('layouts.app')

@section('title', 'Master Penagih')
@section('page-title', 'Master Penagih')
@section('page-subtitle', 'Kelola data petugas penagihan lapangan')

@section('content')

    <div x-data="{ 
            showModal: {{ $errors->any() ? 'true' : 'false' }}, 
            mode: 'create',
            formAction: '{{ route('master.penagih.store') }}',
            formMethod: 'POST',
            formData: { nama_penagih: '{{ old('nama_penagih') }}', alamat: '{{ old('alamat') }}', kecamatan: '{{ old('kecamatan') }}', desa: '{{ old('desa') }}' },

            openCreate() {
                this.mode = 'create';
                this.formAction = '{{ route('master.penagih.store') }}';
                this.formMethod = 'POST';
                this.formData = { nama_penagih: '', alamat: '', kecamatan: '', desa: '' };
                this.showModal = true;
            },
            openEdit(item) {
                this.mode = 'edit';
                this.formAction = '/master/penagih/' + item.id;
                this.formMethod = 'PUT';
                this.formData = { 
                    nama_penagih: item.nama_penagih, 
                    alamat: item.alamat,
                    kecamatan: item.kecamatan,
                    desa: item.desa
                };
                this.showModal = true;
            }
        }">

        <x-list-filter-bar
            :reset-url="route('master.penagih.index')"
            :active-count="$activeFilterCount"
            :show-reset="request()->hasAny(['kecamatan', 'desa'])"
            :kecamatan-list="$kecamatanList"
            :desa-options="$desaOptions"
            :dusun-options="[]"
            :show-wilayah-dusun="false" />

        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-border flex items-center justify-between">
                <h2 class="text-content-primary font-semibold text-lg">Daftar Penagih</h2>
                <button @click="openCreate()" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Penagih
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Nama Penagih</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Wilayah</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($penagih as $item)
                            <tr class="hover:bg-base-page transition-colors">
                                <td class="px-6 py-4 text-sm text-content-primary">{{ $item->nama_penagih }}</td>
                                <td class="px-6 py-4 text-sm text-content-secondary">{{ $item->desa }}, {{ $item->kecamatan }}
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
                                        <form method="POST" action="{{ route('master.penagih.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus penagih ini?')">
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
                                <td colspan="3" class="px-6 py-10 text-center text-content-tertiary">Belum ada data penagih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                        x-text="mode === 'create' ? 'Tambah Penagih Baru' : 'Edit Data Penagih'"></h3>
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

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Nama Penagih</label>
                            <input type="text" name="nama_penagih" x-model="formData.nama_penagih" class="input-field"
                                required>
                        </div>
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" x-model="formData.alamat" class="input-field"></textarea>
                        </div>
                        @hasanyrole('superadmin|kolektor')
                        @include('layouts.components.wilayah-api')
                        @else
                        @include('layouts.components.wilayah-fixed-admin-desa')
                        @endhasanyrole
                    </div>

                    <div class="px-6 py-4 border-t border-border flex justify-end gap-3 bg-base-page">
                        <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary"
                            x-text="mode === 'create' ? 'Simpan Penagih' : 'Update Penagih'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection