@php
    $wilayahDisplay = \App\Support\AdminDesaScope::wilayahDisplay();
@endphp
@if($wilayahDisplay)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-content-secondary text-sm mb-2">Provinsi</label>
            <div class="rounded-lg border border-border bg-base-page px-4 py-2.5 text-sm text-content-primary font-medium">
                {{ $wilayahDisplay['provinsi'] ?? '—' }}
            </div>
        </div>
        <div>
            <label class="block text-content-secondary text-sm mb-2">Kecamatan</label>
            <div class="rounded-lg border border-border bg-base-page px-4 py-2.5 text-sm text-content-primary font-medium">
                {{ $wilayahDisplay['kecamatan'] ?? '—' }}
            </div>
        </div>
        <div class="md:col-span-2">
            <label class="block text-content-secondary text-sm mb-2">Desa/Kelurahan</label>
            <div class="rounded-lg border border-border bg-base-page px-4 py-2.5 text-sm text-content-primary font-medium">
                {{ $wilayahDisplay['desa'] ?? '—' }}
            </div>
        </div>
    </div>
    <input type="hidden" name="kecamatan" value="{{ $wilayahDisplay['kecamatan'] }}">
    <input type="hidden" name="desa" value="{{ $wilayahDisplay['desa'] }}">
    @if(!empty($withDesaKode))
        <input type="hidden" name="desa_kode" value="{{ $wilayahDisplay['desa_kode'] }}">
    @endif
@else
    <div class="rounded-lg border border-status-danger/30 bg-status-danger/5 px-4 py-3">
        <p class="text-sm text-status-danger">Wilayah desa belum dikonfigurasi pada akun admin desa Anda. Hubungi superadmin.</p>
    </div>
@endif
