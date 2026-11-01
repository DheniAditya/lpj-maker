<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-lg shadow-slate-200/50">
    <h3 class="text-lg font-bold mb-4 text-slate-700">Tambah Transaksi</h3>

    <form id="form-transaksi" action="{{ route('lpj.entry.store', $report->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        @include('lpj.partials.type-toggle', ['checked' => 'credit'])

        <div class="space-y-4">
            <div>
                <label for="description" class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Keterangan</label>
                <input type="text" name="description" id="description" required placeholder="Contoh: Beli Makan Siang"
                    class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 text-sm focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition">
            </div>

            <div>
                <label for="amount_display" class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Nominal (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-slate-400 font-bold">Rp</span>
                    <input type="text" id="amount_display" data-currency-for="amount_real" inputmode="numeric" placeholder="0" required
                        class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 pl-10 text-sm font-mono font-bold text-slate-700 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition">
                </div>
                <input type="hidden" name="amount" id="amount_real">
            </div>

            <x-lpj.receipt-picker />
        </div>

        <button type="submit" data-submit-label="Simpan Transaksi"
            class="relative flex items-center justify-center w-full mt-6 bg-blue-600 text-white p-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-500/30 transform active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
            Simpan Transaksi
        </button>
    </form>
</div>
