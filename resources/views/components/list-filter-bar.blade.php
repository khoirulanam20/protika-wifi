@props([
    'resetUrl' => '',
    'activeCount' => 0,
    'showSearch' => false,
    'searchPlaceholder' => 'Cari...',
    'showReset' => false,
    'kecamatanList' => [],
    'desaOptions' => [],
    'dusunOptions' => [],
    'showWilayahKecamatan' => true,
    'showWilayahDesa' => true,
    'showWilayahDusun' => false,
    'enableWilayahAlpine' => true,
])

@php
    $slotHtml = $slot->isEmpty() ? '' : $slot->toHtml();
    $wilayahConfig = $enableWilayahAlpine ? [
        'kecamatan' => request('kecamatan', ''),
        'desa' => request('desa', ''),
        'dusun_id' => request('dusun_id', ''),
        'desaOptions' => $desaOptions,
        'dusunOptions' => $dusunOptions,
    ] : [];
@endphp

<form method="GET"
    @if($enableWilayahAlpine)
        x-data="listFilterData(@js($wilayahConfig))"
    @else
        x-data="{
            filterOpen: false,
            isDesktop: window.matchMedia('(min-width: 768px)').matches,
            init() {
                const mq = window.matchMedia('(min-width: 768px)');
                mq.addEventListener('change', (e) => {
                    this.isDesktop = e.matches;
                    if (e.matches) this.filterOpen = false;
                });
                this.$watch('filterOpen', (open) => {
                    document.body.style.overflow = open ? 'hidden' : '';
                });
            }
        }"
    @endif
    class="mb-4 md:mb-6">

    {{-- Mobile toolbar --}}
    <div class="md:hidden flex items-center gap-2 {{ $showSearch ? '' : 'justify-end' }}">
        @if($showSearch)
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="{{ $searchPlaceholder }}"
                class="input-field flex-1 min-w-0 text-sm"
                :disabled="isDesktop">
        @else
            <span class="flex-1 text-sm font-medium text-content-primary">Filter Data</span>
        @endif
        @include('layouts.components.filter-button', ['activeCount' => $activeCount])
    </div>

    {{-- Desktop filter bar --}}
    <fieldset class="hidden md:flex md:flex-wrap md:items-end md:gap-3 card px-6 py-4 border-0 m-0 min-w-0 w-full"
        :disabled="!isDesktop">
        @if($showSearch)
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="{{ $searchPlaceholder }}"
                class="input-field flex-1 min-w-40 text-sm">
        @endif

        @if($showWilayahKecamatan || $showWilayahDesa || $showWilayahDusun)
            @include('layouts.components.wilayah-filter-fields', [
                'kecamatanList' => $kecamatanList,
                'desaOptions' => $desaOptions,
                'dusunOptions' => $dusunOptions,
                'showKecamatan' => $showWilayahKecamatan,
                'showDesa' => $showWilayahDesa,
                'showDusun' => $showWilayahDusun,
                'layout' => 'inline',
            ])
        @endif

        {!! $slotHtml !!}

        <button type="submit" class="btn-primary px-5 text-sm whitespace-nowrap">Filter</button>
        @if($showReset)
            <a href="{{ $resetUrl }}" class="btn-secondary px-5 text-sm whitespace-nowrap">Reset</a>
        @endif
    </fieldset>

    {{-- Mobile bottom sheet --}}
    <div x-show="filterOpen" x-cloak class="md:hidden fixed inset-0 z-[60]" style="display: none;">
        <div class="absolute inset-0 bg-black/50"
            x-show="filterOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="filterOpen = false">
        </div>

        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-xl border-t border-border flex flex-col max-h-[85vh] pb-safe"
            x-show="filterOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform translate-y-full"
            x-transition:enter-end="transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform translate-y-0"
            x-transition:leave-end="transform translate-y-full"
            @click.stop>

            <div class="flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-3 border-b border-border flex-shrink-0">
                <h3 class="text-base font-semibold text-content-primary">Filter</h3>
                <button type="button" @click="filterOpen = false"
                    class="p-2 text-content-tertiary hover:bg-gray-100 rounded-full transition-colors"
                    aria-label="Tutup filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 py-4">
                <fieldset class="border-0 m-0 p-0 space-y-4" :disabled="isDesktop">
                    @if($showWilayahKecamatan || $showWilayahDesa || $showWilayahDusun)
                        @include('layouts.components.wilayah-filter-fields', [
                            'kecamatanList' => $kecamatanList,
                            'desaOptions' => $desaOptions,
                            'dusunOptions' => $dusunOptions,
                            'showKecamatan' => $showWilayahKecamatan,
                            'showDesa' => $showWilayahDesa,
                            'showDusun' => $showWilayahDusun,
                            'layout' => 'stacked',
                        ])
                    @endif

                    {!! $slotHtml !!}
                </fieldset>
            </div>

            <div class="flex-shrink-0 px-5 py-4 border-t border-border bg-white flex gap-3">
                <button type="submit" class="btn-primary flex-1 justify-center" @click="filterOpen = false">
                    Terapkan
                </button>
                @if($showReset)
                    <a href="{{ $resetUrl }}" class="btn-secondary flex-1 justify-center text-center">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </div>
</form>
