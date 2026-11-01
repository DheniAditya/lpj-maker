{{-- Modal "Bagikan LPJ". Dibuka dengan event open-share-modal (lihat resources/js/components/share-modal.js). --}}
<div x-data="shareModal()" x-show="isOpen" x-cloak @open-share-modal.window="open($event.detail)" @keydown.escape.window="isOpen && close()"
    @click.self="close()" class="fixed inset-0 z-[99] flex items-end sm:items-center justify-center sm:p-4 bg-slate-900/60 backdrop-blur-sm">

    <div x-show="isOpen" x-transition
        class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden border border-slate-200 max-h-[92vh] overflow-y-auto">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 p-5 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <x-lpj.icon name="share" />
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-slate-800 leading-tight">Bagikan LPJ</h3>
                    <p class="text-xs text-slate-500 truncate" x-text="title"></p>
                </div>
            </div>
            <button type="button" @click="close()" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600" title="Tutup">
                <x-lpj.icon name="close" />
            </button>
        </div>

        <div class="p-5 space-y-5">
            {{-- Status link --}}
            <div class="flex items-center justify-between gap-4 p-4 rounded-xl border"
                :class="isShared ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                <div>
                    <span class="block font-bold text-sm" :class="isShared ? 'text-emerald-700' : 'text-slate-700'"
                        x-text="isShared ? 'Link aktif' : 'Link nonaktif'"></span>
                    <span class="text-xs text-slate-500"
                        x-text="isShared ? 'Siapa pun yang punya link bisa membuka LPJ ini.' : 'Hanya kamu yang bisa membuka LPJ ini.'"></span>
                </div>
                <button type="button" @click="toggleSharing()" :disabled="saving" role="switch" :aria-checked="isShared"
                    :class="isShared ? 'bg-emerald-500' : 'bg-slate-300'"
                    class="relative inline-flex h-7 w-12 shrink-0 items-center rounded-full transition-colors disabled:opacity-60">
                    <span :class="isShared ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform"></span>
                </button>
            </div>

            <template x-if="isShared">
                <div class="space-y-5">
                    {{-- Izin akses --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-2">Izin akses</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ([
                                'viewer' => ['eye', 'Pelihat', 'Hanya melihat & unduh PDF'],
                                'editor' => ['pencil', 'Editor', 'Bisa tambah/ubah transaksi'],
                            ] as $value => [$icon, $label, $hint])
                                <button type="button" @click="setRole('{{ $value }}')" :disabled="saving"
                                    :class="role === '{{ $value }}' ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20' : 'border-slate-200 hover:border-slate-300'"
                                    class="text-left p-3 rounded-xl border-2 transition">
                                    <span class="flex items-center gap-2 font-bold text-sm" :class="role === '{{ $value }}' ? 'text-blue-700' : 'text-slate-700'">
                                        <x-lpj.icon :name="$icon" class="w-4 h-4" /> {{ $label }}
                                    </span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">{{ $hint }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Link --}}
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-2">Link</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1 min-w-0">
                                <x-lpj.icon name="link" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                                <input x-ref="linkInput" type="text" readonly :value="shareUrl" @focus="$el.select()"
                                    class="w-full bg-slate-50 border-slate-200 text-slate-600 text-sm rounded-xl py-2.5 pl-9 pr-3 focus:outline-none focus:border-blue-400 focus:ring-0">
                            </div>
                            <button type="button" @click="copyLink()"
                                class="shrink-0 inline-flex items-center gap-1.5 px-4 rounded-xl text-sm font-bold text-white transition"
                                :class="copied ? 'bg-emerald-500' : 'bg-blue-600 hover:bg-blue-700'">
                                <x-lpj.icon name="copy" class="w-4 h-4" x-show="!copied" />
                                <x-lpj.icon name="check" class="w-4 h-4" x-show="copied" />
                                <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <a :href="whatsappUrl" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center gap-1.5 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                <x-lpj.icon name="chat" class="w-4 h-4" /> Kirim WhatsApp
                            </a>
                            <a :href="shareUrl" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center gap-1.5 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                <x-lpj.icon name="external" class="w-4 h-4" /> Buka link
                            </a>
                        </div>
                    </div>

                    {{-- Batalkan --}}
                    <div class="pt-4 border-t border-slate-100">
                        <button type="button" @click="revoke()" :disabled="saving"
                            class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition disabled:opacity-60">
                            <x-lpj.icon name="link-slash" class="w-4 h-4" /> Batalkan link share
                        </button>
                        <p class="text-[11px] text-slate-400 text-center mt-2">Link lama langsung mati untuk semua orang yang sudah menerimanya.</p>
                    </div>
                </div>
            </template>
        </div>

        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-400" x-show="saving">Menyimpan...</span>
            <span class="text-xs text-slate-400" x-show="!saving">Perubahan tersimpan otomatis</span>
            <button type="button" @click="close()" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-sm font-bold hover:bg-slate-700">Selesai</button>
        </div>
    </div>
</div>
