{{--
    Layar kamera untuk memotret nota.
    Harus berada di dalam elemen x-data="receiptPicker(...)" (state showCamera, isMirrored, dll.).
--}}
<div x-show="showCamera" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center bg-transparent backdrop-blur-sm p-0 sm:p-4">
    <div class="relative bg-black overflow-hidden shadow-2xl w-full max-w-md h-full sm:h-[80vh] sm:rounded-3xl flex flex-col">

        <div class="relative flex-1 bg-slate-900 flex items-center justify-center overflow-hidden">
            <video x-ref="videoPreview" autoplay playsinline :class="{ 'scale-x-[-1]': isMirrored }" class="w-full h-full object-cover"></video>

            <button type="button" @click="stopCamera()" title="Tutup Kamera"
                class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center bg-black/20 backdrop-blur-md text-white rounded-full hover:bg-red-500 transition-all duration-300 z-10">
                <x-lpj.icon name="close" class="h-6 w-6" />
            </button>
        </div>

        <div class="absolute bottom-0 left-0 right-0 p-8 pb-12 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
            <div class="flex items-center justify-around gap-4">
                <button type="button" @click="toggleMirror()" title="Mirror Mode"
                    class="flex items-center justify-center w-12 h-12 bg-white/10 backdrop-blur-xl border border-white/20 text-white rounded-2xl hover:bg-white/20 transition-all group">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="fill-white/20 group-hover:fill-white/40 transition-colors" d="M8 5l-6 14h6V5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 5l6 14h-6V5z" />
                    </svg>
                </button>

                <button type="button" @click="takePicture()" title="Ambil Foto"
                    class="relative flex items-center justify-center w-20 h-20 bg-white rounded-full p-1 shadow-2xl transform active:scale-90 transition-all duration-150">
                    <div class="w-full h-full rounded-full border-4 border-black/10 flex items-center justify-center">
                        <div class="w-14 h-14 bg-white rounded-full border-2 border-slate-200"></div>
                    </div>
                </button>

                <button type="button" @click="switchCamera()" title="Ganti Kamera"
                    class="flex items-center justify-center w-12 h-12 bg-white/10 backdrop-blur-xl border border-white/20 text-white rounded-2xl hover:bg-white/20 transition-all">
                    <x-lpj.icon name="switch-camera" class="h-6 w-6" />
                </button>
            </div>
        </div>
    </div>
</div>
