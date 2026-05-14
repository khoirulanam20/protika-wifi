@extends('layouts.app')

@section('title', 'Tambah Kolektor')
@section('page-title', 'Tambah Kolektor Baru')

@section('content')

<div class="max-w-2xl">
    <div class="card p-8">
        <form action="{{ route('master.kolektor.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Nama Kolektor</label>
                    <input type="text" name="nama_kolektor" value="{{ old('nama_kolektor') }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="input-field">{{ old('alamat') }}</textarea>
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
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('master.kolektor.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Kolektor</button>
            </div>
        </form>
    </div>
</div>

@endsection
