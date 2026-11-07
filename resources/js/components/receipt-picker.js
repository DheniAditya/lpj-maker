/**
 * Komponen Alpine untuk memilih foto nota: upload file, ambil dari kamera, atau tempel (Ctrl+V).
 * Dipakai oleh form "Tambah Transaksi" dan modal "Edit Transaksi".
 *
 * Tempel: hanya picker yang terlihat dan paling atas (modal edit bila terbuka) yang menerima gambar.
 *
 * Opsi:
 *  - syncInput: true  -> file disalin ke <input type="file" x-ref="fileInput"> (form submit biasa/FormData).
 *  - syncInput: false -> file hanya disimpan di state; pengirim membaca `newFiles()` & `deletedIds`.
 */
export default function receiptPicker({ syncInput = true } = {}) {
    return {
        images: [], // { id: number|null, file: File|null, preview: string }
        deletedIds: [],
        pasted: false, // sebentar true setelah gambar ditempel (umpan balik di UI)
        showCamera: false,
        stream: null,
        facingMode: 'environment', // default kamera belakang (memotret nota); 'user' = kamera depan
        isMirrored: false,

        init() {
            this.onPaste = (event) => this.handlePaste(event);
            window.addEventListener('paste', this.onPaste);
        },

        destroy() {
            window.removeEventListener('paste', this.onPaste);
        },

        /** Isi dengan nota yang sudah tersimpan (mode edit). */
        loadExisting(images = []) {
            this.deletedIds = [];
            this.images = images.map((img) => ({
                id: img.id,
                file: null,
                preview: `/storage/${img.image_path}`,
            }));
        },

        addImages(files) {
            Array.from(files).forEach((file) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.images.push({ id: null, file, preview: e.target.result });
                    this.syncFileInput();
                };
                reader.readAsDataURL(file);
            });
        },

        // ---------- Tempel (Ctrl+V) ----------

        /** Picker aktif = yang terlihat dan paling akhir di halaman (modal edit ada setelah form tambah). */
        isActivePicker() {
            const visible = [...document.querySelectorAll('[data-receipt-picker]')].filter((el) => el.getClientRects().length > 0);
            return visible[visible.length - 1] === this.$el;
        },

        async handlePaste(event) {
            const files = [...(event.clipboardData?.items || [])]
                .filter((item) => item.kind === 'file' && item.type.startsWith('image/'))
                .map((item) => item.getAsFile())
                .filter(Boolean);

            if (!files.length || this.showCamera || !this.isActivePicker()) return;
            event.preventDefault();

            const stamp = Date.now();
            const ready = await Promise.all(files.map((file, i) => this.normalizePasted(file, `nota_tempel_${stamp}_${i + 1}`)));
            this.addImages(ready);
            this.pasted = true;
            clearTimeout(this.pastedTimer);
            this.pastedTimer = setTimeout(() => (this.pasted = false), 1800);
        },

        /**
         * Server hanya menerima JPG/PNG maksimal 5 MB. PNG/JPG kecil dipakai apa adanya;
         * format lain (webp, gif, dll.) atau file besar diubah ke JPEG.
         */
        async normalizePasted(file, name) {
            const ok = ['image/png', 'image/jpeg'].includes(file.type) && file.size <= 4.5 * 1024 * 1024;
            if (ok) return new File([file], `${name}.${file.type === 'image/png' ? 'png' : 'jpg'}`, { type: file.type });

            const bitmap = await createImageBitmap(file);
            const scale = Math.min(1, 2400 / Math.max(bitmap.width, bitmap.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(bitmap.width * scale);
            canvas.height = Math.round(bitmap.height * scale);
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.85));
            return new File([blob], `${name}.jpg`, { type: 'image/jpeg' });
        },

        removeImage(index) {
            const [removed] = this.images.splice(index, 1);
            if (removed?.id) this.deletedIds.push(removed.id);
            this.syncFileInput();
        },

        newFiles() {
            return this.images.filter((img) => img.file).map((img) => img.file);
        },

        reset() {
            this.images = [];
            this.deletedIds = [];
            this.syncFileInput();
        },

        syncFileInput() {
            if (!syncInput || !this.$refs.fileInput) return;
            const dt = new DataTransfer();
            this.newFiles().forEach((file) => dt.items.add(file));
            this.$refs.fileInput.files = dt.files;
        },

        // ---------- Kamera ----------

        async startCamera() {
            this.showCamera = true;
            this.stopStream();

            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: this.facingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
                });
                this.$refs.videoPreview.srcObject = this.stream;
            } catch (error) {
                console.error('Gagal akses kamera:', error);
                alert('Kamera tidak ditemukan atau izin ditolak.');
                this.showCamera = false;
            }
        },

        async switchCamera() {
            this.facingMode = this.facingMode === 'user' ? 'environment' : 'user';
            this.isMirrored = this.facingMode === 'user'; // kamera depan otomatis dicerminkan
            await this.startCamera();
        },

        toggleMirror() {
            this.isMirrored = !this.isMirrored;
        },

        stopCamera() {
            this.stopStream();
            this.showCamera = false;
        },

        stopStream() {
            this.stream?.getTracks().forEach((track) => track.stop());
            this.stream = null;
        },

        takePicture() {
            const video = this.$refs.videoPreview;
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            // Resolusi asli video agar foto nota tidak buram
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            if (this.isMirrored) {
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
            }
            ctx.drawImage(video, 0, 0);

            canvas.toBlob(
                (blob) => {
                    this.addImages([new File([blob], `nota_${Date.now()}.jpg`, { type: 'image/jpeg' })]);
                    this.stopCamera();
                },
                'image/jpeg',
                0.8,
            );
        },
    };
}
