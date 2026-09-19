import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import './site';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();
