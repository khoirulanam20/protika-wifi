<div class="grid grid-cols-1 md:grid-cols-2 gap-4" 
     x-data="wilayahData(formData.kecamatan, formData.desa)"
     x-init="initWilayah()">
    
    <div>
        <label class="block text-content-secondary text-sm mb-2">Provinsi</label>
        <select class="input-field" x-model="selectedProv" @change="fetchKab()">
            <option value="">Pilih Provinsi</option>
            <template x-for="p in provinces" :key="p.id">
                <option :value="p.id" x-text="p.name"></option>
            </template>
        </select>
    </div>

    <div>
        <label class="block text-content-secondary text-sm mb-2">Kabupaten/Kota</label>
        <select class="input-field" x-model="selectedKab" @change="fetchKec()" :disabled="!selectedProv">
            <option value="">Pilih Kabupaten/Kota</option>
            <template x-for="k in kabupatens" :key="k.id">
                <option :value="k.id" x-text="k.name"></option>
            </template>
        </select>
    </div>

    <div>
        <label class="block text-content-secondary text-sm mb-2">Kecamatan</label>
        <select class="input-field" x-model="selectedKec" @change="fetchDesa()" :disabled="!selectedKab">
            <option value="">Pilih Kecamatan</option>
            <template x-for="kc in kecamatans" :key="kc.id">
                <option :value="kc.id" x-text="kc.name"></option>
            </template>
        </select>
    </div>

    <div>
        <label class="block text-content-secondary text-sm mb-2">Desa/Kelurahan</label>
        <select class="input-field" x-model="selectedDesa" @change="updateHidden()" :disabled="!selectedKec">
            <option value="">Pilih Desa/Kelurahan</option>
            <template x-for="d in desas" :key="d.id">
                <option :value="d.id" x-text="d.name"></option>
            </template>
        </select>
    </div>

    {{-- Hidden Inputs for form submission --}}
    <input type="hidden" name="kecamatan" :value="kecName || initialKecamatan">
    <input type="hidden" name="desa" :value="desaName || initialDesa">
</div>

@push('scripts')
@once
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('wilayahData', (initKec = '', initDesa = '') => ({
        provinces: [], kabupatens: [], kecamatans: [], desas: [],
        selectedProv: '', selectedKab: '', selectedKec: '', selectedDesa: '',
        kecName: '', desaName: '',
        initialKecamatan: initKec, initialDesa: initDesa,

        async initWilayah() {
            this.$watch('formData', (val) => {
                if (val) {
                    if (val.kecamatan !== this.initialKecamatan) {
                        this.initialKecamatan = val.kecamatan;
                    }
                    if (val.desa !== this.initialDesa) {
                        this.initialDesa = val.desa;
                    }
                }
            });
            
            this.$watch('showModal', (val) => {
                if (val) {
                    this.$nextTick(() => {
                        // Ensure we have the latest from formData if it was replaced entirely
                        if (this.formData) {
                            this.initialKecamatan = this.formData.kecamatan;
                            this.initialDesa = this.formData.desa;
                        }
                        
                        if (this.mode === 'create') this.resetAll();
                        else if (this.mode === 'edit') this.setupEditData();
                    });
                }
            });

            try {
                const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                this.provinces = await res.json();
                
                if (this.initialKecamatan) {
                    this.setupEditData();
                }
            } catch (e) { console.error('Error fetching provinces', e); }
        },

        async setupEditData() {
            if (!this.initialKecamatan || !this.initialDesa) return;
            
            // Smart Fast-Path for Temanggung (ID: 3323) in Jawa Tengah (ID: 33)
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/3323.json`);
                const districts = await res.json();
                const dist = districts.find(d => d.name.toUpperCase() === this.initialKecamatan.toUpperCase());
                
                if (dist) {
                    this.selectedProv = '33';
                    await this.fetchKab(false); // fetch without clearing
                    this.selectedKab = '3323';
                    await this.fetchKec(false);
                    this.selectedKec = dist.id;
                    await this.fetchDesa(false);
                    
                    const v = this.desas.find(d => d.name.toUpperCase() === this.initialDesa.toUpperCase());
                    if (v) this.selectedDesa = v.id;
                    this.updateHidden();
                    return;
                }
            } catch(e) {}

            // Fallback for non-Temanggung: Use dummy options so it displays correctly
            if (!this.provinces.find(p => p.id === 'dummy_prov')) {
                this.provinces.unshift({id: 'dummy_prov', name: '(Wilayah Lain - Pilih Ulang)'});
            }
            this.kabupatens = [{id: 'dummy_kab', name: '(Wilayah Lain - Pilih Ulang)'}];
            this.kecamatans = [{id: 'dummy_kec', name: this.initialKecamatan}];
            this.desas = [{id: 'dummy_desa', name: this.initialDesa}];
            
            this.selectedProv = 'dummy_prov'; this.selectedKab = 'dummy_kab';
            this.selectedKec = 'dummy_kec'; this.selectedDesa = 'dummy_desa';
            this.updateHidden();
        },

        async fetchKab(clear = true) {
            if (clear) {
                this.kabupatens = []; this.kecamatans = []; this.desas = [];
                this.selectedKab = ''; this.selectedKec = ''; this.selectedDesa = '';
                this.updateHidden();
            }
            if (!this.selectedProv || this.selectedProv === 'dummy_prov') return;
            
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${this.selectedProv}.json`);
                this.kabupatens = await res.json();
            } catch (e) { console.error(e); }
        },

        async fetchKec(clear = true) {
            if (clear) {
                this.kecamatans = []; this.desas = [];
                this.selectedKec = ''; this.selectedDesa = '';
                this.updateHidden();
            }
            if (!this.selectedKab || this.selectedKab === 'dummy_kab') return;
            
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${this.selectedKab}.json`);
                this.kecamatans = await res.json();
            } catch (e) { console.error(e); }
        },

        async fetchDesa(clear = true) {
            if (clear) {
                this.desas = [];
                this.selectedDesa = '';
                this.updateHidden();
            }
            if (!this.selectedKec || this.selectedKec === 'dummy_kec') return;
            
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${this.selectedKec}.json`);
                this.desas = await res.json();
            } catch (e) { console.error(e); }
        },

        updateHidden() {
            const kec = this.kecamatans.find(k => k.id === this.selectedKec);
            const desa = this.desas.find(d => d.id === this.selectedDesa);
            
            this.kecName = kec ? kec.name : '';
            this.desaName = desa ? desa.name : '';
            
            if (this.selectedDesa && this.selectedDesa !== 'dummy_desa') {
                this.initialKecamatan = '';
                this.initialDesa = '';
            }

            if (this.formData) {
                this.formData.kecamatan = this.kecName || this.initialKecamatan;
                this.formData.desa = this.desaName || this.initialDesa;
            }
        },
        
        async resetAll() {
            this.kecName = ''; this.desaName = '';
            this.initialKecamatan = ''; this.initialDesa = '';
            this.selectedKec = ''; this.selectedDesa = '';
            this.desas = []; this.kecamatans = [];

            // Default ke Jawa Tengah (33) & Temanggung (3323)
            this.selectedProv = '33';
            await this.fetchKab(false);
            this.selectedKab = '3323';
            await this.fetchKec(false);
        }
    }));
});
</script>
@endonce
@endpush
