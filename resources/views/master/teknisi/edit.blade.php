@extends('layouts.app')

@section('title', 'Edit Teknisi')
@section('page-title', 'Edit Data Teknisi')

@section('content')

<div class="max-w-2xl">
    <div class="card p-8">
        <form action="{{ route('master.teknisi.update', $teknisi) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Nama Teknisi</label>
                    <input type="text" name="nama_teknisi" value="{{ old('nama_teknisi', $teknisi->nama_teknisi) }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="input-field">{{ old('alamat', $teknisi->alamat) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" value="{{ old('kecamatan', $teknisi->kecamatan) }}" class="input-field">
                    </div>
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Desa</label>
                        <input type="text" name="desa" value="{{ old('desa', $teknisi->desa) }}" class="input-field">
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('master.teknisi.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Update Teknisi</button>
            </div>
        </form>
    </div>
</div>

@endsection
