/**
 * Modal "Bagikan LPJ" (dipakai di dashboard dan halaman LPJ milik sendiri).
 * Dibuka lewat event:  window.dispatchEvent(new CustomEvent('open-share-modal', { detail }))
 * detail = { title, token, role, shared, updateUrl, revokeUrl }
 */
export default function shareModal() {
    return {
        isOpen: false,
        title: '',
        token: '',
        role: 'viewer',
        isShared: false,
        updateUrl: null,
        revokeUrl: null,
        saving: false,
        copied: false,

        get shareUrl() {
            return `${window.location.origin}/lpj/share/${this.token}`;
        },

        get whatsappUrl() {
            const text = `LPJ "${this.title}" bisa dibuka di link berikut:\n${this.shareUrl}`;
            return `https://wa.me/?text=${encodeURIComponent(text)}`;
        },

        open(detail) {
            Object.assign(this, {
                title: detail.title,
                token: detail.token,
                role: detail.role || 'viewer',
                isShared: detail.shared === true || detail.shared === 'true',
                updateUrl: detail.updateUrl,
                revokeUrl: detail.revokeUrl,
                copied: false,
                isOpen: true,
            });
        },

        close() {
            this.isOpen = false;
            // Muat ulang agar status share di kartu/tombol ikut terbarui
            if (this.dirty) location.reload();
        },

        async toggleSharing() {
            this.isShared = !this.isShared;
            await this.save();
        },

        async setRole(role) {
            if (this.role === role) return;
            this.role = role;
            await this.save();
        },

        async save() {
            const res = await this.request(this.updateUrl, { is_shared: this.isShared ? 1 : 0, share_role: this.role });
            if (res?.share_token) this.token = res.share_token;
        },

        async revoke() {
            const { isConfirmed } = await Swal.fire({
                title: 'Batalkan link share?',
                text: 'Link lama langsung tidak bisa dibuka lagi oleh siapa pun. Jika nanti dibagikan lagi, link baru akan dibuat.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'Ya, batalkan link',
                cancelButtonText: 'Kembali',
            });
            if (!isConfirmed) return;

            const res = await this.request(this.revokeUrl, {});
            if (res) {
                this.token = res.share_token;
                this.isShared = false;
                Swal.fire({ icon: 'success', title: 'Link dibatalkan', text: res.message, timer: 1800, showConfirmButton: false });
            }
        },

        async request(url, payload) {
            this.saving = true;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(payload),
                });
                const json = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(json.message || 'Gagal menyimpan pengaturan berbagi.');
                this.dirty = true;
                return json;
            } catch (error) {
                Swal.fire('Gagal', error.message, 'error');
                return null;
            } finally {
                this.saving = false;
            }
        },

        async copyLink() {
            try {
                await navigator.clipboard.writeText(this.shareUrl);
            } catch {
                this.$refs.linkInput.select();
                document.execCommand('copy');
            }
            this.copied = true;
            setTimeout(() => (this.copied = false), 2000);
        },

        dirty: false,
    };
}
