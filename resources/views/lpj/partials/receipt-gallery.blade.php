{{-- Tombol jumlah nota + modal galeri untuk satu transaksi. --}}
@php($imageCount = $entry->images->count())

<div x-data="{ open: false }">
    <button type="button" @click="open = true"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full transition shadow-sm border bg-blue-600 text-white border-blue-700 hover:bg-blue-800">
        <x-lpj.icon name="image" class="w-4 h-4" />
        @if ($imageCount > 1)
            <span class="text-xs font-bold">{{ $imageCount }}</span>
        @endif
    </button>

    {{-- Teleport ke <body> agar modal berada di atas semua layer --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak x-transition.opacity.duration.300ms @click="open = false" @keydown.escape.window="open = false"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden border border-white/20" @click.stop>
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Nota</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ $no }}. {{ $entry->description }} • {{ $imageCount }} File</p>
                    </div>
                    <button type="button" @click="open = false" class="p-2 rounded-full hover:bg-slate-100 text-slate-400 transition">
                        <x-lpj.icon name="close" />
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[70vh] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-slate-50">
                    @foreach ($entry->images as $image)
                        <div class="relative group aspect-[3/4] rounded-2xl border-4 border-white shadow-sm overflow-hidden bg-white">
                            <img src="{{ $image->url() }}" alt="Nota {{ $loop->iteration }}" loading="lazy"
                                class="w-full h-full object-contain bg-slate-100 group-hover:scale-105 transition duration-500">
                            <a href="{{ $image->url() }}" target="_blank"
                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-300">
                                <div class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-full border border-white/30 text-white font-bold text-xs uppercase tracking-widest">Buka File</div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </template>
</div>
