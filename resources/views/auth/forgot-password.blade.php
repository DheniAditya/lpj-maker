@php
    $adminWa = config('lpj.admin_whatsapp');
    $waText = fn (string $username) => "Assalamu'alaikum admin LPJ Maker, saya lupa password.\nUsername: ".($username ?: '-')."\nMohon dibantu reset password. Terima kasih.";
@endphp

<x-guest-layout>
    <div class="bg-white w-full max-w-lg mx-auto p-8 sm:p-10 rounded-[2.5rem] shadow-2xl relative">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Lupa password?</h2>
            <p class="text-sm text-gray-500 mt-2">
                Kirim permintaan ke admin. Admin akan membuatkan password baru dan memberitahukannya kepadamu.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm space-y-3">
                <p>{{ session('status') }}</p>
                <a href="https://wa.me/{{ $adminWa }}?text={{ rawurlencode($waText(session('sent_username', ''))) }}" target="_blank" rel="noopener"
                    class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-[#25D366] hover:bg-[#1ebe5b] text-white font-bold">
                    @include('auth.partials.whatsapp-logo') Kabari admin lewat WhatsApp
                </a>
            </div>
        @endif

        <form method="POST" action="{{ route('password.request.store') }}" class="space-y-5" x-data="{ username: @js(old('username', '')) }">
            @csrf

            <div>
                <x-input-label for="username" value="Username" class="block text-gray-600 text-sm font-bold mb-2 ml-1" />
                <x-text-input id="username" type="text" name="username" x-model="username" required autofocus autocapitalize="none" spellcheck="false"
                    placeholder="Username akun kamu"
                    class="w-full py-3.5 px-5 bg-gray-50 rounded-2xl border-gray-200 focus:ring-1 focus:ring-[#6B81A7] focus:border-[#6B81A7] focus:bg-white" />
                <x-input-error :messages="$errors->get('username')" class="mt-2 text-xs ml-1" />
            </div>

            <div>
                <x-input-label for="contact" value="Nomor WhatsApp (opsional)" class="block text-gray-600 text-sm font-bold mb-2 ml-1" />
                <x-text-input id="contact" type="text" name="contact" :value="old('contact')" inputmode="tel"
                    placeholder="Agar admin bisa mengabari kamu"
                    class="w-full py-3.5 px-5 bg-gray-50 rounded-2xl border-gray-200 focus:ring-1 focus:ring-[#6B81A7] focus:border-[#6B81A7] focus:bg-white" />
                <x-input-error :messages="$errors->get('contact')" class="mt-2 text-xs ml-1" />
            </div>

            <div>
                <x-input-label for="note" value="Catatan untuk admin (opsional)" class="block text-gray-600 text-sm font-bold mb-2 ml-1" />
                <textarea id="note" name="note" rows="2" maxlength="500" placeholder="Misal: nama lengkap / unit"
                    class="w-full py-3 px-5 bg-gray-50 rounded-2xl border-gray-200 focus:ring-1 focus:ring-[#6B81A7] focus:border-[#6B81A7] focus:bg-white text-sm">{{ old('note') }}</textarea>
                <x-input-error :messages="$errors->get('note')" class="mt-2 text-xs ml-1" />
            </div>

            <x-primary-button class="w-full justify-center py-4 text-base font-bold rounded-2xl bg-[#4A5D7B] hover:bg-[#36445a]">
                Kirim permintaan ke admin
            </x-primary-button>

            {{-- Chat langsung ke admin, username ikut terisi otomatis --}}
            <div class="relative py-1 text-center text-xs text-gray-400">
                <span class="bg-white px-3 relative z-10">atau</span>
                <span class="absolute inset-x-0 top-1/2 border-t border-gray-200"></span>
            </div>
            <a :href="'https://wa.me/{{ $adminWa }}?text=' + encodeURIComponent(@js($waText('__USERNAME__')).replace('__USERNAME__', username.trim().toLowerCase() || '-'))"
                target="_blank" rel="noopener"
                class="flex items-center justify-center gap-2 w-full py-3.5 rounded-2xl border-2 border-[#25D366] text-[#128C7E] font-bold hover:bg-[#25D366]/10 transition">
                @include('auth.partials.whatsapp-logo') Chat admin di WhatsApp
            </a>

            <p class="text-center text-sm text-gray-500">
                Sudah ingat? <a href="{{ route('login') }}" class="font-bold text-[#6B81A7] hover:underline">Kembali masuk</a>
            </p>
        </form>
    </div>
</x-guest-layout>
