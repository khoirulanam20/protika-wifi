@extends('layouts.app')

@section('title', 'Detail Perbaikan')
@section('page-title', 'Detail Perbaikan')
@section('page-subtitle', $perbaikan->tanggal_perbaikan->format('d M Y H:i') . ' · ' . $perbaikan->user->petugasNama())

@push('styles')
<style>
    .pb-dl { display: grid; grid-template-columns: 1fr; gap: .25rem 1rem; font-size: .875rem; }
    .pb-dl dt { margin-top: .5rem; }
    .pb-dl dd { margin: 0; word-break: break-word; }
    .pb-photo { max-width: 100%; max-height: 24rem; }
    @media (min-width: 640px) { .pb-dl { grid-template-columns: 9rem 1fr; } .pb-dl dt { margin-top: 0; } }
</style>
@endpush

@section('content')
<div class="card p-4 sm:p-6 max-w-3xl space-y-4">
    @if($perbaikan->foto_url)
        <img src="{{ $perbaikan->foto_url }}" class="pb-photo rounded-lg border border-border" alt="Foto perbaikan">
    @endif
    <dl class="pb-dl">
        <dt class="text-content-tertiary">Pelanggan</dt><dd class="text-content-primary">{{ $perbaikan->pelanggan?->nama_pelanggan ?? '—' }}</dd>
        <dt class="text-content-tertiary">Alamat</dt><dd class="text-content-primary">{{ $perbaikan->alamat ?: '—' }}</dd>
        <dt class="text-content-tertiary">Koordinat</dt><dd><a href="{{ $perbaikan->maps_url }}" target="_blank" rel="noopener" class="text-primary">{{ $perbaikan->lokasi }} ↗</a></dd>
        <dt class="text-content-tertiary">Deskripsi</dt><dd class="text-content-primary whitespace-pre-line">{{ $perbaikan->deskripsi }}</dd>
    </dl>
    <a href="{{ route('perbaikan.index') }}" class="btn-secondary inline-block">Kembali</a>
</div>
@endsection
