@extends('layouts.app')

@section('title', 'Daftar Tagihan')
@section('page-title', 'Daftar Tagihan')
@section('page-subtitle', 'Tagihan bulanan pelanggan — dibuat otomatis setiap bulan')

@section('content')

    <div x-data="tagihanData()">

        {{-- Tunggakan Alert --}}
        @if($totalTunggakan > 0)
            <div class="mb-4 p-4 rounded-xl bg-status-danger/10 border border-status-danger/20 flex items-center gap-3">
                <svg class="w-5 h-5 text-status-danger flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <div>
                    <p class="text-status-danger font-semibold text-sm">⚠ Terdapat <strong>{{ $totalTunggakan }}
                            tunggakan</strong> dari bulan-bulan sebelumnya yang belum diselesaikan.</p>
                    <p class="text-status-danger/70 text-xs mt-0.5">Filter bulan sebelumnya untuk melihat detail tunggakan.</p>
                </div>
            </div>
        @endif

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-status-success/10 border border-status-success/20 flex items-center gap-3">
                <svg class="w-5 h-5 text-status-success flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-status-success font-medium text-sm">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('info'))
            <div class="mb-4 p-4 rounded-xl bg-status-info/10 border border-status-info/20 flex items-center gap-3">
                <svg class="w-5 h-5 text-status-info flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-status-info font-medium text-sm">{{ session('info') }}</p>
            </div>
        @endif

        <div class="card overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-content-primary font-semibold text-lg">Daftar Tagihan</h2>
                    <p class="text-content-secondary text-sm mt-0.5">
                        Periode: <span class="font-medium text-content-primary">{{ date('F', mktime(0, 0, 0, $bulan, 1)) }}
                            {{ $tahun }}</span>
                        — Total: <span class="font-medium text-content-primary">{{ $tagihan->total() }}</span> entri
                    </p>
                </div>
            </div>

            {{-- Filter Bar --}}
            <form method="GET" class="px-6 py-4 border-b border-border flex flex-wrap gap-3 bg-base-page">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelanggan..."
                    class="input-field flex-1 min-w-40">
                <select name="kecamatan" class="input-field w-40">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatanList as $kec)
                        <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                    @endforeach
                </select>
                @role('superadmin')
                <select name="kolektor_id" class="input-field w-36">
                    <option value="">Semua Kolektor</option>
                    @foreach($kolektorList as $kol)
                        <option value="{{ $kol->id }}" {{ request('kolektor_id') == $kol->id ? 'selected' : '' }}>{{ $kol->nama_kolektor }}</option>
                    @endforeach
                </select>
                @endrole
                <select name="bulan" class="input-field w-32">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>
                <select name="tahun" class="input-field w-24">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <select name="status" class="input-field w-36">
                    <option value="">Semua Status</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas
                    </option>
                    <option value="sebagian" {{ request('status') == 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                </select>
                <button type="submit" class="btn-primary px-5">Filter</button>
                @if(request()->hasAny(['search', 'status', 'bulan', 'tahun', 'kecamatan', 'kolektor_id']))
                    <a href="{{ route('tagihan.index') }}" class="btn-secondary px-5">Reset</a>
                @endif
            </form>

            {{-- Bulk Action Bar --}}
            <div x-show="selected.length > 0" x-cloak x-transition class="bg-status-success/10 border-b border-status-success/20 px-6 py-3 flex items-center justify-between">
                <p class="text-sm text-status-success font-medium">
                    <span x-text="selected.length"></span> tagihan terpilih
                </p>
                <button type="button"
                    @click="if(confirm('Tandai ' + selected.length + ' tagihan ini sebagai LUNAS?')) submitBulk()"
                    class="btn-primary py-1.5 text-sm bg-status-success hover:bg-status-success/90">
                    Tandai Lunas
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-base-page/50">
                            <th class="px-4 py-3 text-center w-12">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" class="w-4 h-4 rounded border-border text-primary focus:ring-primary">
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Pelanggan</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Periode</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Nominal</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Jatuh Tempo</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Tgl Bayar</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-content-tertiary uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($tagihan as $item)
                            @php
                                $jt = $item->tanggal_jatuh_tempo;
                                $sisaHari = $item->sisa_hari;
                                $isTunggakan = $item->is_tunggakan;
                            @endphp
                            <tr class="hover:bg-base-page transition-colors {{ $isTunggakan ? 'bg-status-danger/5' : '' }}">
                                {{-- Checkbox per baris --}}
                                <td class="px-4 py-4 text-center">
                                    @if($item->status === 'lunas')
                                        {{-- Lunas: checkbox tercentang — uncheck = batal lunas --}}
                                        <input type="checkbox" checked
                                            class="w-4 h-4 rounded border-status-success text-status-success focus:ring-status-success cursor-pointer accent-green-500"
                                            title="Klik untuk batalkan pelunasan"
                                            onclick="
                                                event.preventDefault();
                                                if(confirm('Batalkan pelunasan tagihan {{ addslashes($item->pelanggan->nama_pelanggan) }}?')) {
                                                    document.getElementById('batal-lunas-{{ $item->id }}').submit();
                                                }
                                            ">
                                        <form id="batal-lunas-{{ $item->id }}" method="POST"
                                            action="{{ route('tagihan.batal-lunas', $item) }}" style="display:none;">
                                            @csrf
                                        </form>
                                    @else
                                        {{-- Belum lunas / sebagian: checkbox kosong untuk bulk select --}}
                                        <input type="checkbox" name="tagihan_ids[]" value="{{ $item->id }}"
                                            x-model="selected"
                                            class="w-4 h-4 rounded border-border text-primary focus:ring-primary cursor-pointer">
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($isTunggakan)
                                            <span class="w-2 h-2 rounded-full bg-status-danger flex-shrink-0"
                                                title="Tunggakan"></span>
                                        @endif
                                        <div>
                                            <p class="text-sm text-content-primary font-medium">
                                                {{ $item->pelanggan->nama_pelanggan }}
                                            </p>
                                            <p class="text-xs text-content-tertiary">{{ $item->pelanggan->desa }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-content-secondary">
                                    {{ date('M', mktime(0, 0, 0, $item->bulan, 1)) }} {{ $item->tahun }}
                                    @if($isTunggakan)
                                        <span class="ml-1 text-xs font-semibold text-status-danger">(Tunggakan)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-content-primary font-mono font-medium">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    @if($item->status === 'sebagian')
                                        <p class="text-xs text-status-warning font-sans">
                                            Terbayar: Rp {{ number_format($item->terbayar, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($item->status === 'lunas')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-status-success/20 text-status-success">✓
                                            Lunas</span>
                                    @elseif($item->status === 'sebagian')
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-status-warning/20 text-status-warning">~
                                            Sebagian</span>
                                    @else
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-semibold bg-status-danger/20 text-status-danger">✗
                                            Belum Lunas</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($jt)
                                        <div>
                                            <p class="text-content-secondary">{{ $jt->format('d/m/Y') }}</p>
                                            @if($item->status !== 'lunas')
                                                @if($sisaHari === null)
                                                    <p class="text-xs text-content-tertiary">—</p>
                                                @elseif($sisaHari > 7)
                                                    <p class="text-xs text-status-success font-medium">{{ $sisaHari }} hari lagi</p>
                                                @elseif($sisaHari >= 0)
                                                    <p class="text-xs text-status-warning font-semibold">⚡ {{ $sisaHari }} hari lagi</p>
                                                @else
                                                    <p class="text-xs text-status-danger font-semibold">{{ abs($sisaHari) }} hari lewat</p>
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-content-tertiary">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-content-secondary">
                                    {{ $item->tanggal_bayar ? $item->tanggal_bayar->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button @click="openEdit({{ $item->toJson() }})"
                                            class="p-1.5 rounded-lg bg-amber-500/10 text-amber-500 hover:bg-amber-500/20 transition-colors"
                                            title="Update Pembayaran">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @role('superadmin')
                                        <form method="POST" action="{{ route('tagihan.destroy', $item) }}" class="inline"
                                            onsubmit="return confirm('Hapus tagihan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg bg-status-danger/10 text-status-danger hover:bg-status-danger/20 transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-content-tertiary text-sm">
                                    Belum ada tagihan untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Standalone hidden bulk form (avoids nested form issue) --}}
            <form id="bulkLunasForm" method="POST" action="{{ route('tagihan.lunas-banyak') }}" style="display:none;">
                @csrf
                <div id="bulkCheckboxContainer"></div>
            </form>


            @if($tagihan->hasPages())
                <div class="px-6 py-4 border-t border-border">
                    {{ $tagihan->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Update Pembayaran --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto px-4 pt-4 pb-24" style="display: none;">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-content-primary/40 backdrop-blur-sm"
                @click="showModal = false"></div>

            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-xl shadow-modal w-full max-w-lg mx-auto z-10 overflow-hidden">

                <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-base-page">
                    <div>
                        <h3 class="text-lg font-semibold text-content-primary">Update Pembayaran</h3>
                        <p class="text-xs text-content-tertiary mt-0.5"
                            x-text="formData.pelangganNama + ' · ' + formData.periode"></p>
                    </div>
                    <button @click="showModal = false"
                        class="text-content-tertiary hover:text-content-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="formAction" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <div class="p-6 space-y-4">
                        {{-- Info nominal tagihan --}}
                        <div class="p-3 rounded-lg bg-base-page border border-border">
                            <p class="text-xs text-content-tertiary">Sisa Tagihan</p>
                            <p class="text-xl font-bold text-content-primary font-mono"
                                x-text="'Rp ' + Number(formData.nominalTagihan).toLocaleString('id-ID')"></p>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Status Pembayaran <span
                                    class="text-status-danger">*</span></label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="belum_lunas" x-model="formData.status"
                                        class="sr-only">
                                    <div :class="formData.status === 'belum_lunas' ? 'ring-2 ring-status-danger bg-status-danger/10 text-status-danger' : 'bg-base-page text-content-secondary'"
                                        class="rounded-lg border border-border p-3 text-center text-sm font-medium transition-all cursor-pointer">
                                        ✗ Belum Lunas
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="sebagian" x-model="formData.status"
                                        class="sr-only">
                                    <div :class="formData.status === 'sebagian' ? 'ring-2 ring-status-warning bg-status-warning/10 text-status-warning' : 'bg-base-page text-content-secondary'"
                                        class="rounded-lg border border-border p-3 text-center text-sm font-medium transition-all cursor-pointer">
                                        ~ Sebagian
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="lunas" x-model="formData.status"
                                        class="sr-only">
                                    <div :class="formData.status === 'lunas' ? 'ring-2 ring-status-success bg-status-success/10 text-status-success' : 'bg-base-page text-content-secondary'"
                                        class="rounded-lg border border-border p-3 text-center text-sm font-medium transition-all cursor-pointer">
                                        ✓ Lunas
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Nominal Bayar (jika lunas atau sebagian) --}}
                        <div x-show="formData.status === 'lunas' || formData.status === 'sebagian'">
                            <label class="block text-content-secondary text-sm mb-2">
                                Nominal Dibayar
                                <span x-show="formData.status === 'sebagian'" class="text-xs text-status-warning">(harus
                                    kurang dari nominal tagihan)</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-content-tertiary text-sm">Rp</span>
                                <input type="number" name="nominal_bayar" x-model="formData.nominalBayar"
                                    class="input-field pl-12" :min="1"
                                    :max="formData.status === 'sebagian' ? formData.nominalTagihan - 1 : formData.nominalTagihan"
                                    :readonly="formData.status === 'lunas'"
                                    :class="{'bg-base-page text-content-tertiary cursor-not-allowed': formData.status === 'lunas'}"
                                    placeholder="0">
                            </div>
                            <p class="text-xs text-status-danger mt-1"
                                x-show="formData.status === 'sebagian' && Number(formData.nominalBayar) >= Number(formData.nominalTagihan)">
                                Nominal sebagian harus lebih kecil dari nominal tagihan.
                            </p>
                        </div>

                        {{-- Tanggal Bayar --}}
                        <div x-show="formData.status === 'lunas' || formData.status === 'sebagian'">
                            <label class="block text-content-secondary text-sm mb-2">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" x-model="formData.tanggalBayar" class="input-field">
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label class="block text-content-secondary text-sm mb-2">Keterangan</label>
                            <textarea name="keterangan" rows="2" x-model="formData.keterangan" class="input-field"
                                placeholder="Opsional..."></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-border flex justify-end gap-3 bg-base-page">
                        <button type="button" @click="showModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    @once
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('tagihanData', () => ({
                    showModal: {{ $errors->any() ? 'true' : 'false' }},
                    formAction: '',
                    formData: {
                        pelangganNama: '',
                        periode: '',
                        status: 'belum_lunas',
                        nominalTagihan: 0,
                        nominalBayar: '',
                        tanggalBayar: '',
                        keterangan: '',
                    },
                    selected: [],
                    selectAll: false,
                    tagihanBelumLunasIds: {{ json_encode($tagihan->where('status', '!=', 'lunas')->pluck('id')) }},

                    toggleAll() {
                        if (this.selectAll) {
                            this.selected = this.tagihanBelumLunasIds.map(String);
                        } else {
                            this.selected = [];
                        }
                    },

                    init() {
                        this.$watch('formData.status', (newStatus) => {
                            if (newStatus === 'lunas') {
                                this.formData.nominalBayar = this.formData.nominalTagihan;
                            } else if (newStatus === 'belum_lunas') {
                                this.formData.nominalBayar = '';
                            }
                        });

                        this.$watch('selected', (newVal) => {
                            if (newVal.length === 0) {
                                this.selectAll = false;
                            } else if (newVal.length === this.tagihanBelumLunasIds.length && this.tagihanBelumLunasIds.length > 0) {
                                this.selectAll = true;
                            } else {
                                this.selectAll = false;
                            }
                        });
                    },

                    openEdit(item) {
                        const months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        this.formAction = '/tagihan/' + item.id;
                        const sisaTagihan = Math.max(0, item.nominal - (item.terbayar || 0));
                        this.formData = {
                            pelangganNama: item.pelanggan?.nama_pelanggan ?? '',
                            periode: months[item.bulan] + ' ' + item.tahun,
                            status: item.status,
                            nominalTagihan: sisaTagihan,
                            nominalBayar: (item.status === 'lunas') ? sisaTagihan : '',
                            tanggalBayar: item.tanggal_bayar ? item.tanggal_bayar.split('T')[0] : new Date().toISOString().split('T')[0],
                            keterangan: item.keterangan || '',
                        };
                        this.showModal = true;
                    },

                    submitBulk() {
                        const container = document.getElementById('bulkCheckboxContainer');
                        container.innerHTML = '';
                        this.selected.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'tagihan_ids[]';
                            input.value = id;
                            container.appendChild(input);
                        });
                        document.getElementById('bulkLunasForm').submit();
                    }
                }));
            });
        </script>
    @endonce
@endpush