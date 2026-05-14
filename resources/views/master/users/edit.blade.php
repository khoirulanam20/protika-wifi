@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Data Pengguna')

@section('content')

<div class="max-w-2xl">
    <div class="card p-8">
        <form action="{{ route('master.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required>
                </div>
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="input-field">
                    </div>
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="input-field">
                    </div>
                </div>
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Role Sistem</label>
                    <select name="role" class="input-field" required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ old('role', $user->getRoleNames()->first()) == $r->name ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Mapping Kolektor (Opsional)</label>
                    <select name="kolektor_id" class="input-field">
                        <option value="">Tidak ada mapping</option>
                        @foreach($kolektor as $k)
                            <option value="{{ $k->id }}" {{ old('kolektor_id', $user->kolektor_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kolektor }}</option>
                        @endforeach
                    </select>
                    <p class="text-content-tertiary text-xs mt-1">Pilih jika pengguna ini bertindak sebagai kolektor penagihan.</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('master.users.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Update Pengguna</button>
            </div>
        </form>
    </div>
</div>

@endsection
