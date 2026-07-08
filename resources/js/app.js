import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';

// ── SweetAlert2 ───────────────────────────────────────────────────
window.Swal = Swal;

// ── Alpine.js ─────────────────────────────────────────────────────
window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();

