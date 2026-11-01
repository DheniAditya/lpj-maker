/**
 * Komponen Alpine untuk memilih foto nota: upload file atau ambil dari kamera.
 * Dipakai oleh form "Tambah Transaksi" dan modal "Edit Transaksi".
 *
 * Opsi:
 *  - syncInput: true  -> file disalin ke <input type="file" x-ref="fileInput"> (form submit biasa/FormData).
 *  - syncInput: false -> file hanya disimpan di state; pengirim membaca `newFiles()` & `deletedIds`.
 */
export default function receiptPicker({ syncInput = true } = {}) {
    return {
        images: [], // { id: number|null, file: File|null, preview: string }
        deletedIds: [],
        showCamera: false,
        stream: null,
        facingMode: 'user', // 'user' = kamera depan, 'environment' = kamera belakang
        isMirrored: false,

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
