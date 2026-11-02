<x-guest-layout>
    <div class="bg-white w-full max-w-lg mx-auto p-8 sm:p-10 rounded-[2.5rem] shadow-2xl relative">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Lupa password?</h2>
            <p class="text-sm text-gray-500 mt-2">
                Kirim permintaan ke admin. Admin akan membuatkan password baru dan memberitahukannya kepadamu.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.request.store') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="username" value="Username" class="block text-gray-600 text-sm font-bold mb-2 ml-1" />
                <x-text-input id="username" type="text" name="username" :value="old('username')" required autofocus autocapitalize="none" spellcheck="false"
                    placeholder="Username akun kamu"
                    class="w-full py-3.5 px-5 bg-gray-50 rounded-2xl border-gray-200 focus:ring-4 focus:ring-[#6B81A7]/20 focus:border-[#6B81A7] focus:bg-white" />
                <x-input-error :messages="$errors->get('username')" class="mt-2 text-xs ml-1" />
            </div>

            <div>
                <x-input-label for="contact" value="Nomor WhatsApp (opsional)" class="block text-gray-600 text-sm font-bold mb-2 ml-1" />
                <x-text-input id="contact" type="text" name="contact" :value="old('contact')" inputmode="tel"
                    placeholder="Agar admin bisa mengabari kamu"
                    class="w-full py-3.5 px-5 bg-gray-50 rounded-2xl border-gray-200 focus:ring-4 focus:ring-[#6B81A7]/20 focus:border-[#6B81A7] focus:bg-white" />
                <x-input-error :messages="$errors->get('contact')" class="mt-2 text-xs ml-1" />
            </div>

            <div>
                <x-input-label for="note" value="Catatan untuk admin (opsional)" class="block text-gray-600 text-sm font-bold mb-2 ml-1" />
                <textarea id="note" name="note" rows="2" maxlength="500" placeholder="Misal: nama lengkap / unit"
                    class="w-full py-3 px-5 bg-gray-50 rounded-2xl border-gray-200 focus:ring-4 focus:ring-[#6B81A7]/20 focus:border-[#6B81A7] focus:bg-white text-sm">{{ old('note') }}</textarea>
                <x-input-error :messages="$errors->get('note')" class="mt-2 text-xs ml-1" />
            </div>

            <x-primary-button class="w-full justify-center py-4 text-base font-bold rounded-2xl bg-[#4A5D7B] hover:bg-[#36445a]">
                Kirim permintaan ke admin
            </x-primary-button>

            <p class="text-center text-sm text-gray-500">
                Sudah ingat? <a href="{{ route('login') }}" class="font-bold text-[#6B81A7] hover:underline">Kembali masuk</a>
            </p>
        </form>
    </div>
</x-guest-layout>
