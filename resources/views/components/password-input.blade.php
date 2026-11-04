@props(['wrapperClass' => ''])
{{-- Input password dengan tombol "lihat" (ikon mata) untuk mengecek salah ketik --}}
<div x-data="{ show: false }" class="relative {{ $wrapperClass }}">
    <x-text-input {{ $attributes->class('pr-11') }} type="password" x-bind:type="show ? 'text' : 'password'" />
    <button type="button" @click="show = !show"
            class="absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400 hover:text-[#0B6BD3] focus:outline-none focus-visible:text-[#0B6BD3]"
            :title="show ? 'Sembunyikan password' : 'Lihat password'"
            :aria-label="show ? 'Sembunyikan password' : 'Lihat password'" :aria-pressed="show.toString()">
        <x-lpj.icon name="eye" class="w-5 h-5" x-show="!show" />
        <x-lpj.icon name="eye-slash" class="w-5 h-5" x-show="show" x-cloak />
    </button>
</div>
