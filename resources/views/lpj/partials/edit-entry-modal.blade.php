<div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-5 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-800">Edit Transaksi</h3>
            <button type="button" data-close-modal class="text-slate-400 hover:text-rose-500 transition" title="Tutup">
                <x-lpj.icon name="close" />
            </button>
        </div>

        <form id="editModalForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            @include('lpj.partials.type-toggle', ['prefix' => 'edit_'])

            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit_created_at" class="text-[10px] font-bold text-slate-400 uppercase ml-1">Tanggal</label>
                        <input type="date" name="created_at" id="edit_created_at" required class="w-full border-slate-200 bg-slate-50 rounded-xl p-2 text-sm">
                    </div>
                    <div>
                        <label for="edit_amount_display" class="text-[10px] font-bold text-slate-400 uppercase ml-1">Nominal</label>
                        <input type="text" id="edit_amount_display" data-currency-for="edit_amount_real" inputmode="numeric" required class="w-full border-slate-200 bg-slate-50 rounded-xl p-2 text-sm font-bold">
                        <input type="hidden" name="amount" id="edit_amount_real">
                    </div>
                </div>

                <div>
                    <label for="edit_description" class="text-[10px] font-bold text-slate-400 uppercase ml-1">Keterangan</label>
                    <input type="text" name="description" id="edit_description" required class="w-full border-slate-200 bg-slate-50 rounded-xl p-2 text-sm">
                </div>

                <x-lpj.receipt-picker id="edit-receipts" class="pt-2 border-t border-slate-100" compact :sync="false"
                    @load-edit-images.window="loadExisting($event.detail)" />
            </div>

            <button type="submit" data-submit-label="Simpan Perubahan" class="w-full mt-6 bg-blue-600 text-white p-3 rounded-xl font-bold hover:bg-blue-700 transition disabled:opacity-70">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
