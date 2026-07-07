import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.plugin(collapse);
Alpine.start();
