<script>
    $(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // ---------- Helper ----------

        const formatRupiah = (value) => String(value ?? '').replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        const toast = (title, timer = 800) => Swal.fire({ icon: 'success', title, timer, showConfirmButton: false });

        /** Ambil pesan error dari respons Laravel (validasi 422 / 403 / 500). */
        const showError = (xhr, fallback = 'Terjadi kesalahan, coba lagi.') => {
            const json = xhr.responseJSON || {};
            const firstError = json.errors ? Object.values(json.errors)[0][0] : null;
            Swal.fire('Gagal', firstError || json.message || fallback, 'error');
        };

        /** Kunci tombol submit selama request berjalan. */
        const setBusy = ($btn, busy) => {
            $btn.prop('disabled', busy).text(busy ? 'Menyimpan...' : $btn.data('submit-label'));
        };

        const postForm = (form, formData) => $.ajax({
            url: form.action,
            method: 'POST', // PUT/PATCH dikirim lewat field _method
            data: formData,
            processData: false,
            contentType: false,
        });

        // Input nominal: tampil "1.250.000", nilai asli "1250000" disimpan di input hidden.
        $('[data-currency-for]').on('input', function () {
            const raw = this.value.replace(/\D/g, '');
            $('#' + $(this).data('currency-for')).val(raw);
            this.value = formatRupiah(raw);
        });

        // ---------- Tambah transaksi ----------

        $('#form-transaksi').on('submit', function (e) {
            e.preventDefault();
            const $btn = $(this).find('[type=submit]');
            setBusy($btn, true);

            postForm(this, new FormData(this))
                .done((res) => {
                    toast(res.message || 'Berhasil!');
                    setTimeout(() => location.reload(), 1000);
                })
                .fail((xhr) => { showError(xhr); setBusy($btn, false); });
        });

        // ---------- Edit transaksi ----------

        const $modal = $('#editModal');
        const editPicker = () => Alpine.$data(document.getElementById('edit-receipts'));

        $(document).on('click', '.btn-edit-entry', function () {
            const entry = $(this).data('entry'); // jQuery otomatis mem-parse JSON
            const form = document.getElementById('editModalForm');

            form.action = $(this).data('url');
            $('#edit_description').val(entry.description);
            $('#edit_amount_real').val(entry.amount);
            $('#edit_amount_display').val(formatRupiah(entry.amount));
            $('#edit_' + entry.type).prop('checked', true);

            window.dispatchEvent(new CustomEvent('load-edit-images', { detail: entry.images }));
            $modal.removeClass('hidden');
        });

        $modal.on('click', '[data-close-modal]', () => $modal.addClass('hidden'));

        $('#editModalForm').on('submit', function (e) {
            e.preventDefault();
            const $btn = $(this).find('[type=submit]');
            const picker = editPicker();
            const formData = new FormData(this);

            // Foto dikelola Alpine: kirim hanya file baru + id foto lama yang dihapus.
            formData.delete('images[]');
            picker.newFiles().forEach((file) => formData.append('images[]', file));
            picker.deletedIds.forEach((id) => formData.append('deleted_images[]', id));

            setBusy($btn, true);
            postForm(this, formData)
                .done(() => {
                    toast('Berhasil Update!');
                    setTimeout(() => location.reload(), 1000);
                })
                .fail((xhr) => { showError(xhr, 'Gagal memperbarui data'); setBusy($btn, false); });
        });

        // ---------- Hapus transaksi ----------

        $(document).on('click', '.btn-delete-entry', function () {
            const $btn = $(this);

            Swal.fire({ title: 'Hapus?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus!' })
                .then(({ isConfirmed }) => {
                    if (!isConfirmed) return;

                    $.ajax({ url: $btn.data('url'), type: 'DELETE' })
                        .done(() => {
                            toast('Dihapus!', 500);
                            $($btn.data('row')).fadeOut();
                        })
                        .fail((xhr) => showError(xhr));
                });
        });

        // ---------- Ubah judul / penanggung jawab di tempat ----------

        $('[data-inline-edit]').each(function () {
            const $root = $(this);
            const $display = $root.find('[data-display]');
            const $form = $root.find('form');
            const $btn = $form.find('[type=submit]');

            const toggle = (editing) => {
                $display.toggleClass('hidden', editing);
                $form.toggleClass('hidden', !editing);
                if (editing) $form.find('input[type=text]').trigger('focus');
            };

            $root.on('click', '[data-edit-open]', () => toggle(true));
            $root.on('click', '[data-edit-cancel]', () => toggle(false));

            $form.on('submit', function (e) {
                e.preventDefault();
                $btn.prop('disabled', true).find('[data-spinner]').removeClass('hidden');

                $.post(this.action, $form.serialize())
                    .done((res) => {
                        toast('Berhasil!');
                        $root.find('[data-value]').text(res[$root.data('response-key')]);
                        toggle(false);
                    })
                    .fail((xhr) => showError(xhr))
                    .always(() => $btn.prop('disabled', false).find('[data-spinner]').addClass('hidden'));
            });
        });
    });
</script>
