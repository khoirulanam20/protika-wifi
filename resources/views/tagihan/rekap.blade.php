@extends('layouts.app')

@section('title', 'Rekap & Laporan')
@section('page-title', 'Rekap & Laporan')
@section('page-subtitle', 'Analisis data tagihan dan penerimaan kas')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="card p-6">
        <p class="text-content-tertiary text-xs font-medium uppercase mb-1">Total Piutang / Omzet</p>
        <p class="text-content-primary text-3xl font-bold">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
    </div>
    <div class="card p-6">
        <p class="text-content-tertiary text-xs font-medium uppercase mb-1">Total Transaksi Lunas</p>
        <p class="text-content-primary text-3xl font-bold">{{ $totalLunas }} <span class="text-content-tertiary text-sm font-normal">Selesai</span></p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="px-6 py-5 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="text-content-primary font-semibold text-lg">Laporan Rincian</h2>
        @role('superadmin')
        <a href="{{ route('tagihan.rekap.export', request()->all()) }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Ekspor Excel
        </a>
        @endrole
    </div>

    {{-- Filter --}}
    <form class="px-6 py-4 border-b border-border flex flex-wrap gap-3">
        <select name="bulan" class="input-field w-40">
            <option value="">Semua Bulan</option>
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
            @endforeach
        </select>
        <select name="kolektor_id" class="input-field w-48">
            <option value="">Semua Kolektor</option>
            @foreach($kolektor as $k)
                <option value="{{ $k->id }}" {{ request('kolektor_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kolektor }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-content-tertiary text-left border-b border-border">
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider">Pelanggan</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider">Kolektor</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider">Periode</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Nominal</th>
                    <th class="px-6 py-4 font-semibold uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($rekap as $item)
                <tr class="hover:bg-base-page transition-colors">
                    <td class="px-6 py-4 text-content-primary">{{ $item->pelanggan->nama_pelanggan }}</td>
                    <td class="px-6 py-4 text-content-secondary">{{ $item->kolektor?->nama_kolektor ?? '—' }}</td>
                    <td class="px-6 py-4 text-content-secondary">{{ $item->bulan }}/{{ $item->tahun }}</td>
                    <td class="px-6 py-4 text-content-primary text-right font-medium">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                     {{ $item->status === 'lunas' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-content-tertiary">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
