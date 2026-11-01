/**
 * Modal tanda tangan: dua cara input.
 *  - "draw"   : menggambar langsung di canvas (latar transparan).
 *  - "upload" : memilih file gambar; latar putih dibuat transparan di server.
 *
 * Pemakaian: x-data="signaturePad({ saveUrl, deleteUrl })"
 */
export default function signaturePad({ saveUrl, deleteUrl }) {
    return {
        open: false,
        mode: 'draw',
        saving: false,
        error: '',
        isEmpty: true,
        uploadFile: null,
        uploadPreview: null,

        // state canvas
        ctx: null,
        drawing: false,
        points: [],

        show() {
            this.open = true;
            this.error = '';
            // canvas baru punya ukuran setelah modal tampil
            this.$nextTick(() => this.setupCanvas());
        },

        close() {
            this.open = false;
        },

        switchMode(mode) {
            this.mode = mode;
            this.error = '';
            if (mode === 'draw') this.$nextTick(() => this.setupCanvas());
        },

        // ---------- Canvas ----------

        setupCanvas() {
            const canvas = this.$refs.canvas;
            if (!canvas) return;

            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const { width, height } = canvas.getBoundingClientRect();
            canvas.width = width * ratio;
            canvas.height = height * ratio;

            this.ctx = canvas.getContext('2d');
            this.ctx.scale(ratio, ratio);
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            this.ctx.strokeStyle = '#0f172a';
            this.ctx.lineWidth = 2.5;
            this.isEmpty = true;
        },

        pointerPosition(event) {
            const rect = this.$refs.canvas.getBoundingClientRect();
            return { x: event.clientX - rect.left, y: event.clientY - rect.top };
        },

        startStroke(event) {
            event.preventDefault();
            this.$refs.canvas.setPointerCapture(event.pointerId);
            this.drawing = true;
            this.points = [this.pointerPosition(event)];
            const { x, y } = this.points[0];
            this.ctx.beginPath();
            this.ctx.arc(x, y, this.ctx.lineWidth / 2, 0, Math.PI * 2);
            this.ctx.fillStyle = this.ctx.strokeStyle;
            this.ctx.fill();
            this.isEmpty = false;
        },

        moveStroke(event) {
            if (!this.drawing) return;
            event.preventDefault();

            this.points.push(this.pointerPosition(event));
            const n = this.points.length;
            if (n < 3) return;

            // Kurva halus: garis lewat titik tengah antar titik
            const [p0, p1, p2] = this.points.slice(n - 3);
            const mid1 = { x: (p0.x + p1.x) / 2, y: (p0.y + p1.y) / 2 };
            const mid2 = { x: (p1.x + p2.x) / 2, y: (p1.y + p2.y) / 2 };
            this.ctx.beginPath();
            this.ctx.moveTo(mid1.x, mid1.y);
            this.ctx.quadraticCurveTo(p1.x, p1.y, mid2.x, mid2.y);
            this.ctx.stroke();
        },

        endStroke() {
            this.drawing = false;
            this.points = [];
        },

        clearCanvas() {
            const canvas = this.$refs.canvas;
            this.ctx.clearRect(0, 0, canvas.width, canvas.height);
            this.isEmpty = true;
        },

        // ---------- Upload ----------

        pickFile(event) {
            const file = event.target.files[0];
            event.target.value = null;
            if (!file) return;

            this.uploadFile = file;
            const reader = new FileReader();
            reader.onload = (e) => (this.uploadPreview = e.target.result);
            reader.readAsDataURL(file);
        },

        clearUpload() {
            this.uploadFile = null;
            this.uploadPreview = null;
        },

        // ---------- Simpan / hapus ----------

        canSave() {
            return !this.saving && (this.mode === 'draw' ? !this.isEmpty : !!this.uploadFile);
        },

        async save() {
            if (!this.canSave()) return;

            const data = new FormData();
            if (this.mode === 'draw') {
                data.append('signature_data', this.$refs.canvas.toDataURL('image/png'));
            } else {
                data.append('signature', this.uploadFile);
            }

            await this.send(saveUrl, 'POST', data, 'Tanda tangan disimpan');
        },

        async remove() {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus tanda tangan?',
                text: 'PDF tidak bisa diunduh sampai tanda tangan ditambahkan lagi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            });
            if (isConfirmed) await this.send(deleteUrl, 'DELETE', null, 'Tanda tangan dihapus');
        },

        async send(url, method, body, successTitle) {
            this.saving = true;
            this.error = '';

            try {
                const response = await fetch(url, {
                    method,
                    body,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                });
                const json = await response.json().catch(() => ({}));

                if (!response.ok) {
                    this.error = json.errors ? Object.values(json.errors)[0][0] : json.message || 'Gagal menyimpan tanda tangan.';
                    return;
                }

                Swal.fire({ icon: 'success', title: successTitle, timer: 800, showConfirmButton: false });
                setTimeout(() => location.reload(), 900);
            } catch (e) {
                this.error = 'Koneksi bermasalah, coba lagi.';
            } finally {
                this.saving = false;
            }
        },
    };
}
