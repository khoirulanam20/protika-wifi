@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('page-title', 'Tambah Pelanggan Baru')
@section('page-subtitle', 'Pendaftaran pelanggan baru ke sistem Protika WiFi')

@section('content')

<div class="max-w-4xl">
    <a href="{{ route('master.pelanggan.index') }}" class="btn-secondary mb-6 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar
    </a>

    <div class="card p-8">
        <form action="{{ route('master.pelanggan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Data Pribadi --}}
                <div class="space-y-4">
                    <h3 class="text-content-primary font-semibold text-lg border-b border-border pb-2">Informasi Dasar</h3>
                    
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}"
                               class="input-field @error('nama_pelanggan') border-red-500/50 @enderror" required>
                        @error('nama_pelanggan') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Kecamatan</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="input-field">
                        </div>
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Desa</label>
                            <input type="text" name="desa" value="{{ old('desa') }}" class="input-field">
                        </div>
                    </div>

                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Dusun / Wilayah</label>
                        <select name="dusun_id" class="input-field">
                            <option value="">Pilih Dusun</option>
                            @foreach($dusun as $d)
                                <option value="{{ $d->id }}" {{ old('dusun_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->dusun }} ({{ $d->desa }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Data Layanan --}}
                <div class="space-y-4">
                    <h3 class="text-content-primary font-semibold text-lg border-b border-border pb-2">Detail Layanan</h3>

                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Paket Bulanan</label>
                        <select name="bulanan_id" class="input-field">
                            <option value="">Pilih Paket</option>
                            @foreach($bulanan as $b)
                                <option value="{{ $b->id }}" {{ old('bulanan_id') == $b->id ? 'selected' : '' }}>
                                    Rp {{ number_format($b->nominal, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Kolektor Penanggung Jawab</label>
                        <select name="kolektor_id" class="input-field">
                            <option value="">Pilih Kolektor</option>
                            @foreach($kolektor as $k)
                                <option value="{{ $k->id }}" {{ old('kolektor_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kolektor }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Tanggal Pasang</label>
                            <input type="date" name="tanggal_pemasangan" value="{{ old('tanggal_pemasangan') }}" class="input-field">
                        </div>
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Status Alat</label>
                            <select name="status_alat" class="input-field" required>
                                <option value="pinjam" {{ old('status_alat') == 'pinjam' ? 'selected' : '' }}>Pinjam</option>
                                <option value="beli" {{ old('status_alat') == 'beli' ? 'selected' : '' }}>Beli</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-border flex justify-end gap-3">
                <button type="reset" class="btn-secondary">Reset</button>
                <button type="submit" class="btn-primary px-8">Simpan Data Pelanggan</button>
            </div>
        </form>
    </div>
</div>

@endsection
