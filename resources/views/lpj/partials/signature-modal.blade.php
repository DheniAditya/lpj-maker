{{-- Modal tanda tangan. Berada di dalam x-data="signaturePad(...)" (lihat report-header). --}}
<div x-show="open" x-cloak @keydown.escape.window="open && close()" @click.self="close()"
    class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center sm:p-4 bg-slate-900/60 backdrop-blur-sm text-left">

    <div x-show="open" x-transition class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden max-h-[95vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-4 p-5 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <x-lpj.icon name="signature" />
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 leading-tight">Tanda Tangan</h3>
                    <p class="text-xs text-slate-500">Akan dicetak di atas nama {{ $report->creator_name }} pada PDF</p>
                </div>
            </div>
            <button type="button" @click="close()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600" title="Tutup">
                <x-lpj.icon name="close" />
            </button>
        </div>

        <div class="p-5 space-y-4">
            {{-- Pilihan cara input --}}
            <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 rounded-xl">
                <button type="button" @click="switchMode('draw')" :class="mode === 'draw' ? 'bg-white shadow-sm text-blue-700' : 'text-slate-500'"
                    class="inline-flex items-center justify-center gap-2 py-2 rounded-lg text-sm font-bold transition">
                    <x-lpj.icon name="signature" class="w-4 h-4" /> Tulis
                </button>
                <button type="button" @click="switchMode('upload')" :class="mode === 'upload' ? 'bg-white shadow-sm text-blue-700' : 'text-slate-500'"
                    class="inline-flex items-center justify-center gap-2 py-2 rounded-lg text-sm font-bold transition">
                    <x-lpj.icon name="image" class="w-4 h-4" /> Gambar
                </button>
            </div>

            {{-- Canvas --}}
            <div x-show="mode === 'draw'">
                <div class="relative rounded-xl border-2 border-dashed border-slate-300 bg-[repeating-linear-gradient(45deg,#f8fafc,#f8fafc_10px,#f1f5f9_10px,#f1f5f9_20px)]">
                    <canvas x-ref="canvas" class="w-full h-52 touch-none cursor-crosshair rounded-xl"
                        @pointerdown="startStroke($event)" @pointermove="moveStroke($event)"
                        @pointerup="endStroke()" @pointerleave="endStroke()" @pointercancel="endStroke()"></canvas>
                    <div x-show="isEmpty" class="pointer-events-none absolute inset-0 flex items-center justify-center text-sm text-slate-400">
                        Tanda tangan di sini
                    </div>
                    <div class="pointer-events-none absolute left-6 right-6 bottom-10 border-b border-slate-300"></div>
                </div>
                <div class="flex justify-end mt-2">
                    <button type="button" @click="clearCanvas()" :disabled="isEmpty"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-rose-600 disabled:opacity-40">
                        <x-lpj.icon name="trash" class="w-4 h-4" /> Ulangi
                    </button>
                </div>
            </div>

            {{-- Upload --}}
            <div x-show="mode === 'upload'" x-cloak>
                <input type="file" accept="image/png,image/jpeg,image/webp" class="hidden" x-ref="signatureFile" @change="pickFile($event)">

                <template x-if="!uploadPreview">
                    <button type="button" @click="$refs.signatureFile.click()"
                        class="w-full h-52 border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center gap-2 hover:bg-slate-50 hover:border-blue-400 transition text-slate-400 hover:text-blue-600">
                        <x-lpj.icon name="upload" class="w-8 h-8" />
                        <span class="text-sm font-bold">Pilih gambar tanda tangan</span>
                        <span class="text-xs">JPG / PNG / WEBP, maks 5 MB</span>
                    </button>
                </template>

                <template x-if="uploadPreview">
                    <div>
                        <div class="h-52 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center p-3">
                            <img :src="uploadPreview" class="max-h-full max-w-full object-contain" alt="Pratinjau tanda tangan">
                        </div>
                        <div class="flex justify-between mt-2">
                            <button type="button" @click="$refs.signatureFile.click()" class="text-xs font-bold text-blue-600 hover:underline">Ganti gambar</button>
                            <button type="button" @click="clearUpload()" class="text-xs font-bold text-slate-500 hover:text-rose-600">Batal</button>
                        </div>
                    </div>
                </template>

                <p class="text-xs text-slate-500 mt-3 flex gap-2">
                    <x-lpj.icon name="check" class="w-4 h-4 text-emerald-500 shrink-0" />
                    Latar putih/kertas otomatis dibuat transparan dan area kosong di sekitarnya dipotong.
                </p>
            </div>

            <p x-show="error" x-text="error" class="text-sm text-rose-600 bg-rose-50 rounded-lg px-3 py-2"></p>

            @if ($report->hasSignature())
                <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $report->signatureUrl() }}" alt="Tanda tangan saat ini" class="h-10 max-w-[140px] object-contain">
                        <span class="text-xs text-slate-500">Tanda tangan saat ini</span>
                    </div>
                    <button type="button" @click="remove()" class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:underline shrink-0">
                        <x-lpj.icon name="trash" class="w-4 h-4" /> Hapus
                    </button>
                </div>
            @endif
        </div>

        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
            <button type="button" @click="close()" class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-200">Batal</button>
            <button type="button" @click="save()" :disabled="!canSave()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <x-lpj.icon name="check" class="w-4 h-4" />
                <span x-text="saving ? 'Menyimpan...' : 'Simpan Tanda Tangan'"></span>
            </button>
        </div>
    </div>
</div>
