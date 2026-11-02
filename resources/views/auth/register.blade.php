@php
    // Gaya kolom isian sama dengan halaman Masuk
    $input = 'w-full py-3 px-5 bg-white rounded-xl border-slate-300 text-gray-700 placeholder-gray-400 shadow-sm transition-all focus:ring-1 focus:ring-[#6B81A7] focus:border-[#6B81A7] focus:bg-white focus:outline-none';
    $label = 'block text-gray-600 text-sm font-bold mb-2 ml-1 tracking-wide';
@endphp

<x-guest-layout>
    <div class="bg-white w-full max-w-lg mx-auto p-7 sm:p-9 rounded-3xl shadow-[0_25px_50px_-12px_rgba(0,0,0,.35)] relative">

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Buat akun baru</h2>
            <p class="text-sm text-gray-500 mt-2">Simpan riwayat LPJ kamu dan akses dari perangkat mana saja.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ show: false }">
            @csrf

            <div>
                <x-input-label for="name" value="Nama lengkap" :class="$label" />
                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                    placeholder="Contoh: Deni Aditya" :class="$input" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs ml-1" />
            </div>

            <div>
                <x-input-label for="username" value="Username" :class="$label" />
                <x-text-input id="username" type="text" name="username" :value="old('username')" required autocomplete="username"
                    autocapitalize="none" spellcheck="false" placeholder="Contoh: deni.aditya" :class="$input" />
                <p class="mt-1.5 ml-1 text-xs text-gray-400">Dipakai untuk masuk. Huruf kecil, angka, titik, garis bawah, atau strip. Tanpa spasi.</p>
                <x-input-error :messages="$errors->get('username')" class="mt-2 text-xs ml-1" />
            </div>

            <div>
                <x-input-label for="password" value="Password" :class="$label" />
                <div class="relative">
                    <x-text-input id="password" x-bind:type="show ? 'text' : 'password'" type="password" name="password" required autocomplete="new-password"
                        placeholder="Minimal 8 karakter" :class="$input.' pr-12'" />
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#6B81A7]"
                        :title="show ? 'Sembunyikan password' : 'Tampilkan password'">
                        <x-lpj.icon name="eye" class="w-5 h-5" />
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs ml-1" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Ulangi password" :class="$label" />
                <x-text-input id="password_confirmation" x-bind:type="show ? 'text' : 'password'" type="password" name="password_confirmation" required
                    autocomplete="new-password" placeholder="Ketik ulang password" :class="$input" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs ml-1" />
            </div>

            <x-primary-button class="w-full justify-center py-3 text-lg font-bold rounded-xl bg-[#0B6BD3] hover:bg-[#0950A0] shadow-lg hover:shadow-xl transition-all active:scale-[0.98] hover:-translate-y-0.5">
                Daftar
            </x-primary-button>

            <div class="text-center text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-[#0B6BD3] hover:underline hover:text-[#0950A0] transition">Masuk</a>
            </div>
        </form>
    </div>
</x-guest-layout>
