@php($p = $perbaikan ?? null)
<div class="space-y-5">
    <div>
        <label class="block text-content-secondary text-sm mb-2">Tanggal & Waktu Perbaikan</label>
        <input type="datetime-local" name="tanggal_perbaikan" class="input-field" required
               value="{{ old('tanggal_perbaikan', ($p?->tanggal_perbaikan ?? now())->format('Y-m-d\TH:i')) }}">
        @error('tanggal_perbaikan')<p class="text-status-danger text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div x-data="{
            items: @js($pelanggan->map(fn ($x) => ['id' => $x->id, 'nama' => $x->nama_pelanggan, 'desa' => $x->desa])->values()),
            selected: {{ (int) old('pelanggan_id', $p?->pelanggan_id) ?: 'null' }},
            q: '',
            open: false,
            get current() { return this.items.find(i => i.id === this.selected) },
            get results() {
                const q = this.q.toLowerCase().trim();
                return (q ? this.items.filter(i => (i.nama + ' ' + (i.desa || '')).toLowerCase().includes(q)) : this.items).slice(0, 50);
            },
            pick(i) { this.selected = i ? i.id : null; this.q = ''; this.open = false; }
        }" @click.away="open = false" class="relative">
        <label class="block text-content-secondary text-sm mb-2">Pelanggan (Opsional)</label>
        <input type="hidden" name="pelanggan_id" :value="selected ?? ''">

        <button type="button" @click="open = !open; if (open) $nextTick(() => $refs.search.focus())"
                class="input-field text-left flex items-center justify-between">
            <span x-text="current ? current.nama + (current.desa ? ' — ' + current.desa : '') : '— Tidak terkait pelanggan —'"
                  :class="current ? '' : 'text-content-tertiary'"></span>
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="open" x-transition.opacity style="display:none"
             class="absolute left-0 right-0 mt-1 bg-white border border-border rounded-lg shadow-xl z-30">
            <div class="p-2 border-b border-border">
                <input type="text" x-ref="search" x-model="q" @keydown.escape="open = false" @keydown.enter.prevent="if (results.length) pick(results[0])"
                       class="input-field" placeholder="Cari nama pelanggan atau desa..." autocomplete="off">
            </div>
            <ul class="max-h-60 overflow-y-auto">
                <li><button type="button" @click="pick(null)" class="w-full text-left px-4 py-2 text-sm text-content-tertiary hover:bg-base-page">— Tidak terkait pelanggan —</button></li>
                <template x-for="i in results" :key="i.id">
                    <li>
                        <button type="button" @click="pick(i)" class="w-full text-left px-4 py-2 hover:bg-base-page border-t border-border">
                            <span class="block text-sm text-content-primary" x-text="i.nama"></span>
                            <span class="block text-xs text-content-secondary" x-text="i.desa" x-show="i.desa"></span>
                        </button>
                    </li>
                </template>
                <li x-show="results.length === 0" class="px-4 py-3 text-sm text-center text-content-tertiary">Pelanggan tidak ditemukan</li>
            </ul>
        </div>
        @error('pelanggan_id')<p class="text-status-danger text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    @include('layouts.components.location-picker', ['lokasiValue' => old('lokasi', $p?->lokasi ?? ''), 'gps' => true])
    @error('lokasi')<p class="text-status-danger text-xs -mt-3">{{ $message }}</p>@enderror

    <div>
        <label class="block text-content-secondary text-sm mb-2">Alamat / Patokan Lokasi</label>
        <input type="text" name="alamat" class="input-field" maxlength="255" value="{{ old('alamat', $p?->alamat) }}" placeholder="Contoh: RT 02/RW 01, depan masjid">
        @error('alamat')<p class="text-status-danger text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-content-secondary text-sm mb-2">Deskripsi Perbaikan</label>
        <textarea name="deskripsi" rows="4" class="input-field" required placeholder="Apa yang diperbaiki?">{{ old('deskripsi', $p?->deskripsi) }}</textarea>
        @error('deskripsi')<p class="text-status-danger text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-content-secondary text-sm mb-2">Peralatan</label>
        <textarea name="peralatan" rows="3" class="input-field" placeholder="Alat/bahan yang dipakai (opsional)">{{ old('peralatan', $p?->peralatan) }}</textarea>
        @error('peralatan')<p class="text-status-danger text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div x-data="{ preview: '{{ $p?->foto_url }}' }">
        <label class="block text-content-secondary text-sm mb-2">Foto Bukti (jpg/png/webp, maks 5MB)</label>
        <input type="file" name="foto" accept="image/*" capture="environment" class="input-field"
               @change="const f = $event.target.files[0]; if (f) preview = URL.createObjectURL(f)">
        <img x-show="preview" :src="preview" class="mt-3 rounded-lg border border-border" alt="Preview foto" style="display:none;max-width:100%;max-height:16rem">
        @error('foto')<p class="text-status-danger text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>
