import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import 'trix';
import 'trix/dist/trix.css';

// Rich text is sanitised server-side; block file attachments in the editor.
document.addEventListener('trix-file-accept', (e) => e.preventDefault());

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();
