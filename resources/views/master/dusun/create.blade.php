@extends('layouts.app')

@section('title', 'Tambah Dusun')
@section('page-title', 'Tambah Dusun Baru')

@section('content')

<div class="max-w-2xl">
    <div class="card p-8">
        <form action="{{ route('master.dusun.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Desa</label>
                    <input type="text" name="desa" value="{{ old('desa') }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Nama Dusun</label>
                    <input type="text" name="dusun" value="{{ old('dusun') }}" class="input-field" required>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('master.dusun.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Simpan Dusun</button>
            </div>
        </form>
    </div>
</div>

@endsection
