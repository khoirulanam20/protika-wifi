@php
    $adminDesa = auth()->user()->adminDesa;
@endphp
@if($adminDesa)
    <div class="rounded-lg border border-border bg-base-page px-4 py-3">
        <p class="text-xs font-medium text-content-tertiary uppercase tracking-wider mb-1">Wilayah Desa</p>
        <p class="text-sm text-content-primary font-medium">
            {{ $adminDesa->desa }}{{ $adminDesa->desa && $adminDesa->kecamatan ? ', ' : '' }}{{ $adminDesa->kecamatan }}
        </p>
    </div>
    <input type="hidden" name="kecamatan" value="{{ $adminDesa->kecamatan }}">
    <input type="hidden" name="desa" value="{{ $adminDesa->desa }}">
    @if(!empty($withDesaKode))
        <input type="hidden" name="desa_kode" value="{{ $adminDesa->desa_kode }}">
    @endif
@endif
