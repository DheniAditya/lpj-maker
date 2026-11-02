{{--
    Input "Bukti Transaksi": tombol Upload & Kamera, pratinjau foto, dan layar kamera.

    Props:
      - compact : tombol lebih pendek (untuk modal edit)
      - sync    : salin file ke <input name="images[]"> (true untuk form tambah)
    Atribut lain (mis. @load-edit-images.window) diteruskan ke elemen x-data.
--}}
@props(['compact' => false, 'sync' => true])

<div x-data="receiptPicker({ syncInput: {{ $sync ? 'true' : 'false' }} })" {{ $attributes }}>
    <label class="block text-xs font-bold text-slate-400 uppercase mb-2 ml-1">Bukti Transaksi</label>

    <input type="file" name="images[]" multiple accept="image/*" class="hidden" x-ref="fileInput"
        @change="addImages($event.target.files); {{ $sync ? '' : '$event.target.value = null' }}">

    <div class="grid grid-cols-2 gap-3 mb-4">
        @foreach ([['upload', 'Upload', '$refs.fileInput.click()'], ['camera', 'Kamera', 'startCamera()']] as [$icon, $label, $action])
            <button type="button" @click="{{ $action }}"
                class="{{ $compact ? 'h-[72px]' : 'h-[88px]' }} border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center hover:bg-slate-50 hover:border-blue-400 transition group">
                <x-lpj.icon :name="$icon" stroke-width="1.8" class="{{ $compact ? 'w-7 h-7' : 'w-8 h-8' }} text-slate-400 group-hover:text-blue-500 mb-1" />
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-blue-600">{{ $label }}</span>
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-3 gap-2" x-show="images.length > 0">
        <template x-for="(img, index) in images" :key="index">
            <div class="relative aspect-square">
                <div class="w-full h-full rounded-xl border-[3px] border-slate-700 overflow-hidden bg-slate-800">
                    <img :src="img.preview" class="w-full h-full object-cover" alt="Pratinjau nota">
                </div>
                <button type="button" @click="removeImage(index)" title="Hapus foto"
                    class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 transition">
                    <x-lpj.icon name="close" stroke-width="3" class="w-3 h-3" />
                </button>
            </div>
        </template>
    </div>

    <x-lpj.camera-modal />
</div>
