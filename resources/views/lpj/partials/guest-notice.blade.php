<div x-data="{ open: false }" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl overflow-hidden shadow-sm">
    <button @click="open = !open" class="w-full flex items-center justify-between p-4 bg-yellow-100 hover:bg-yellow-200 transition-colors text-left text-yellow-800 focus:outline-none">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-yellow-600 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
                <span class="font-bold text-sm block leading-tight">Mode Tamu</span>
                <span class="text-xs opacity-80">Anda belum memiliki fitur <span class="font-bold">History</span></span>
            </div>
        </div>
        <svg class="w-5 h-5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0"
        class="p-4 border-t border-yellow-200 text-sm text-yellow-800 bg-yellow-50">
        <p>Anda berada di mode tamu. Riwayat pekerjaan anda akan terhapus otomatis setelah halaman ini di tutup. Silahkan download LPJ anda sebelum menutup halaman.</p>
        <div class="mt-3 flex flex-wrap items-center gap-1">
            <a href="{{ route('register') }}" class="text-blue-600 font-bold underline hover:text-blue-800">Daftar Akun</a>
            <span class="text-gray-500 mx-1">atau</span>
            <a href="{{ route('login') }}" class="text-blue-600 font-bold underline hover:text-blue-800">Login</a>
            <span class="ml-1">untuk menyimpan riwayat pekerjaan anda.</span>
        </div>
    </div>
</div>
