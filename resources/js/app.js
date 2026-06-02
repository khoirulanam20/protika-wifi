import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import { registerNotificationDropdown } from './notifications';

window.Alpine = Alpine;
window.Chart = Chart;

registerNotificationDropdown();
Alpine.start();
