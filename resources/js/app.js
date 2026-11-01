import './bootstrap';

import Alpine from 'alpinejs';
import receiptPicker from './components/receipt-picker';

window.Alpine = Alpine;

Alpine.data('receiptPicker', receiptPicker);

Alpine.start();
