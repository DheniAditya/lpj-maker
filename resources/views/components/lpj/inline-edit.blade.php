{{--
    Teks yang bisa diedit di tempat (judul LPJ, nama penanggung jawab).
    JS-nya ada di lpj/partials/workspace-scripts: form disubmit via AJAX lalu teks diganti
    dengan nilai `response[responseKey]`.

    Props: action, name, value, responseKey, variant ('title' | 'small')
--}}
@props(['action', 'name', 'value', 'responseKey', 'variant' => 'small'])

@php($isTitle = $variant === 'title')

<div data-inline-edit data-response-key="{{ $responseKey }}" {{ $attributes }}>
    <div data-display class="flex items-center gap-3 group">
        {{ $slot }}
        <button type="button" data-edit-open class="p-2 hover:bg-slate-100 rounded-full text-slate-400" title="Ubah">
            <x-lpj.icon name="pencil" />
        </button>
    </div>

    <form action="{{ $action }}" method="POST" class="hidden">
        @csrf
        @method('PUT')
        <div class="flex {{ $isTitle ? 'flex-col sm:flex-row' : 'flex-wrap items-center' }} gap-2">
            <input type="text" name="{{ $name }}" value="{{ $value }}" required
                class="{{ $isTitle
                    ? 'rounded-xl border-slate-300 focus:ring-slate-500 focus:border-slate-500 flex-1 shadow-sm'
                    : 'flex-1 max-w-[250px] rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-sm py-1.5 shadow-sm px-3' }}">
            <div class="flex items-center gap-1 {{ $isTitle ? 'gap-2' : '' }}">
                <button type="submit"
                    class="{{ $isTitle ? 'bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-xl' : 'bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm' }} text-white transition flex items-center justify-center gap-2">
                    <span data-label>Simpan</span>
                    <div data-spinner class="hidden animate-spin {{ $isTitle ? 'h-4 w-4' : 'h-3 w-3' }} border-2 border-white border-t-transparent rounded-full"></div>
                </button>
                <button type="button" data-edit-cancel
                    class="{{ $isTitle ? 'bg-slate-200 text-slate-700 px-4 py-2 rounded-xl hover:bg-slate-300' : 'bg-white text-slate-600 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-slate-50' }} transition">
                    Batal
                </button>
            </div>
        </div>
    </form>
</div>
