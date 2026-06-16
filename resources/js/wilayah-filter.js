export function registerWilayahFilter() {
    document.addEventListener('alpine:init', () => {
        Alpine.data('listFilterData', (config = {}) => ({
            filterOpen: false,
            isDesktop: window.matchMedia('(min-width: 768px)').matches,
            filterData: {
                kecamatan: config.kecamatan ?? '',
                desa: config.desa ?? '',
                dusun_id: String(config.dusun_id ?? ''),
            },
            filterDesaOptions: config.desaOptions ?? [],
            filterDusunOptions: config.dusunOptions ?? [],

            get filteredFilterDesaList() {
                const list = this.filterDesaOptions
                    .filter((item) => !this.filterData.kecamatan || item.kecamatan === this.filterData.kecamatan)
                    .map((item) => item.desa);

                return [...new Set(list)];
            },

            get filteredFilterDusunList() {
                return this.filterDusunOptions.filter((item) => {
                    if (this.filterData.kecamatan && item.kecamatan !== this.filterData.kecamatan) {
                        return false;
                    }
                    if (this.filterData.desa && item.desa !== this.filterData.desa) {
                        return false;
                    }

                    return true;
                });
            },

            init() {
                const mq = window.matchMedia('(min-width: 768px)');
                this.isDesktop = mq.matches;
                mq.addEventListener('change', (e) => {
                    this.isDesktop = e.matches;
                    if (e.matches) {
                        this.filterOpen = false;
                    }
                });

                this.$watch('filterOpen', (open) => {
                    document.body.style.overflow = open ? 'hidden' : '';
                });

                this.$watch('filterData.kecamatan', () => {
                    if (this.filterData.desa && !this.filteredFilterDesaList.includes(this.filterData.desa)) {
                        this.filterData.desa = '';
                    }
                    const hasDusun = this.filteredFilterDusunList.some(
                        (item) => String(item.id) === String(this.filterData.dusun_id)
                    );
                    if (!hasDusun) {
                        this.filterData.dusun_id = '';
                    }
                });

                this.$watch('filterData.desa', () => {
                    const hasDusun = this.filteredFilterDusunList.some(
                        (item) => String(item.id) === String(this.filterData.dusun_id)
                    );
                    if (!hasDusun) {
                        this.filterData.dusun_id = '';
                    }
                });
            },
        }));
    });
}
