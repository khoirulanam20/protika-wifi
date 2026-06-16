@props([
    'kecamatanList' => [],
    'desaOptions' => [],
    'dusunOptions' => [],
    'showKecamatan' => true,
    'showDesa' => true,
    'showDusun' => false,
    'layout' => 'inline',
])

@php
    $isStacked = $layout === 'stacked';
    $fieldClass = $isStacked
        ? 'input-field w-full'
        : 'input-field w-full min-w-0 md:flex-1 md:min-w-[9rem] md:max-w-[11rem]';
    $wrapperClass = $isStacked ? 'space-y-1' : '';
@endphp

@if($showKecamatan)
    @unlessrole('admin_desa')
        <div class="{{ $wrapperClass }}">
            @if($isStacked)
                <label class="block text-xs font-medium text-content-secondary mb-1">Kecamatan</label>
            @endif
            <select name="kecamatan" x-model="filterData.kecamatan" class="{{ $fieldClass }}">
                <option value="">Semua Kecamatan</option>
                @foreach($kecamatanList as $kec)
                    <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                @endforeach
            </select>
        </div>
    @endunlessrole
@endif

@if($showDesa)
    @unlessrole('admin_desa')
        <div class="{{ $wrapperClass }}">
            @if($isStacked)
                <label class="block text-xs font-medium text-content-secondary mb-1">Desa</label>
            @endif
            <select name="desa" x-model="filterData.desa" class="{{ $fieldClass }}">
                <option value="">Semua Desa</option>
                <template x-for="ds in filteredFilterDesaList" :key="ds">
                    <option :value="ds" x-text="ds" :selected="ds === filterData.desa"></option>
                </template>
            </select>
        </div>
    @endunlessrole
@endif

@if($showDusun)
    <div class="{{ $wrapperClass }}">
        @if($isStacked)
            <label class="block text-xs font-medium text-content-secondary mb-1">Dusun</label>
        @endif
        <select name="dusun_id" x-model="filterData.dusun_id" class="{{ $fieldClass }}">
            <option value="">Semua Dusun</option>
            <template x-for="d in filteredFilterDusunList" :key="d.id">
                <option :value="String(d.id)" x-text="d.dusun" :selected="String(d.id) === String(filterData.dusun_id)"></option>
            </template>
        </select>
    </div>
@endif
