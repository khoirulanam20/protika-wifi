@extends('layouts.app')

@section('title', 'Edit Paket Bulanan')
@section('page-title', 'Edit Paket Bulanan')

@section('content')

<div class="max-w-2xl">
    <div class="card p-8">
        <form action="{{ route('master.bulanan.update', $bulanan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Nominal Harga</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-content-tertiary">Rp</span>
                        <input type="number" name="nominal" value="{{ old('nominal', $bulanan->nominal) }}" class="input-field pl-12" required>
                    </div>
                </div>
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Terbilang (Keterangan)</label>
                    <input type="text" name="terbilang" value="{{ old('terbilang', $bulanan->terbilang) }}" class="input-field" placeholder="Contoh: Seratus Ribu Rupiah" required>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('master.bulanan.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">Update Paket</button>
            </div>
        </form>
    </div>
</div>

@endsection
