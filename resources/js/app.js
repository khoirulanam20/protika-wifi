import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { registerNotificationDropdown } from './notifications';
import { registerWilayahFilter } from './wilayah-filter';

window.Alpine = Alpine;
window.Chart = Chart;

registerNotificationDropdown();
registerWilayahFilter();
Alpine.start();
