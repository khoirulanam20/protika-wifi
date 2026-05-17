@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            .leaflet-container { z-index: 10; } 
        </style>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('locationPicker', (config = {}) => ({
                    map: null,
                    marker: null,
                    lokasi: config.initialValue || '',
                    defaultLat: -7.3195, // Tengah Jawa Tengah / Temanggung default
                    defaultLng: 110.1770,

                    initMap() {
                        // Jika berada di dalam modal yang menggunakan formData
                        if (typeof this.formData !== 'undefined') {
                            this.$watch('formData.lokasi', (val) => {
                                if (this.lokasi !== val) {
                                    this.lokasi = val;
                                    this.updateMarkerFromForm();
                                }
                            });
                            this.$watch('lokasi', (val) => {
                                if (this.formData.lokasi !== val) {
                                    this.formData.lokasi = val;
                                }
                            });
                            this.lokasi = this.formData.lokasi;
                        }

                        // Menunggu modal ditampilkan jika ada
                        if (typeof this.showModal !== 'undefined') {
                            this.$watch('showModal', (value) => {
                                if (value) {
                                    this.$nextTick(() => {
                                        if (!this.map) {
                                            this.setupLeaflet();
                                        } else {
                                            this.map.invalidateSize();
                                            this.updateMarkerFromForm();
                                        }
                                    });
                                }
                            });
                        } else {
                            // Render langsung jika bukan di dalam modal (misal form biasa)
                            this.$nextTick(() => {
                                this.setupLeaflet();
                            });
                        }
                    },

                    setupLeaflet() {
                        let initialLat = this.defaultLat;
                        let initialLng = this.defaultLng;
                        
                        if (this.lokasi) {
                            const parts = this.lokasi.split(',');
                            if (parts.length === 2) {
                                initialLat = parseFloat(parts[0].trim());
                                initialLng = parseFloat(parts[1].trim());
                            }
                        }

                        this.map = L.map(this.$refs.mapContainer).setView([initialLat, initialLng], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(this.map);

                        this.marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(this.map);

                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.lokasi = `${pos.lat.toFixed(6)}, ${pos.lng.toFixed(6)}`;
                        });

                        this.map.on('click', (e) => {
                            this.marker.setLatLng(e.latlng);
                            this.lokasi = `${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;
                        });
                    },

                    updateMarkerFromForm() {
                        if (!this.map || !this.marker) return;
                        
                        if (this.lokasi) {
                            const parts = this.lokasi.split(',');
                            if (parts.length === 2) {
                                const lat = parseFloat(parts[0].trim());
                                const lng = parseFloat(parts[1].trim());
                                if (!isNaN(lat) && !isNaN(lng)) {
                                    this.marker.setLatLng([lat, lng]);
                                    this.map.setView([lat, lng], 13);
                                    return;
                                }
                            }
                        }
                        // Default if empty
                        this.marker.setLatLng([this.defaultLat, this.defaultLng]);
                        this.map.setView([this.defaultLat, this.defaultLng], 13);
                    }
                }));
            });
        </script>
    @endpush
@endonce

<div x-data="locationPicker({ initialValue: '{{ $lokasiValue ?? '' }}' })" x-init="initMap()" class="w-full">
    <label class="block text-content-secondary text-sm mb-2">Tag Lokasi Maps (Koordinat)</label>
    <div class="flex gap-2 mb-2">
        <input type="text" name="lokasi" x-model="lokasi" class="input-field bg-base-page" placeholder="Klik pada peta atau geser marker" @change="updateMarkerFromForm()" readonly>
        <button type="button" @click="lokasi = ''; updateMarkerFromForm()" class="btn-secondary px-3 py-2 flex-shrink-0" title="Reset Lokasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <div x-ref="mapContainer" class="w-full rounded-lg border border-border" style="height: 350px; min-height: 350px;"></div>
</div>
