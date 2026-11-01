import './bootstrap';

import Alpine from 'alpinejs';
import receiptPicker from './components/receipt-picker';
import shareModal from './components/share-modal';
import signaturePad from './components/signature-pad';

window.Alpine = Alpine;

Alpine.data('receiptPicker', receiptPicker);
Alpine.data('shareModal', shareModal);
Alpine.data('signaturePad', signaturePad);

Alpine.start();
