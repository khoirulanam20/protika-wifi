@extends('layouts.app')

@section('title', 'Input Tagihan')
@section('page-title', 'Pencatatan Tagihan Baru')
@section('page-subtitle', 'Input data pembayaran bulanan pelanggan')

@section('content')

<div class="max-w-3xl">
    <div class="card p-8">
        <form action="{{ route('tagihan.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-content-secondary text-sm mb-2">Pilih Pelanggan</label>
                    <select name="pelanggan_id" class="input-field" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($pelanggan as $p)
                            <option value="{{ $p->id }}" {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_pelanggan }} ({{ $p->desa }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Bulan</label>
                        <select name="bulan" class="input-field" required>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ (old('bulan') ?? now()->month) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Tahun</label>
                        <select name="tahun" class="input-field" required>
                            @foreach(range(now()->year - 1, now()->year + 1) as $y)
                                <option value="{{ $y }}" {{ (old('tahun') ?? now()->year) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Nominal Tagihan</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-content-tertiary">Rp</span>
                            <input type="number" name="nominal" value="{{ old('nominal') }}"
                                   class="input-field pl-12" placeholder="0" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-content-secondary text-sm mb-2">Status Pembayaran</label>
                        <select name="status" class="input-field" required>
                            <option value="belum_lunas" {{ old('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            <option value="lunas" {{ old('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                            <option value="sebagian" {{ old('status') == 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-content-secondary text-sm mb-2">Tanggal Bayar (Jika Lunas)</label>
                    <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar') }}" class="input-field">
                </div>

                <div>
                    <label class="block text-content-secondary text-sm mb-2">Keterangan Tambahan</label>
                    <textarea name="keterangan" rows="3" class="input-field">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-border flex justify-end gap-3">
                <a href="{{ route('tagihan.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary px-8">Simpan Tagihan</button>
            </div>
        </form>
    </div>
</div>

@endsection
